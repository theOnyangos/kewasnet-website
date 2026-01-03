<?php

namespace App\Controllers\BackendV2;

use App\Models\Newsletter;
use App\Models\BlogNewsletter;
use App\Controllers\BaseController;
use App\Services\DataTableService;
use CodeIgniter\HTTP\ResponseInterface;

class NewsletterController extends BaseController
{
    protected const PAGE_TITLE = "Newsletter Management - KEWASNET";
    protected const CREATE_TITLE = "Create Newsletter";
    protected const SENT_TITLE = "Sent Newsletters";

    protected $newsletterModel;
    protected $subscriberModel;
    protected $dataTableService;

    public function __construct()
    {
        $this->newsletterModel = new Newsletter();
        $this->subscriberModel = new BlogNewsletter();
        $this->dataTableService = new DataTableService();
    }

    /**
     * Show create newsletter form
     */
    public function create()
    {
        $stats = $this->subscriberModel->getNewsletterStats();

        return view('backendV2/pages/blogs/create_newsletter', [
            'title' => self::CREATE_TITLE . ' - KEWASNET',
            'dashboardTitle' => self::CREATE_TITLE,
            'subscriberStats' => $stats
        ]);
    }

    /**
     * Store new newsletter
     */
    public function store()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        try {
            $data = $this->request->getPost();

            $rules = [
                'subject' => 'required|min_length[3]|max_length[255]',
                'content' => 'required',
                'preview_text' => 'permit_empty|max_length[255]',
                'sender_name' => 'permit_empty|max_length[100]',
                'sender_email' => 'permit_empty|valid_email',
            ];

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // Count active subscribers
            $recipientCount = $this->subscriberModel->where('is_active', 1)->countAllResults();

            $newsletterData = [
                'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                'subject' => $data['subject'],
                'preview_text' => $data['preview_text'] ?? null,
                'content' => $data['content'],
                'sender_name' => $data['sender_name'] ?? 'KEWASNET',
                'sender_email' => $data['sender_email'] ?? env('EMAIL_FROM_ADDRESS', 'info@kewasnet.co.ke'),
                'status' => 'draft',
                'recipient_count' => $recipientCount,
                'created_by' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->newsletterModel->insert($newsletterData);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Newsletter saved as draft successfully!',
                'data' => ['id' => $newsletterData['id']]
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            log_message('error', 'Newsletter creation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create newsletter: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Save newsletter draft (auto-save)
     */
    public function saveDraft($id = null)
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        try {
            $data = $this->request->getPost();

            if ($id) {
                // Update existing draft
                $updateData = [
                    'subject' => $data['subject'] ?? '',
                    'preview_text' => $data['preview_text'] ?? null,
                    'content' => $data['content'] ?? '',
                    'sender_name' => $data['sender_name'] ?? null,
                    'sender_email' => $data['sender_email'] ?? null,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->newsletterModel->update($id, $updateData);
                $message = 'Draft updated successfully!';
            } else {
                // Create new draft
                $recipientCount = $this->subscriberModel->where('is_active', 1)->countAllResults();

                $draftData = [
                    'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                    'subject' => $data['subject'] ?? 'Untitled Newsletter',
                    'preview_text' => $data['preview_text'] ?? null,
                    'content' => $data['content'] ?? '',
                    'sender_name' => $data['sender_name'] ?? 'KEWASNET',
                    'sender_email' => $data['sender_email'] ?? env('EMAIL_FROM_ADDRESS'),
                    'status' => 'draft',
                    'recipient_count' => $recipientCount,
                    'created_by' => session()->get('user_id'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->newsletterModel->insert($draftData);
                $id = $draftData['id'];
                $message = 'Draft saved successfully!';
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message,
                'data' => ['id' => $id]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Draft save error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save draft'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Send test email
     */
    public function sendTest()
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        try {
            $data = $this->request->getPost();
            $testEmail = $data['test_email'] ?? session()->get('user_email');

            if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid email address'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $emailQueueModel = new \App\Models\EmailQueue();
            $fromEmail = $data['sender_email'] ?? env('EMAIL_FROM_ADDRESS');
            $fromName = $data['sender_name'] ?? 'KEWASNET';
            $subject = '[TEST] ' . ($data['subject'] ?? 'Test Newsletter');
            $message = $this->prepareEmailContent($data['content'] ?? '', $testEmail);

            if ($emailQueueModel->queueEmail($testEmail, $subject, $message, null, $fromEmail, $fromName)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Test email queued successfully to ' . $testEmail
                ]);
            } else {
                log_message('error', 'Failed to queue test email to: ' . $testEmail);
                
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to send test email',
                    'debug' => ENVIRONMENT === 'development' ? $debugInfo : null
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            log_message('error', 'Test email error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while sending test email'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Edit newsletter
     */
    public function edit($id)
    {
        $newsletter = $this->newsletterModel->find($id);

        if (!$newsletter) {
            return redirect()->to('auth/blogs/newsletters')->with('error', 'Newsletter not found');
        }

        $stats = $this->subscriberModel->getNewsletterStats();

        return view('backendV2/pages/blogs/edit_newsletter', [
            'title' => 'Edit Newsletter - KEWASNET',
            'dashboardTitle' => 'Edit Newsletter',
            'newsletter' => $newsletter,
            'statistics' => $stats
        ]);
    }

    /**
     * View newsletter details
     */
    public function view($id)
    {
        $newsletter = $this->newsletterModel->find($id);

        if (!$newsletter) {
            return redirect()->to('auth/blogs/newsletters/sent')->with('error', 'Newsletter not found');
        }

        return view('backendV2/pages/blogs/view_newsletter', [
            'title' => 'View Newsletter - KEWASNET',
            'dashboardTitle' => 'Newsletter Details',
            'newsletter' => $newsletter
        ]);
    }

    /**
     * Update newsletter
     */
    public function update($id)
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        try {
            $newsletter = $this->newsletterModel->find($id);

            if (!$newsletter) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Newsletter not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            if ($newsletter['status'] === 'sent') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cannot edit a sent newsletter'
                ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
            }

            $data = $this->request->getPost();

            $rules = [
                'subject' => 'required|min_length[3]|max_length[255]',
                'content' => 'required',
            ];

            if (!$this->validate($rules)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            $updateData = [
                'subject' => $data['subject'],
                'preview_text' => $data['preview_text'] ?? null,
                'content' => $data['content'],
                'sender_name' => $data['sender_name'] ?? 'KEWASNET',
                'sender_email' => $data['sender_email'] ?? env('EMAIL_FROM_ADDRESS'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->newsletterModel->update($id, $updateData);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Newsletter updated successfully!'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Newsletter update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update newsletter'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Send newsletter to all active subscribers
     */
    public function send($id)
    {
        if (!$this->isValidAjaxRequest()) {
            return $this->respondToNonAjax();
        }

        try {
            $newsletter = $this->newsletterModel->find($id);

            if (!$newsletter) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Newsletter not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            if ($newsletter['status'] === 'sent') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Newsletter has already been sent'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Get active subscribers
            $subscribers = $this->subscriberModel->where('is_active', 1)->findAll();

            if (empty($subscribers)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No active subscribers found'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Update status to sending
            $this->newsletterModel->updateStatus($id, 'sending');

            $emailQueueModel = new \App\Models\EmailQueue();
            $sentCount = 0;
            $failedCount = 0;
            $fromEmail = $newsletter['sender_email'] ?? env('EMAIL_FROM_ADDRESS');
            $fromName = $newsletter['sender_name'] ?? 'KEWASNET';

            foreach ($subscribers as $subscriber) {
                try {
                    $subject = $newsletter['subject'];
                    $message = $this->prepareEmailContent($newsletter['content'], $subscriber['email']);

                    if ($emailQueueModel->queueEmail($subscriber['email'], $subject, $message, null, $fromEmail, $fromName)) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                        log_message('error', 'Failed to queue email to: ' . $subscriber['email']);
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    log_message('error', 'Newsletter queue error for ' . $subscriber['email'] . ': ' . $e->getMessage());
                }
            }

            // Update newsletter statistics
            $this->newsletterModel->update($id, [
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s'),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Newsletter sent successfully! Sent: {$sentCount}, Failed: {$failedCount}",
                'data' => [
                    'sent_count' => $sentCount,
                    'failed_count' => $failedCount
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Newsletter send error: ' . $e->getMessage());
            
            // Update status back to draft if failed
            if (isset($id)) {
                $this->newsletterModel->updateStatus($id, 'failed');
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to send newsletter'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete newsletter
     */
    public function delete($id)
    {
        try {
            $newsletter = $this->newsletterModel->find($id);

            if (!$newsletter) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Newsletter not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $this->newsletterModel->delete($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Newsletter deleted successfully!'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Newsletter deletion error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete newsletter'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * List sent newsletters
     */
    public function sentNewsletters()
    {
        $stats = $this->newsletterModel->getNewsletterStats();

        return view('backendV2/pages/blogs/sent_newsletters', [
            'title' => self::SENT_TITLE . ' - KEWASNET',
            'dashboardTitle' => self::SENT_TITLE,
            'statistics' => $stats
        ]);
    }

    /**
     * Get newsletters for DataTable
     */
    public function getNewsletters()
    {
        $columns = ['id', 'subject', 'status', 'recipient_count', 'sent_count', 'scheduled_at', 'sent_at', 'created_at'];

        return $this->dataTableService->handle(
            $this->newsletterModel,
            $columns,
            'getNewslettersTable',
            'countNewsletters'
        );
    }

    /**
     * Prepare email content with unsubscribe link
     */
    private function prepareEmailContent($content, $email)
    {
        $unsubscribeUrl = site_url('newsletter/unsubscribe/' . base64_encode($email));
        
        $footer = "
        <hr style='margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;'>
        <p style='text-align: center; color: #6b7280; font-size: 12px; margin: 20px 0;'>
            If you no longer wish to receive these emails, you can 
            <a href='{$unsubscribeUrl}' style='color: #3b82f6; text-decoration: underline;'>unsubscribe here</a>.
        </p>
        <p style='text-align: center; color: #9ca3af; font-size: 11px;'>
            © " . date('Y') . " KEWASNET. All rights reserved.
        </p>
        ";

        return $content . $footer;
    }

    /**
     * Check if request is a valid AJAX request with CSRF token
     */
    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }
}
