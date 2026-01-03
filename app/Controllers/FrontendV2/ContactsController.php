<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use App\Services\EmailSettingsService;
use CodeIgniter\HTTP\ResponseInterface;

class ContactsController extends BaseController
{
    protected $emailService;
    protected $validation;

    public function __construct()
    {
        $this->emailService = new EmailSettingsService();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Handle contact form submission
     */
    public function submitContact()
    {
        // Validate input
        $rules = [
            'first-name'    => 'required|min_length[2]|max_length[50]|alpha_space',
            'last-name'     => 'required|min_length[2]|max_length[50]|alpha_space',
            'email'         => 'required|valid_email|max_length[100]',
            'organization'  => 'permit_empty|max_length[100]',
            'subject'       => 'required|max_length[200]',
            'message'       => 'required|min_length[10]|max_length[2000]'
        ];

        $messages = [
            'first-name' => [
                'required'      => 'First name is required',
                'min_length'    => 'First name must be at least 2 characters',
                'max_length'    => 'First name cannot exceed 50 characters',
                'alpha_space'   => 'First name can only contain letters and spaces'
            ],
            'last-name' => [
                'required'      => 'Last name is required',
                'min_length'    => 'Last name must be at least 2 characters',
                'max_length'    => 'Last name cannot exceed 50 characters',
                'alpha_space'   => 'Last name can only contain letters and spaces'
            ],
            'email' => [
                'required'      => 'Email address is required',
                'valid_email'   => 'Please enter a valid email address',
                'max_length'    => 'Email address cannot exceed 100 characters'
            ],
            'organization' => [
                'max_length'    => 'Organization name cannot exceed 100 characters'
            ],
            'subject' => [
                'required'      => 'Subject is required',
                'max_length'    => 'Subject cannot exceed 200 characters'
            ],
            'message' => [
                'required'      => 'Message is required',
                'min_length'    => 'Message must be at least 10 characters',
                'max_length'    => 'Message cannot exceed 2000 characters'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status'    => 'error',
                'errors'    => $this->validator->getErrors(),
                'message'   => 'Please correct the errors below'
            ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            // Get form data
            $firstName      = $this->request->getPost('first-name');
            $lastName       = $this->request->getPost('last-name');
            $email          = $this->request->getPost('email');
            $organization   = $this->request->getPost('organization');
            $subject        = $this->request->getPost('subject');
            $message        = $this->request->getPost('message');

            // Send email
            $emailSent = $this->sendContactEmail([
                'first_name'    => $firstName,
                'last_name'     => $lastName,
                'email'         => $email,
                'organization'  => $organization,
                'subject'       => $subject,
                'message'       => $message
            ]);

            if ($emailSent['success']) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Thank you for contacting us! We will get back to you soon.'
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to send email. Please try again later.',
                    'error_details' => $emailSent['message'] ?? 'Unknown error'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            log_message('error', 'Contact form submission error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Send contact email to KEWASNET
     */
    /**
     * Send contact email using Mailer library
     */
    private function sendContactEmail(array $contactData): array
    {
        try {
            // Load the Mailer library
            $mailer = new \App\Libraries\Mailer();

            // Prepare data for email template
            $fullName = $contactData['first_name'] . ' ' . $contactData['last_name'];
            $organization = !empty($contactData['organization']) ? $contactData['organization'] : 'Not specified';

            $templateData = [
                'contactData' => $contactData,
                'fullName' => $fullName,
                'organization' => $organization
            ];

            // Generate email body from template
            $emailBody = view('emails/contact-us-template', $templateData);

            // Send email using Mailer library
            $result = $mailer->send(
                'info@kewasnet.org',                               // to (KEWASNET's email)
                'Contact Form: ' . $contactData['subject'],        // subject
                $emailBody,                                        // body
                $contactData['email'],                             // reply_to (sender's email)
                $fullName                                          // reply_to_name (sender's name)
            );

            if ($result) {
                // Log successful contact
                log_message('info', "Contact form email sent from: {$contactData['email']}");
                
                return [
                    'success' => true,
                    'message' => 'Email sent successfully'
                ];
            } else {
                log_message('error', 'Contact email failed using Mailer library');
                
                return [
                    'success' => false,
                    'message' => 'Failed to send email'
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'Contact email error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
