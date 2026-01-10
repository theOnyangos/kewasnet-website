<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\ForumMember;
use App\Models\CourseModel;
use App\Models\Discussion;
use App\Models\Forum as ForumModel;

class HomeService
{
    protected $resourceModel;
    protected $forumMemberModel;
    protected $courseModel;
    protected $discussionModel;
    protected $forumModel;

    public function __construct()
    {
        $this->resourceModel = new Resource();
        $this->forumMemberModel = new ForumMember();
        $this->courseModel = new CourseModel();
        $this->discussionModel = new Discussion();
        $this->forumModel = new ForumModel();
    }

    public function getKnowledgeHubStats()
    {
        try {
            // Count published resources (pillar articles) - only published ones
            $totalResources = $this->resourceModel
                ->where('is_published', 1)
                ->where('deleted_at', null)
                ->countAllResults(false);
        } catch (\Exception $e) {
            log_message('error', 'Error counting resources: ' . $e->getMessage());
            $totalResources = 0;
        }
        
        try {
            // Count published courses (learning hub resources)
            // First try with status filter, if that fails, count all non-deleted courses
            $totalLearningHub = $this->courseModel
                ->where('status', 1)
                ->where('deleted_at', null)
                ->countAllResults(false);
        } catch (\Exception $e) {
            // If status column doesn't exist, count all non-deleted courses
            try {
                log_message('debug', 'Courses table may not have status column, counting all non-deleted courses: ' . $e->getMessage());
                $totalLearningHub = $this->courseModel
                    ->where('deleted_at', null)
                    ->countAllResults(false);
            } catch (\Exception $e2) {
                log_message('error', 'Error counting courses: ' . $e2->getMessage());
                $totalLearningHub = 0;
            }
        }
        
        try {
            // Count forum members (networking corner members)
            $totalMembers = $this->forumMemberModel->countAllResults(false);
        } catch (\Exception $e) {
            log_message('error', 'Error counting forum members: ' . $e->getMessage());
            $totalMembers = 0;
        }
        
        try {
            // Additional stats for more details
            // Count published discussions
            $totalDiscussions = $this->discussionModel
                ->where('status', 'active')
                ->where('deleted_at', null)
                ->countAllResults(false);
        } catch (\Exception $e) {
            log_message('error', 'Error counting discussions: ' . $e->getMessage());
            $totalDiscussions = 0;
        }
        
        try {
            // Count active forums (forums use is_active instead of status)
            $totalForums = $this->forumModel
                ->where('is_active', 1)
                ->where('deleted_at', null)
                ->countAllResults(false);
        } catch (\Exception $e) {
            log_message('error', 'Error counting forums: ' . $e->getMessage());
            $totalForums = 0;
        }
        
        try {
            // Count total downloads for resources
            $db = \Config\Database::connect();
            $downloadsResult = $db->table('resources')
                ->selectSum('download_count')
                ->where('is_published', 1)
                ->where('deleted_at', null)
                ->get()
                ->getRow();
            $totalDownloadsCount = !empty($downloadsResult) && !empty($downloadsResult->download_count) ? (int)$downloadsResult->download_count : 0;
        } catch (\Exception $e) {
            log_message('error', 'Error counting downloads: ' . $e->getMessage());
            $totalDownloadsCount = 0;
        }

        return [
            'total_resources'      => $totalResources,
            'total_members'        => $totalMembers,
            'total_learning_hub'   => $totalLearningHub,
            'total_discussions'    => $totalDiscussions,
            'total_forums'         => $totalForums,
            'total_downloads'      => $totalDownloadsCount
        ];
    }
}
