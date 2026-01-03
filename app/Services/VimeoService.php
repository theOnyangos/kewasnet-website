<?php

namespace App\Services;

use App\Models\VimeoVideoModel;
use App\Models\CourseEnrollmentModel;
use App\Services\CourseService;

class VimeoService
{
    protected $vimeoModel;
    protected $enrollmentModel;
    protected $courseService;

    public function __construct()
    {
        $this->vimeoModel = new VimeoVideoModel();
        $this->enrollmentModel = new CourseEnrollmentModel();
        $this->courseService = new CourseService();
    }

    /**
     * Get video embed code
     */
    public function getVideoEmbedCode($vimeoId, $userId, $courseId)
    {
        // Validate access first
        if (!$this->validateVideoAccess($vimeoId, $userId, $courseId)) {
            return [
                'status' => 'error',
                'message' => 'You do not have access to this video'
            ];
        }

        $video = $this->vimeoModel->getByVimeoId($vimeoId);
        
        if (!$video) {
            return [
                'status' => 'error',
                'message' => 'Video not found'
            ];
        }

        // If embed code exists, return it
        if (!empty($video['embed_code'])) {
            return [
                'status' => 'success',
                'embed_code' => $video['embed_code'],
                'video_url' => $video['video_url'],
                'thumbnail_url' => $video['thumbnail_url']
            ];
        }

        // Generate embed code from Vimeo ID
        $embedCode = $this->generateEmbedCode($vimeoId);

        // Update database
        $this->vimeoModel->update($video['id'], [
            'embed_code' => $embedCode
        ]);

        return [
            'status' => 'success',
            'embed_code' => $embedCode,
            'video_url' => $video['video_url'],
            'thumbnail_url' => $video['thumbnail_url']
        ];
    }

    /**
     * Validate video access
     */
    public function validateVideoAccess($vimeoId, $userId, $courseId)
    {
        // Check if user has access to the course
        return $this->courseService->checkAccess($userId, $courseId);
    }

    /**
     * Generate Vimeo embed code
     */
    protected function generateEmbedCode($vimeoId)
    {
        $vimeoApi = new \App\Libraries\VimeoApi();
        $config = config('Vimeo');
        return $vimeoApi->getEmbedCode($vimeoId, $config->embedSettings ?? []);
    }

    /**
     * Upload video to Vimeo (if needed)
     * This would require Vimeo API integration
     */
    public function uploadVideo($file)
    {
        // TODO: Implement Vimeo API upload
        // This would use Vimeo PHP library to upload videos
        // For now, videos should be uploaded manually to Vimeo
        // and the vimeo_video_id stored in the database
        
        return [
            'status' => 'error',
            'message' => 'Video upload not implemented. Please upload to Vimeo manually and enter the video ID.'
        ];
    }
}

