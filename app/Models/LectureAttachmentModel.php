<?php

namespace App\Models;

use CodeIgniter\Model;

class LectureAttachmentModel extends Model
{
    protected $table            = 'lecture_attachments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lecture_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'download_count',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Increment download count
     */
    public function incrementDownload($attachmentId)
    {
        $attachment = $this->find($attachmentId);
        if ($attachment) {
            $this->update($attachmentId, [
                'download_count' => ($attachment['download_count'] ?? 0) + 1
            ]);
        }
    }
}

