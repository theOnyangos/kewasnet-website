<?php

namespace App\Services;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use App\Libraries\CIAuth;
use CodeIgniter\Files\File;
use App\Models\FileAttachment;
use App\Models\JobApplicantModel;
use App\Services\NotificationService;
use CodeIgniter\HTTP\Files\UploadedFile;

class JobApplicationService
{
    protected JobApplicantModel $jobApplicantModel;
    protected FileAttachment $fileAttachmentModel;
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->jobApplicantModel = new JobApplicantModel();
        $this->fileAttachmentModel = new FileAttachment();
        $this->notificationService = new NotificationService();
    }

    /**
     * Submit a job application
     *
     * @param array $applicationData
     * @param array $opportunity
     * @param UploadedFile $resumeFile
     * @return array
     */
    public function submitApplication(array $applicationData, array $opportunity, UploadedFile $resumeFile): array
    {
        $request    = service('request');
        $userAgent  = $request->getUserAgent();
        $browser    = $userAgent->getBrowser();
        $platform   = $userAgent->getPlatform();
        $ip         = $request->getIPAddress();

        // Initialize variables for cleanup
        $resumeFileData = null;
        $applicationId = null;
        
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Check for duplicate application
            if ($this->isDuplicateApplication($opportunity['id'], $applicationData['application_email'])) {
                $db->transRollback();
                return [
                    'success' => false,
                    'message' => 'You have already applied for this position with this email address.'
                ];
            }

            // Handle resume upload BEFORE database operations
            $resumeFileData = $this->handleResumeUpload($resumeFile);
            if (!$resumeFileData['success']) {
                $db->transRollback();
                return $resumeFileData;
            }

            // Generate application ID
            $applicationId = Uuid::uuid4()->toString();

            // Get browser info
            $applicationData['ip_address'] = $ip ?? null;
            $applicationData['user_agent'] = $browser . '-' . $platform ?? null;

            // Prepare application data
            $finalApplicationData = [
                'id'                    => $applicationId,
                'opportunity_id'        => $opportunity['id'], // Use opportunity_id to match the model
                'first_name'            => $applicationData['first_name'],
                'last_name'             => $applicationData['last_name'],
                'email'                 => $applicationData['application_email'],
                'phone'                 => $applicationData['phone'] ?? null,
                'location'              => $applicationData['location'] ?? null,
                'linkedin_profile'      => $applicationData['linkedin_profile'] ?? null,
                'current_job_title'     => $applicationData['current_job_title'] ?? null,
                'current_company'       => $applicationData['current_company'] ?? null,
                'portfolio_url'         => $applicationData['portfolio_url'] ?? null,
                'years_of_experience'   => !empty($applicationData['experience_years']) ? (int)$applicationData['experience_years'] : null,
                'education_level'       => $applicationData['education_level'] ?? null,
                'cover_letter'          => $applicationData['cover_letter'],
                'availability'          => $applicationData['availability'] ?? null,
                'salary_expectation'    => $applicationData['salary_expectation'] ?? null,
                'status'                => 'pending',
                'ip_address'            => $applicationData['ip_address'],
                'user_agent'            => $applicationData['user_agent'],
            ];

            // Save the application
            if (!$this->jobApplicantModel->save($finalApplicationData)) {
                $db->transRollback();
                
                // Clean up uploaded file since database operation failed
                if ($resumeFileData && isset($resumeFileData['file_path'])) {
                    $this->cleanUpFile($resumeFileData['file_path']);
                }
                
                return [
                    'success' => false,
                    'message' => 'Failed to submit application. Please try again.',
                    'errors' => $this->jobApplicantModel->errors()
                ];
            }

            // Save file attachment record
            $fileAttachmentData = [
                'attachable_type'   => 'job_applications',
                'attachable_id'     => $applicationId,
                'original_name'     => $resumeFileData['original_name'],
                'file_name'         => $resumeFileData['file_name'],
                'file_path'         => $resumeFileData['file_path'],
                'file_type'         => $resumeFileData['file_extension'],
                'file_size'         => $resumeFileData['file_size'],
                'mime_type'         => $resumeFileData['mime_type'],
                'download_count'    => 0,
            ];

            if (!$this->fileAttachmentModel->save($fileAttachmentData)) {
                $db->transRollback();
                
                // Clean up uploaded file since file attachment save failed
                if ($resumeFileData && isset($resumeFileData['file_path'])) {
                    $this->cleanUpFile($resumeFileData['file_path']);
                }
                
                log_message('error', 'Failed to save file attachment for application: ' . $applicationId);
                return [
                    'success' => false,
                    'message' => 'Failed to process file attachment. Please try again.'
                ];
            }

            // Complete the transaction
            $db->transComplete();

            // Check if transaction was successful
            if ($db->transStatus() === FALSE) {
                // Transaction failed, clean up uploaded file
                if ($resumeFileData && isset($resumeFileData['file_path'])) {
                    $this->cleanUpFile($resumeFileData['file_path']);
                }

                log_message('error', 'Database transaction failed for job application: ' . $applicationId);
                return [
                    'success' => false,
                    'message' => 'Failed to submit application due to database error. Please try again.'
                ];
            }

            // Send notification to admin users about new application
            try {
                $this->notifyAdminsOfNewApplication($opportunity, $applicationData);
            } catch (\Exception $e) {
                // Log notification error but don't fail the application
                log_message('error', 'Failed to send notification for job application: ' . $e->getMessage());
            }

            return [
                'success' => true,
                'message' => 'Your application has been submitted successfully! We will review your application and get back to you soon.',
                'application_id' => $applicationId
            ];

        } catch (\Exception $e) {
            // Rollback database transaction
            $db->transRollback();
            
            // Clean up uploaded file on any error
            if ($resumeFileData && isset($resumeFileData['file_path'])) {
                $this->cleanUpFile($resumeFileData['file_path']);
            }
            
            log_message('error', 'Job application submission error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'An error occurred while submitting your application. Please try again.'
            ];
        }
    }

    /**
     * Handle resume file upload
     *
     * @param UploadedFile $file
     * @return array
     */
    public function handleResumeUpload(UploadedFile $file): array
    {
        $filePath = 'job_applications/resumes/' . date('Y-m-d');
        return $this->handleFileUpload($file, $filePath, [
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_extensions' => ['pdf', 'doc', 'docx'],
            'file_type' => 'resume'
        ]);
    }

    /**
     * General file upload handler
     *
     * @param UploadedFile $file
     * @param string $subDirectory
     * @param array $options
     * @return array
     */
    public function handleFileUpload(UploadedFile $file, string $subDirectory, array $options = []): array
    {
        try {
            if (!$file->isValid()) {
                return [
                    'success' => false,
                    'message' => 'Invalid file upload.'
                ];
            }

            // Default options
            $defaultOptions = [
                'max_size' => 10 * 1024 * 1024, // 10MB default
                'allowed_extensions' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
                'file_type' => 'document'
            ];

            $options = array_merge($defaultOptions, $options);

            // Check file size
            if ($file->getSize() > $options['max_size']) {
                $maxSizeMB = round($options['max_size'] / (1024 * 1024), 1);
                return [
                    'success' => false,
                    'message' => "File size cannot exceed {$maxSizeMB}MB."
                ];
            }

            // Check file extension
            $fileExtension = strtolower($file->getClientExtension());
            if (!in_array($fileExtension, $options['allowed_extensions'])) {
                $allowedList = implode(', ', array_map('strtoupper', $options['allowed_extensions']));
                return [
                    'success' => false,
                    'message' => "File must be one of: {$allowedList}."
                ];
            }

            // Generate unique filename
            $fileName   = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/' . $subDirectory . '/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0755, true)) {
                    throw new RuntimeException('Failed to create upload directory');
                }
            }
            
            // Move file to upload directory
            if (!$file->move($uploadPath, $fileName)) {
                throw new RuntimeException('Failed to move uploaded file');
            }

            $relativePath = $subDirectory . '/' . $fileName;
            
            return [
                'success'           => true,
                'original_name'     => $file->getClientName(),
                'file_name'         => $fileName,
                'file_path'         => $relativePath,
                'file_extension'    => $fileExtension,
                'file_size'         => $file->getSize(),
                'mime_type'         => $file->getClientMimeType(),
                'full_path'         => $uploadPath . $fileName,
                'file_type'         => $options['file_type']
            ];

        } catch (\Exception $e) {
            log_message('error', 'File upload error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to upload file. Please try again.'
            ];
        }
    }

    /**
     * Check if user has already applied for this opportunity
     *
     * @param string $opportunityId
     * @param string $email
     * @return bool
     */
    protected function isDuplicateApplication(string $opportunityId, string $email): bool
    {
        $existingApplication = $this->jobApplicantModel
            ->where('opportunity_id', $opportunityId) // Use opportunity_id to match the model
            ->where('email', $email)
            ->where('deleted_at', null)
            ->first();

        return !empty($existingApplication);
    }

    /**
     * Clean up uploaded file
     *
     * @param string $filePath
     * @return bool
     */
    public function cleanUpFile(string $filePath): bool
    {
        $fullPath = WRITEPATH . 'uploads/' . $filePath;
        
        if (file_exists($fullPath)) {
            $success = unlink($fullPath);
            if ($success) {
                log_message('info', 'Cleaned up file: ' . $filePath);
            } else {
                log_message('error', 'Failed to clean up file: ' . $filePath);
            }
            return $success;
        }
        
        return false;
    }

    /**
     * Clean up multiple uploaded files
     *
     * @param array $filePaths Array of file paths to clean up
     * @return array Results of cleanup operations
     */
    public function cleanUpFiles(array $filePaths): array
    {
        $results = [];
        
        foreach ($filePaths as $filePath) {
            $results[$filePath] = $this->cleanUpFile($filePath);
        }
        
        return $results;
    }

    /**
     * Validate application data
     *
     * @param array $data
     * @return array
     */
    public function validateApplicationData(array $data): array
    {
        $validation = \Config\Services::validation();

        $rules = [
            'first_name'            => 'required|max_length[100]',
            'last_name'             => 'required|max_length[100]',
            'application_email'     => 'required|valid_email|max_length[255]',
            'phone'                 => 'permit_empty|max_length[20]',
            'location'              => 'permit_empty|max_length[255]',
            'experience_years'      => 'permit_empty|integer|greater_than_equal_to[0]',
            'education_level'       => 'permit_empty|in_list[high-school,diploma,bachelors,masters,phd,other]',
            'cover_letter'          => 'required|max_length[5000]',
            'availability'          => 'permit_empty|in_list[immediate,2-weeks,1-month,2-months,3-months,negotiable]',
            'salary_expectation'    => 'permit_empty|max_length[100]'
        ];

        $messages = [
            'first_name' => [
                'required' => 'First name is required.',
                'max_length' => 'First name cannot exceed 100 characters.'
            ],
            'last_name' => [
                'required' => 'Last name is required.',
                'max_length' => 'Last name cannot exceed 100 characters.'
            ],
            'application_email' => [
                'required' => 'Email address is required.',
                'valid_email' => 'Please provide a valid email address.',
                'max_length' => 'Email address cannot exceed 255 characters.'
            ],
            'cover_letter' => [
                'required' => 'Cover letter is required.',
                'max_length' => 'Cover letter cannot exceed 5000 characters.'
            ]
        ];

        $validation->setRules($rules, $messages);

        if (!$validation->run($data)) {
            return [
                'success' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validation->getErrors()
            ];
        }

        return ['success' => true];
    }

    /**
     * Validate resume file
     *
     * @param UploadedFile $file
     * @return array
     */
    public function validateResumeFile(UploadedFile $file): array
    {
        if (!$file->isValid()) {
            return [
                'success' => false,
                'message' => 'Please upload your resume.'
            ];
        }

        // Check file size (5MB max)
        if ($file->getSize() > 5 * 1024 * 1024) {
            return [
                'success' => false,
                'message' => 'Resume file size cannot exceed 5MB.'
            ];
        }

        // Check file extension
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $fileExtension = strtolower($file->getClientExtension());
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            return [
                'success' => false,
                'message' => 'Resume must be a PDF, DOC, or DOCX file.'
            ];
        }

        return ['success' => true];
    }

    /**
     * Check if application deadline has passed
     *
     * @param string|null $deadline
     * @return bool
     */
    public function isDeadlinePassed(?string $deadline): bool
    {
        if (empty($deadline)) {
            return false;
        }

        $deadlineDate = new \DateTime($deadline);
        $now = new \DateTime();

        return $now > $deadlineDate;
    }

    /**
     * Notify admin users about new job application
     *
     * @param array $opportunity
     * @param array $applicationData
     * @return void
     */
    protected function notifyAdminsOfNewApplication(array $opportunity, array $applicationData): void
    {
        // Get all admin users (you may need to adjust this based on your role system)
        $userModel = new \App\Models\UserModel();
        $adminUsers = $userModel->where('role', 'admin')->findAll();

        if (empty($adminUsers)) {
            return;
        }

        $applicantName = $applicationData['first_name'] . ' ' . $applicationData['last_name'];
        $jobTitle = $opportunity['title'];
        $jobId = $opportunity['id'];

        // Send notification to each admin
        foreach ($adminUsers as $admin) {
            $this->notificationService->notifyNewJobApplication(
                (int)$admin['id'],
                $jobTitle,
                $applicantName,
                $jobId
            );
        }
    }
}
