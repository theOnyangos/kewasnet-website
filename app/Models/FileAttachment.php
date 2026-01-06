<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;
use App\Libraries\CIAuth;
use App\Libraries\ClientAuth;

class FileAttachment extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'file_attachments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'attachable_type',
        'attachable_id',
        'user_id',
        'original_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'download_count',
        'is_image',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [];

    protected $validationMessages = [];

    protected $beforeInsert = ['generateUUID', 'determineIfImage', 'setUserId'];
    protected $beforeUpdate = ['determineIfImage', 'setUserId'];

    /**
     * Generates UUID using Ramsey's UUID library
     */
    protected function generateUUID(array $data)
    {
        if (empty($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Add user_id field based on session data
     */
    protected function setUserId(array $data)
    {
        if (empty($data['data']['user_id']) && (CIAuth::isLoggedIn() || ClientAuth::isLoggedIn())) {
            $data['data']['user_id'] = session()->get('id') ?? session()->get('user_id');
        }
        return $data;
    }

    /**
     * Determines if attachment is an image based on mime type
     */
    protected function determineIfImage(array $data)
    {
        if (isset($data['data']['mime_type'])) {
            $data['data']['is_image'] = strpos($data['data']['mime_type'], 'image/') === 0;
        }
        return $data;
    }

    /**
     * Increments download count for a file
     */
    public function incrementDownloadCount(string $fileId)
    {
        return $this->builder()
            ->where('id', $fileId)
            ->set('download_count', 'download_count + 1', false)
            ->update();
    }

    /**
     * Gets attachments for a specific item
     */
    public function getAttachmentsFor(string $attachableType, string $attachableId)
    {
        return $this->where('attachable_type', $attachableType)
                   ->where('attachable_id', $attachableId)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    /**
     * Gets image attachments for a specific item
     */
    public function getImageAttachmentsFor(string $attachableType, string $attachableId)
    {
        return $this->where('attachable_type', $attachableType)
                   ->where('attachable_id', $attachableId)
                   ->where('is_image', true)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    /**
     * Gets attachments by a specific user
     */
    public function getUserAttachments(string $userId, int $limit = 20)
    {
        return $this->where('user_id', $userId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll($limit);
    }

    /**
     * Deletes an attachment and its file
     */
    public function deleteAttachment(string $attachmentId): bool
    {
        $attachment = $this->find($attachmentId);
        if (!$attachment) {
            return false;
        }

        // Delete the physical file
        $filePath = WRITEPATH . 'uploads/' . $attachment->file_path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the database record
        return $this->delete($attachmentId);
    }

    /**
     * Gets total attachment size for a user
     */
    public function getUserTotalAttachmentSize(string $userId): int
    {
        $result = $this->builder()
            ->selectSum('file_size')
            ->where('user_id', $userId)
            ->get()
            ->getRow();

        return $result ? (int)$result->file_size : 0;
    }

    /**
     * Gets all attachments of a specific type
     */
    public function getByFileType(string $fileType, int $limit = 20)
    {
        return $this->where('file_type', $fileType)
                   ->orderBy('created_at', 'DESC')
                   ->findAll($limit);
    }

    // Get Resource Attachment
    public function getAttachmentsForResource($resourceId)
    {
        log_message('debug', "FileAttachment: Looking for attachments with resourceId: " . $resourceId);
        
        // Use 'resources' (plural) to match the database value
        $attachments = $this->select('file_path, file_size, original_name, download_count, file_type, mime_type')
                            ->where('attachable_type', 'resources')
                           ->where('attachable_id', $resourceId)
                           ->findAll();
        
        log_message('debug', "FileAttachment: Found " . count($attachments) . " attachments");

        $totalDownloads = 0;
        $totalFileSize = 0;
        $attachmentsArray = [];
        
        foreach ($attachments as $attachment) {
            // Convert to array if object
            $attachmentArray = is_object($attachment) ? (array)$attachment : $attachment;
            
            $totalDownloads += $attachmentArray['download_count'] ?? 0;
            $totalFileSize += $attachmentArray['file_size'] ?? 0;
            $attachmentsArray[] = $attachmentArray;
        }

        return [
            'attachments' => $attachmentsArray,
            'total_downloads' => $totalDownloads,
            'total_file_size' => $totalFileSize,
        ];
    }
}