<?php

namespace App\Models;

use CodeIgniter\Model;

class VimeoVideoModel extends Model
{
    protected $table            = 'vimeo_videos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lecture_id',
        'vimeo_video_id',
        'video_url',
        'thumbnail_url',
        'duration',
        'embed_code',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get video by lecture ID
     */
    public function getByLectureId($lectureId)
    {
        return $this->where('lecture_id', $lectureId)
            ->first();
    }

    /**
     * Get video by Vimeo ID
     */
    public function getByVimeoId($vimeoId)
    {
        return $this->where('vimeo_video_id', $vimeoId)
            ->first();
    }
}

