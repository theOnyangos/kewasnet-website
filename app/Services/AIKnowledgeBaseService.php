<?php

namespace App\Services;

use App\Models\AIKbChunkModel;
use App\Models\AIKbSourceModel;
use CodeIgniter\HTTP\Exceptions\HTTPException;

class AIKnowledgeBaseService
{
    protected $sourceModel;
    protected $chunkModel;
    protected $db;

    public function __construct()
    {
        $this->sourceModel = new AIKbSourceModel();
        $this->chunkModel = new AIKbChunkModel();
        $this->db = \Config\Database::connect();
        helper(['text', 'filesystem', 'url']);
    }

    /**
     * Ingest all active sources (or all sources when $includeDisabled=true)
     */
    public function ingestAll(bool $includeDisabled = false): array
    {
        $builder = $this->sourceModel->orderBy('updated_at', 'DESC');
        if (!$includeDisabled) {
            $builder->where('status', 'active');
        }

        $sources = $builder->findAll();
        $results = [];

        foreach ($sources as $src) {
            $results[] = $this->ingestSource($src['id']);
        }

        return [
            'success' => true,
            'results' => $results,
        ];
    }

    /**
     * Ingest one source into chunks
     */
    public function ingestSource(string $sourceId): array
    {
        $source = $this->sourceModel->find($sourceId);
        if (!$source) {
            return [
                'success' => false,
                'source_id' => $sourceId,
                'error' => 'Source not found',
            ];
        }

        $now = date('Y-m-d H:i:s');

        try {
            $this->db->transBegin();

            $rawText = $this->extractSourceText($source);
            $rawText = $this->normalizeText($rawText);

            if (mb_strlen($rawText) < 50) {
                throw new \RuntimeException('Extracted text is too short to ingest.');
            }

            // Update cached raw content for url/file (and keep for text too)
            $this->sourceModel->update($sourceId, [
                'content_raw' => $rawText,
                'last_ingested_at' => $now,
                'ingest_error' => null,
            ]);

            // Replace chunks
            $this->db->table('ai_kb_chunks')->where('source_id', $sourceId)->delete();

            $chunks = $this->chunkText($rawText, 1200);
            $chunkCount = 0;

            foreach ($chunks as $idx => $chunk) {
                $metadata = [
                    'source_id' => $sourceId,
                    'source_type' => $source['type'] ?? null,
                    'title' => $source['title'] ?? '',
                    'source_url' => $source['source_url'] ?? null,
                    'file_path' => $source['file_path'] ?? null,
                    'chunk_index' => $idx,
                ];

                $ok = $this->chunkModel->insert([
                    'source_id' => $sourceId,
                    'chunk_index' => $idx,
                    'content' => $chunk,
                    'metadata' => $metadata,
                    'created_at' => $now,
                ]);

                if (!$ok) {
                    throw new \RuntimeException('Failed to insert chunk ' . $idx);
                }

                $chunkCount++;
            }

            $this->db->transCommit();

            return [
                'success' => true,
                'source_id' => $sourceId,
                'chunks' => $chunkCount,
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();

            $this->sourceModel->update($sourceId, [
                'last_ingested_at' => $now,
                'ingest_error' => $e->getMessage(),
            ]);

            log_message('error', 'AI KB ingestion failed for ' . $sourceId . ': ' . $e->getMessage());

            return [
                'success' => false,
                'source_id' => $sourceId,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function extractSourceText(array $source): string
    {
        $type = $source['type'] ?? 'text';

        if ($type === 'text') {
            return (string) ($source['content_raw'] ?? '');
        }

        if ($type === 'url') {
            $url = (string) ($source['source_url'] ?? '');
            if (empty($url)) {
                throw new \InvalidArgumentException('source_url is required for url sources.');
            }

            $client = \Config\Services::curlrequest([
                'timeout' => 15,
                'http_errors' => false,
                'headers' => [
                    'User-Agent' => 'KEWASNET-AI-KB-Ingest/1.0',
                ],
            ]);

            $resp = $client->get($url);
            $status = $resp->getStatusCode();
            if ($status < 200 || $status >= 300) {
                throw new \RuntimeException('Failed to fetch URL. HTTP ' . $status);
            }

            $body = (string) $resp->getBody();
            return $this->htmlToText($body);
        }

        if ($type === 'file') {
            $filePath = (string) ($source['file_path'] ?? '');
            if (empty($filePath)) {
                throw new \InvalidArgumentException('file_path is required for file sources.');
            }

            $abs = FCPATH . ltrim($filePath, '/');
            if (!is_file($abs)) {
                throw new \RuntimeException('File not found at ' . $abs);
            }

            $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
            if (in_array($ext, ['txt', 'md'], true)) {
                return (string) file_get_contents($abs);
            }

            if ($ext === 'pdf') {
                // Use smalot/pdfparser if installed
                if (!class_exists('\\Smalot\\PdfParser\\Parser')) {
                    throw new \RuntimeException('PDF parsing dependency missing. Please install smalot/pdfparser.');
                }

                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($abs);
                return (string) $pdf->getText();
            }

            throw new \RuntimeException('Unsupported file type: ' . $ext);
        }

        throw new \RuntimeException('Unsupported source type: ' . $type);
    }

    protected function htmlToText(string $html): string
    {
        // Remove scripts/styles
        $html = preg_replace('#<script[^>]*>.*?</script>#is', ' ', $html) ?? $html;
        $html = preg_replace('#<style[^>]*>.*?</style>#is', ' ', $html) ?? $html;

        // Replace some block tags with newlines for better chunking
        $html = preg_replace('#</(p|div|h1|h2|h3|h4|h5|h6|li|br|tr)>#i', "\n", $html) ?? $html;

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $text;
    }

    protected function normalizeText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        // Collapse excessive whitespace
        $text = preg_replace("/[ \t]+/", " ", $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
        return trim($text);
    }

    /**
     * Chunk by paragraphs up to max chars
     *
     * @return string[]
     */
    protected function chunkText(string $text, int $maxChars = 1200): array
    {
        $paragraphs = preg_split("/\n\s*\n/", $text) ?: [];
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $p) {
            $p = trim($p);
            if ($p === '') {
                continue;
            }

            $candidate = $current === '' ? $p : ($current . "\n\n" . $p);

            if (mb_strlen($candidate) <= $maxChars) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $chunks[] = $current;
                $current = '';
            }

            // If paragraph itself is huge, hard split
            while (mb_strlen($p) > $maxChars) {
                $chunks[] = mb_substr($p, 0, $maxChars);
                $p = mb_substr($p, $maxChars);
                $p = ltrim($p);
            }
            $current = $p;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        // Filter tiny chunks
        return array_values(array_filter($chunks, fn ($c) => mb_strlen(trim($c)) >= 50));
    }
}

