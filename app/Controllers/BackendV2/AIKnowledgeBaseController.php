<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Libraries\CIAuth;
use App\Models\AIKbSourceModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\AIKnowledgeBaseService;

class AIKnowledgeBaseController extends BaseController
{
    protected $sourceModel;

    public function __construct()
    {
        $this->sourceModel = new AIKbSourceModel();
        helper(['url', 'form', 'filesystem', 'text']);
    }

    protected function ensureAdmin()
    {
        if (!CIAuth::isLoggedIn()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }

        if (!CIAuth::isAdmin()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }

        return null;
    }

    public function index()
    {
        if ($resp = $this->ensureAdmin()) {
            return $resp;
        }

        $sources = $this->sourceModel->orderBy('updated_at', 'DESC')->findAll();

        return view('backendV2/pages/ai-assistant/knowledge-base/index', [
            'title' => 'AI Knowledge Base - KEWASNET',
            'sources' => $sources,
        ]);
    }

    public function create()
    {
        if ($resp = $this->ensureAdmin()) {
            return $resp;
        }

        return view('backendV2/pages/ai-assistant/knowledge-base/form', [
            'title' => 'Add Knowledge Source - KEWASNET',
            'mode' => 'create',
            'source' => null,
        ]);
    }

    public function edit($id)
    {
        if ($resp = $this->ensureAdmin()) {
            return $resp;
        }

        $source = $this->sourceModel->find($id);
        if (!$source) {
            return redirect()->to(base_url('auth/ai-assistant/knowledge-base'))
                ->with('error', 'Knowledge source not found.');
        }

        return view('backendV2/pages/ai-assistant/knowledge-base/form', [
            'title' => 'Edit Knowledge Source - KEWASNET',
            'mode' => 'edit',
            'source' => $source,
        ]);
    }

    public function store()
    {
        if ($resp = $this->ensureAdmin()) {
            return $resp;
        }

        $type = $this->request->getPost('type');
        $title = trim((string) $this->request->getPost('title'));
        $status = $this->request->getPost('status') ?? 'active';
        $sourceUrl = trim((string) $this->request->getPost('source_url'));
        $contentRaw = (string) $this->request->getPost('content_raw');

        $rules = [
            'type' => 'required|in_list[text,url,file]',
            'title' => 'required|min_length[2]|max_length[255]',
            'status' => 'required|in_list[active,disabled]',
        ];

        if ($type === 'url') {
            $rules['source_url'] = 'required|valid_url_strict';
        } elseif ($type === 'text') {
            $rules['content_raw'] = 'required|min_length[10]';
        } elseif ($type === 'file') {
            $rules['kb_file'] = 'uploaded[kb_file]|max_size[kb_file,10240]|ext_in[kb_file,pdf,txt,md]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', json_encode($this->validator->getErrors()));
        }

        $filePath = null;
        if ($type === 'file') {
            $file = $this->request->getFile('kb_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/ai-kb/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newFileName = 'ai_kb_' . uniqid() . '_' . time() . '.' . $file->getExtension();
                if ($file->move($uploadPath, $newFileName)) {
                    $filePath = 'uploads/ai-kb/' . $newFileName;
                } else {
                    return redirect()->back()->withInput()->with('error', 'Failed to upload file.');
                }
            }
        }

        $data = [
            'type' => $type,
            'title' => $title,
            'status' => $status,
            'source_url' => $type === 'url' ? $sourceUrl : null,
            'file_path' => $type === 'file' ? $filePath : null,
            'content_raw' => $type === 'text' ? $contentRaw : null,
            'created_by' => session()->get('id'),
            'ingest_error' => null,
        ];

        $id = $this->sourceModel->insert($data);
        if (!$id) {
            return redirect()->back()->withInput()->with('error', 'Failed to create knowledge source.');
        }

        return redirect()->to(base_url('auth/ai-assistant/knowledge-base'))
            ->with('success', 'Knowledge source created. You can now ingest it to make it searchable.');
    }

    public function update($id)
    {
        if ($resp = $this->ensureAdmin()) {
            return $resp;
        }

        $source = $this->sourceModel->find($id);
        if (!$source) {
            return redirect()->to(base_url('auth/ai-assistant/knowledge-base'))
                ->with('error', 'Knowledge source not found.');
        }

        $type = $this->request->getPost('type');
        $title = trim((string) $this->request->getPost('title'));
        $status = $this->request->getPost('status') ?? 'active';
        $sourceUrl = trim((string) $this->request->getPost('source_url'));
        $contentRaw = (string) $this->request->getPost('content_raw');

        $rules = [
            'type' => 'required|in_list[text,url,file]',
            'title' => 'required|min_length[2]|max_length[255]',
            'status' => 'required|in_list[active,disabled]',
        ];

        if ($type === 'url') {
            $rules['source_url'] = 'required|valid_url_strict';
        } elseif ($type === 'text') {
            $rules['content_raw'] = 'required|min_length[10]';
        } elseif ($type === 'file') {
            // Optional replacement file
            if ($this->request->getFile('kb_file') && $this->request->getFile('kb_file')->isValid()) {
                $rules['kb_file'] = 'max_size[kb_file,10240]|ext_in[kb_file,pdf,txt,md]';
            }
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', json_encode($this->validator->getErrors()));
        }

        $filePath = $source['file_path'] ?? null;
        if ($type === 'file') {
            $file = $this->request->getFile('kb_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/ai-kb/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newFileName = 'ai_kb_' . uniqid() . '_' . time() . '.' . $file->getExtension();
                if ($file->move($uploadPath, $newFileName)) {
                    $filePath = 'uploads/ai-kb/' . $newFileName;
                } else {
                    return redirect()->back()->withInput()->with('error', 'Failed to upload file.');
                }
            }
        }

        $data = [
            'type' => $type,
            'title' => $title,
            'status' => $status,
            'source_url' => $type === 'url' ? $sourceUrl : null,
            'file_path' => $type === 'file' ? $filePath : null,
            'content_raw' => $type === 'text' ? $contentRaw : null,
        ];

        if (!$this->sourceModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Failed to update knowledge source.');
        }

        return redirect()->to(base_url('auth/ai-assistant/knowledge-base'))
            ->with('success', 'Knowledge source updated.');
    }

    public function toggle($id)
    {
        if ($resp = $this->ensureAdmin()) {
            return $resp;
        }

        $source = $this->sourceModel->find($id);
        if (!$source) {
            return redirect()->to(base_url('auth/ai-assistant/knowledge-base'))
                ->with('error', 'Knowledge source not found.');
        }

        $newStatus = ($source['status'] ?? 'active') === 'active' ? 'disabled' : 'active';
        $this->sourceModel->update($id, ['status' => $newStatus]);

        return redirect()->to(base_url('auth/ai-assistant/knowledge-base'))
            ->with('success', 'Knowledge source status updated.');
    }

    public function ingest($id)
    {
        if ($resp = $this->ensureAdmin()) {
            return $resp;
        }

        $service = new AIKnowledgeBaseService();
        $result = $service->ingestSource($id);

        if ($result['success'] ?? false) {
            return redirect()->to(base_url('auth/ai-assistant/knowledge-base'))
                ->with('success', 'Ingested source successfully (chunks: ' . ($result['chunks'] ?? 0) . ').');
        }

        return redirect()->to(base_url('auth/ai-assistant/knowledge-base'))
            ->with('error', 'Ingestion failed: ' . ($result['error'] ?? 'Unknown error'));
    }
}

