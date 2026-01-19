<?php

namespace App\Commands;

use App\Services\AIKnowledgeBaseService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * AI Knowledge Base ingestion
 *
 * Usage:
 *   php spark ai:kb:ingest
 *   php spark ai:kb:ingest --id=<uuid>
 *   php spark ai:kb:ingest --includeDisabled
 */
class AIKnowledgeBaseIngest extends BaseCommand
{
    protected $group       = 'AI';
    protected $name        = 'ai:kb:ingest';
    protected $description = 'Ingest AI knowledge base sources into searchable chunks';

    public function run(array $params)
    {
        $service = new AIKnowledgeBaseService();

        $id = $this->resolveIdOption($params);
        $includeDisabled = $this->resolveFlagOption('includeDisabled', $params);

        if (!empty($id)) {
            CLI::write('Ingesting AI KB source: ' . $id, 'cyan');
            $result = $service->ingestSource($id);
            $this->renderResult($result);
            return $result['success'] ? EXIT_SUCCESS : EXIT_ERROR;
        }

        CLI::write('Ingesting AI KB sources' . ($includeDisabled ? ' (including disabled)' : ''), 'cyan');
        $res = $service->ingestAll($includeDisabled);

        foreach (($res['results'] ?? []) as $r) {
            $this->renderResult($r);
        }

        return EXIT_SUCCESS;
    }

    protected function resolveIdOption(array $params): string
    {
        // CI4 seems to support "-id value" reliably. We also accept "--id" forms for convenience.
        $idOpt = CLI::getOption('id');
        if (is_string($idOpt) && trim($idOpt) !== '') {
            return trim($idOpt);
        }

        $argv = $_SERVER['argv'] ?? [];
        foreach ($argv as $idx => $p) {
            if (strpos($p, '--id=') === 0) {
                return trim(substr($p, 5));
            }
            if ($p === '--id' && isset($argv[$idx + 1])) {
                return trim((string) $argv[$idx + 1]);
            }
            if (strpos($p, '-id=') === 0) {
                return trim(substr($p, 4));
            }
        }

        return '';
    }

    protected function resolveFlagOption(string $name, array $params): bool
    {
        if (CLI::getOption($name) !== null) {
            return true;
        }

        $argv = $_SERVER['argv'] ?? [];
        foreach ($argv as $p) {
            if ($p === "--{$name}" || $p === "-{$name}") {
                return true;
            }
            if (strpos($p, "--{$name}=") === 0 || strpos($p, "-{$name}=") === 0) {
                return true;
            }
        }

        return false;
    }

    protected function renderResult(array $result): void
    {
        $sourceId = $result['source_id'] ?? 'unknown';
        if (($result['success'] ?? false) === true) {
            CLI::write('✓ ' . $sourceId . ' (chunks: ' . ($result['chunks'] ?? 0) . ')', 'green');
        } else {
            CLI::write('✗ ' . $sourceId . ' (error: ' . ($result['error'] ?? 'unknown') . ')', 'red');
        }
    }
}

