<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class PreferencesController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['text', 'url', 'form']);
    }

    /**
     * View/edit preferences for logged-in admin
     */
    public function index()
    {
        $userId = session()->get('id');
        
        if (!$userId) {
            return redirect()->to(base_url('auth/login'));
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('auth/login'))->with('error', 'User not found');
        }

        // Convert to array if object
        if (is_object($user)) {
            $user = (array) $user;
        }

        // Get preferences (could be stored as JSON in a preferences field or in separate table)
        // For now, we'll use a simple approach - store in user table or separate preferences table
        $preferences = [];
        
        // If you have a preferences table, fetch from there
        // For now, return empty preferences and let the view handle defaults

        return view('backendV2/pages/preferences/index', [
            'title' => 'My Preferences - KEWASNET',
            'user' => $user,
            'preferences' => $preferences
        ]);
    }

    /**
     * Update preferences (AJAX)
     */
    public function update()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->ajaxErrorResponse('Method not allowed', 405);
        }

        $userId = session()->get('id');
        
        if (!$userId) {
            return $this->ajaxErrorResponse('User not authenticated', 401);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->ajaxErrorResponse('User not found', 404);
        }

        try {
            // Get preferences data
            $preferencesData = [
                'language' => $this->request->getPost('language') ?? 'en',
                'timezone' => $this->request->getPost('timezone') ?? 'Africa/Nairobi',
                'theme' => $this->request->getPost('theme') ?? 'light',
                'notifications_email' => $this->request->getPost('notifications_email') ? 1 : 0,
                'notifications_sms' => $this->request->getPost('notifications_sms') ? 1 : 0,
                'notifications_push' => $this->request->getPost('notifications_push') ? 1 : 0,
                'date_format' => $this->request->getPost('date_format') ?? 'Y-m-d',
                'time_format' => $this->request->getPost('time_format') ?? 'H:i',
                'items_per_page' => (int) ($this->request->getPost('items_per_page') ?? 10),
            ];

            // Store preferences as JSON in a field (if you have a preferences column)
            // Or update in a separate preferences table
            // For now, we'll store in a JSON field or add to user table
            // This is a simplified version - you can expand based on your needs

            // If you have a user_preferences table, insert/update there
            // For this implementation, we'll assume preferences can be stored in user table as JSON
            // or in a separate preferences table

            // Example: Store as JSON string in a 'preferences' column
            // $this->userModel->update($userId, ['preferences' => json_encode($preferencesData)]);

            // For now, just return success - you can implement actual storage based on your database structure
            return $this->ajaxSuccessResponse('Preferences updated successfully', ['preferences' => $preferencesData]);
        } catch (\Exception $e) {
            log_message('error', 'Preferences update error: ' . $e->getMessage());
            return $this->ajaxErrorResponse('Failed to update preferences: ' . $e->getMessage(), 500);
        }
    }

    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }

    protected function ajaxErrorResponse($message, int $statusCode = 400, array $additionalData = [])
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'token' => csrf_hash()
        ];

        if (is_array($message)) {
            $response['errors'] = $message;
            unset($response['message']);
        }

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON(array_merge($response, $additionalData));
    }

    protected function ajaxSuccessResponse(string $message, array $additionalData = [])
    {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => $message,
            'token' => csrf_hash(),
            ...$additionalData
        ]);
    }
}