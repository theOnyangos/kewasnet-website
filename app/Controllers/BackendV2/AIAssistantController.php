<?php

namespace App\Controllers\BackendV2;

use App\Libraries\CIAuth;
use App\Controllers\BaseController;
use App\Config\AIAgent;
use CodeIgniter\HTTP\ResponseInterface;

class AIAssistantController extends BaseController
{
    public function index()
    {
        // Check if the user is logged in
        if (!CIAuth::isLoggedIn()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }

        // Check if the user is an admin
        if (!CIAuth::isAdmin()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }
        
        $title = "AI Assistant - KEWASNET";
        
        $data = [
            'title' => $title,
        ];
        
        return view('backendV2/pages/ai-assistant/index', $data);
    }

    public function settings()
    {
        // Check if the user is logged in
        if (!CIAuth::isLoggedIn()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }

        // Check if the user is an admin
        if (!CIAuth::isAdmin()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }
        
        $aiConfig = config('AIAgent');
        $settingsModel = new \App\Models\AIAgentSettingsModel();
        
        $title = "AI Assistant Settings - KEWASNET";
        
        $data = [
            'title' => $title,
            'config' => $aiConfig,
            'settings' => $settingsModel->findAll(),
        ];
        
        return view('backendV2/pages/ai-assistant/settings', $data);
    }

    public function documentation()
    {
        // Check if the user is logged in
        if (!CIAuth::isLoggedIn()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }

        // Check if the user is an admin
        if (!CIAuth::isAdmin()) {
            CIAuth::logout();
            return redirect()->to(base_url('auth/login'));
        }
        
        $title = "AI Assistant Documentation - KEWASNET";
        
        $data = [
            'title' => $title,
        ];
        
        return view('backendV2/pages/ai-assistant/documentation', $data);
    }

    public function updateSettings()
    {
        // Check if the user is logged in
        if (!CIAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ])->setStatusCode(401);
        }

        // Check if the user is an admin
        if (!CIAuth::isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied',
            ])->setStatusCode(403);
        }
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'AJAX request required',
            ])->setStatusCode(400);
        }
        
        try {
            $input = $this->request->getJSON(true);
            $settingsModel = new \App\Models\AIAgentSettingsModel();
            
            // Update settings
            if (isset($input['settings']) && is_array($input['settings'])) {
                foreach ($input['settings'] as $key => $value) {
                    $description = $input['descriptions'][$key] ?? null;
                    $settingsModel->setSetting($key, $value, $description);
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Settings updated successfully',
            ])->setStatusCode(200);
            
        } catch (\Exception $e) {
            log_message('error', 'AI Assistant settings update error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update settings',
            ])->setStatusCode(500);
        }
    }

    /**
     * Fetch available OpenAI Assistants (Agents) for selection in settings UI
     * GET /auth/ai-assistant/openai/assistants
     */
    public function listOpenAIAssistants()
    {
        // Check if the user is logged in
        if (!CIAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // Check if the user is an admin
        if (!CIAuth::isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied',
            ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'AJAX request required',
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $config = config('AIAgent');
            if (empty($config->apiKey)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'OpenAI API key is not configured',
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $client = \OpenAI::client($config->apiKey);
            $resp = $client->assistants()->list([
                'limit' => 100,
                'order' => 'desc',
            ]);

            $data = $resp->toArray();
            $assistants = array_map(static function (array $a) {
                return [
                    'id' => $a['id'] ?? '',
                    'name' => $a['name'] ?? null,
                    'model' => $a['model'] ?? null,
                    'description' => $a['description'] ?? null,
                    'created_at' => $a['created_at'] ?? null,
                ];
            }, $data['data'] ?? []);

            return $this->response->setJSON([
                'success' => true,
                'assistants' => $assistants,
                'has_more' => $data['has_more'] ?? false,
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            log_message('error', 'OpenAI assistants list error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch OpenAI assistants',
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Fetch available OpenAI Models for selection in settings UI
     * GET /auth/ai-assistant/openai/models
     */
    public function listOpenAIModels()
    {
        // Check if the user is logged in
        if (!CIAuth::isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // Check if the user is an admin
        if (!CIAuth::isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied',
            ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'AJAX request required',
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $config = config('AIAgent');
            if (empty($config->apiKey)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'OpenAI API key is not configured',
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $client = \OpenAI::client($config->apiKey);
            $resp = $client->models()->list();
            $arr = $resp->toArray();
            $rawModels = array_map(static function (array $m) {
                return [
                    'id' => $m['id'] ?? '',
                    'owned_by' => $m['owned_by'] ?? null,
                    'created' => $m['created'] ?? null,
                ];
            }, $arr['data'] ?? []);

            // Only include models that make sense for the chat assistant UI.
            // The models list contains many modalities (audio, realtime, transcribe, embeddings, images, moderation, etc.)
            // which cannot be used as the primary chat model for this assistant.
            $excludedTokens = [
                'audio',
                'realtime',
                'transcribe',
                'tts',
                'image',
                'embedding',
                'moderation',
                'search',
                'instruct',
                'codex',
            ];

            $models = array_values(array_filter($rawModels, static function (array $m) use ($excludedTokens) {
                $id = strtolower((string) ($m['id'] ?? ''));
                if ($id === '') return false;

                // Allow well-known chat model families only
                $allowedPrefix = str_starts_with($id, 'gpt-') || $id === 'chatgpt-4o-latest';
                if (!$allowedPrefix) return false;

                foreach ($excludedTokens as $token) {
                    if (str_contains($id, $token)) {
                        return false;
                    }
                }

                return true;
            }));

            // Keep the list useful: prefer gpt* models first
            usort($models, static function ($a, $b) {
                $ag = str_starts_with((string) ($a['id'] ?? ''), 'gpt') ? 0 : 1;
                $bg = str_starts_with((string) ($b['id'] ?? ''), 'gpt') ? 0 : 1;
                if ($ag !== $bg) return $ag <=> $bg;
                return strcmp((string) ($a['id'] ?? ''), (string) ($b['id'] ?? ''));
            });

            return $this->response->setJSON([
                'success' => true,
                'models' => $models,
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            log_message('error', 'OpenAI models list error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch OpenAI models',
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
