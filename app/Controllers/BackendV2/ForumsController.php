<?php

namespace App\Controllers\BackendV2;

use Carbon\Carbon;
use App\Models\Forum;
use App\Models\Report;
use App\Models\Discussion;
use App\Models\FileAttachment;
use App\Models\ForumModerator;
use App\Models\ForumMember;
use App\Services\ForumService;
use App\Services\DiscussionService;
use App\Services\DataTableService;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Exceptions\ValidationException;

class ForumsController extends BaseController
{
    protected const PAGE_TITLE = "Manage Forums - KEWASNET";
    protected const FORUMS_PAGE_TITLE = "Forums Management";
    protected const RECENT_DISCUSSIONS_PAGE_TITLE = "Recent Discussions";
    protected const MODERATORS_PAGE_TITLE = "Forum Moderators";
    protected const REPORTS_PAGE_TITLE = "Content Reports";
    protected const CREATE_FORUM_PAGE_TITLE = "Create New Forum";
    protected const ADD_MODERATORS_PAGE_TITLE = "Add Forum Moderators";
    protected const CREATE_DISCUSSIONS_PAGE_TITLE = "Create Forum Discussion";
    protected const FORUM_DETAILS_PAGE_TITLE = "Forum Details";
    protected const EDIT_FORUM_PAGE_TITLE = "Edit Forum Details";

    protected $discussionService;
    protected $dataTableService;
    protected $forumService;
    protected $forumModel;
    protected $stats;

    public function __construct()
    {
        $this->discussionService = new DiscussionService();
        $this->dataTableService = new DataTableService();
        $this->forumService = new ForumService();
        $this->forumModel = new Forum();

        $reportCount = model(Report::class)->getReportCount();
        $forumStats = $this->forumModel->getForumStats();
        $forumStats->report_count = $reportCount;

        $this->stats = [
            'forum_count' => $forumStats->forum_count,
            'total_discussions' => $forumStats->discussion_count,
            'total_reports' => $reportCount,
            'total_moderator' => $forumStats->moderator_count
        ];
    }

    //======= View Methods =======
    public function index()
    {
        return view('backendV2/pages/forums/index', [
            'title' => self::FORUMS_PAGE_TITLE,
            'dashboardTitle' => self::FORUMS_PAGE_TITLE,
            'forumStats' => $this->stats
        ]);
    }

    public function recentDiscussions()
    {
        return view('backendV2/pages/forums/discussions', [
            'title' => self::RECENT_DISCUSSIONS_PAGE_TITLE,
            'dashboardTitle' => self::RECENT_DISCUSSIONS_PAGE_TITLE,
        ]);
    }

    public function moderators()
    {
        return view('backendV2/pages/forums/moderators', [
            'title' => self::MODERATORS_PAGE_TITLE,
            'dashboardTitle' => self::MODERATORS_PAGE_TITLE,
        ]);
    }

    public function reports()
    {
        $reportModel = model(Report::class);
        
        // Get all reports with reporter and reported user details
        $reports = $reportModel->select('reports.*, 
                                         reporter.first_name as reporter_first_name,
                                         reporter.last_name as reporter_last_name,
                                         reporter.email as reporter_email,
                                         reported.first_name as reported_first_name,
                                         reported.last_name as reported_last_name,
                                         reported.email as reported_email')
                              ->join('system_users as reporter', 'reporter.id = reports.reporter_id', 'left')
                              ->join('system_users as reported', 'reported.id = reports.reportable_id', 'left')
                              ->where('reports.reportable_type', 'user')
                              ->orderBy('reports.created_at', 'DESC')
                              ->findAll();
        
        return view('backendV2/pages/forums/reports', [
            'title' => self::REPORTS_PAGE_TITLE,
            'dashboardTitle' => self::REPORTS_PAGE_TITLE,
            'forumStats' => $this->stats,
            'reports' => $reports
        ]);
    }

    public function getCreateForum()
    {
        return view('backendV2/pages/forums/create_forum', [
            'title' => self::CREATE_FORUM_PAGE_TITLE,
            'dashboardTitle' => self::CREATE_FORUM_PAGE_TITLE,
        ]);
    }

    public function editForum($forumId)
    {
        $forum = $this->forumModel->find($forumId);

        if (!$forum) return redirect()->to(site_url('auth/forums'));

        return view('backendV2/pages/forums/edit_forum', [
            'title' => self::EDIT_FORUM_PAGE_TITLE,
            'dashboardTitle' => $forum->name . ' - Edit Forum Details',
            'forum' => $forum
        ]);
    }

    public function addModerators($forumId)
    {
        $forum = $this->forumModel->find($forumId);

        if (!$forum) return redirect()->to(site_url('auth/forums'));

        return view('backendV2/pages/forums/add_moderators', [
            'title' => self::ADD_MODERATORS_PAGE_TITLE,
            'dashboardTitle' => $forum->name . ' - Add Moderators',
            'forumId' => $forumId
        ]);
    }

    public function addDiscussions($forumId)
    {
        $forum = $this->forumModel->find($forumId);

        if (!$forum) return redirect()->to(site_url('auth/forums'));

        return view('backendV2/pages/forums/create_discussion', [
            'title' => self::CREATE_DISCUSSIONS_PAGE_TITLE,
            'dashboardTitle' => $forum->name . ' - Create Discussion',
            'forumId' => $forumId
        ]);
    }

    public function forumDetails($forumId)
    {
        $forum = $this->forumModel->getForumDetails($forumId);

        if (!$forum) return redirect()->to(site_url('auth/forums'));

        return view('backendV2/pages/forums/forum_details', [
            'title' => self::FORUMS_PAGE_TITLE,
            'dashboardTitle' => $forum->name . ' - Forum Details',
            'forum' => $forum
        ]);
    }

    public function deleteForum($forumId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $forumModel = new Forum();
            $forum = $forumModel->find($forumId);
            
            if (!$forum) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Forum not found.'
                );
            }
            
            // Delete forum with all relations and attachments
            $result = $forumModel->deleteForumWithRelations($forumId);
            
            if (!$result) {
                // Get more detailed error info
                $error = $forumModel->errors();
                $dbError = $forumModel->db->error();
                
                log_message('error', 'Forum deletion failed. Result: false');
                log_message('error', 'Model errors: ' . json_encode($error));
                log_message('error', 'Database error: ' . json_encode($dbError));
                
                $errorMessage = 'Failed to delete forum. Please try again.';
                if (ENVIRONMENT === 'development' && !empty($dbError['message'])) {
                    $errorMessage .= ' Database error: ' . $dbError['message'];
                }
                
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                    $errorMessage
                );
            }

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Forum and all related data have been deleted successfully.'
            );
            
        } catch (\Exception $e) {
            log_message('error', 'Forum deletion exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'An error occurred while deleting the forum.';
            if (ENVIRONMENT === 'development') {
                $errorMessage .= ' Error: ' . $e->getMessage();
            }
            
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                $errorMessage
            );
        }
    }

    public function getForums()
    {
        $model = model(Forum::class);
        $columns = ['id', 'name', 'description', 'icon', 'color', 'members', 'total_discussions', 'reply_count', 'status'];

        return $this->dataTableService->handle(
            $model,
            $columns,
            'getForumTable', // Model method to get data
            'countForums',
        );
    }

    public function editDiscussion(string $discussionId)
    {
        $discussionModel = new Discussion();
        $discussion = $discussionModel->find($discussionId);

        if (!$discussion) {
            return redirect()->to(site_url('auth/forums'))->with('error', 'Discussion not found');
        }

        // Get attachments for this discussion
        $attachmentModel = new FileAttachment();
        $attachments = $attachmentModel->getAttachmentsFor('discussion', $discussionId);

        // Convert discussion object to array for view
        $discussionArray = (array) $discussion;
        $discussionArray['attachments'] = $attachments;

        return view('backendV2/pages/forums/edit_discussion', [
            'title' => 'Edit Discussion',
            'dashboardTitle' => 'Edit Discussion - ' . $discussion->title,
            'discussion' => $discussionArray
        ]);
    }

    public function updateDiscussion(string $discussionId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $discussionData = $this->request->getPost();
            $files = $this->request->getFiles();
            
            $discussion = $this->discussionService->updateDiscussion($discussionId, $discussionData, $files);

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Discussion updated successfully',
                ['discussion' => $discussion]
            );
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to update discussion: ' . $e->getMessage()
            );
        }
    }

    public function deleteDiscussion(string $discussionId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $discussionModel = new Discussion();
            $discussion = $discussionModel->find($discussionId);

            if (!$discussion) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Discussion not found'
                );
            }

            $discussionModel->delete($discussionId);

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Discussion deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to delete discussion: ' . $e->getMessage()
            );
        }
    }

    public function deleteAttachment(string $attachmentId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $attachmentModel = new FileAttachment();
            $attachment = $attachmentModel->find($attachmentId);

            if (!$attachment) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Attachment not found'
                );
            }

            // Verify user has permission to delete (owns the discussion)
            $discussionModel = new Discussion();
            $discussion = $discussionModel->find($attachment['attachable_id']);
            
            if (!$discussion) {
                return $this->generateJsonResponse(
                    'error',
                    ResponseInterface::HTTP_NOT_FOUND,
                    'Discussion not found'
                );
            }

            // Delete physical file
            $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    log_message('error', 'Failed to delete file: ' . $filePath);
                }
            }

            // Delete database record
            $attachmentModel->delete($attachmentId);

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Attachment deleted successfully'
            );
        } catch (\Exception $e) {
            log_message('error', 'Attachment deletion failed: ' . $e->getMessage());
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to delete attachment: ' . $e->getMessage()
            );
        }
    }

    public function handleCreateDiscussions(string $forumId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $discussionData = $this->request->getPost();
            $content = $this->request->getPost('content');

            // Purify the content
            $content = strip_tags($content, '<p><br><strong><em><u><ul><ol><li><a>');
            $content = htmlspecialchars_decode($content); // Preserve allowed tags

            $discussionData['content'] = $content;
            $files = $this->request->getFiles();
            
            $discussion = $this->discussionService->createDiscussion($forumId, $discussionData, $files);

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_CREATED,
                'Discussion created successfully',
                ['discussion' => $discussion]
            );
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to create discussion: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function viewForum($forumId)
    {
        try {            
            // Get discussions with pagination and attachments
            $result = $this->discussionService->getDiscussionsByForumId($forumId, [
                'per_page' => 15,
                'order_by' => 'created_at',
                'order_dir' => 'DESC',
                'force_refresh' => true,
                'search' => 'important topic',
                'cache_ttl' => 600 // 10 minute cache
            ]);
            
            return view('forum/view', [
                'discussions' => $result['discussions'],
                'pager' => $result['pager']
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function handleGetForumModerators($forumId)
    {
        try {
            $moderators = $this->forumService->getModeratorsByForumId($forumId);

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Moderators retrieved successfully',
                $moderators
            );
        } catch (\Exception $e) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to create discussion: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function getSystemUsers()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            // Accept both GET and POST requests for search term
            $searchTerm = $this->request->getPost('search') ?? $this->request->getGet('search');

            $users = $this->forumService->getSystemUsers($searchTerm);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to retrieve users: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    public function handleCreateModerators()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $moderatorData = $this->request->getPost();
            $result = $this->forumService->addModerators($moderatorData);

            // Handle array result from multiple moderators
            if (is_array($result) && isset($result['success_count'])) {
                $message = "{$result['success_count']} moderator(s) added successfully";
                if ($result['skipped_count'] > 0) {
                    $message .= ", {$result['skipped_count']} skipped (already moderators)";
                }

                return $this->generateJsonResponse(
                    'success',
                    ResponseInterface::HTTP_CREATED,
                    $message,
                    $result
                );
            }

            // Handle single moderator response
            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_CREATED,
                'Moderator added successfully'
            );
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors());
        } catch (\Exception $e) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to add moderators: ' . $e->getMessage(),
                [],
                $e
            );
        }
    }

    //=========== Action Methods =============

    public function handleCreateForum()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $forumData = $this->request->getPost();
            $forum = $this->forumService->createForum($forumData);
            
            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_CREATED,
                'Forum created successfully',
                ['forum' => $forum]
            );
            
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors()); // Handle validation errors with field-specific messages
        } catch (\Exception $e) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to create forum',
                [],
                $e
            );
        }
    }

    public function updateForumDetails(string $forumId)
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $forumData = $this->request->getPost();

            $forum = $this->forumService->updateForum($forumId, $forumData);

            return $this->generateJsonResponse(
                'success',
                ResponseInterface::HTTP_OK,
                'Forum updated successfully',
                ['forum' => $forum]
            );
        } catch (ValidationException $e) {
            return $this->failValidationErrors($e->getErrors()); // Handle validation errors with field-specific messages
        } catch (\Exception $e) {
            return $this->generateJsonResponse(
                'error',
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'Failed to update forum',
                [],
                $e
            );
        }
    }

    public function handleGetDiscussions(string $forumId)
    {
        $model = model(Discussion::class);
        $columns = ['id', 'author_profile', 'title', 'content', 'reply_count', 'view_count', 'category', 'comment_count', 'last_reply_at',  'created_at'];

        // Use DataTableService to handle the request with forum ID filter
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getDiscussions',
            'countDiscussions',
            null, // No data formatter
            [$forumId] // Pass forum ID as additional parameter
        );
    }

    public function handleGetModerators(string $forumId)
    {
        $model = model(ForumModerator::class);
        $columns = ['id', 'moderator_name', 'moderator_profile', 'moderator_title', 'assigned_at', 'is_active'];

        // Use DataTableService to handle the request
        return $this->dataTableService->handle(
            $model,
            $columns,
            'getForumModeratorsTable', // Model method to get data
            'countModerators',
            function($item) {  // Optional data formatter
                // Format or modify data before sending to client (using object syntax)
                $item->assigned_at = $item->assigned_at ?: $item->created_at;
                return $item;
            },
            [$forumId] // Pass forum ID as additional parameter
        );
    }

    //======== Helper Methods ==========

    /**
     * Revoke moderator permissions
     */
    public function revokeModerator($moderatorId)
    {
        try {
            $moderatorModel = new ForumModerator();
            $notes = $this->request->getPost('notes') ?? '';

            // Find the moderator record
            $moderator = $moderatorModel->find($moderatorId);
            
            if (!$moderator) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Moderator not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Update moderator status
            $data = [
                'is_active' => 0,
                'revoked_at' => date('Y-m-d H:i:s'),
                'revoked_by' => session()->get('user_id'),
                'revocation_notes' => $notes
            ];

            if ($moderatorModel->update($moderatorId, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Moderator has been revoked successfully'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to revoke moderator'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            log_message('error', 'Error revoking moderator: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while revoking moderator'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reactivate moderator permissions
     */
    public function reactivateModerator($moderatorId)
    {
        try {
            $moderatorModel = new ForumModerator();

            // Find the moderator record
            $moderator = $moderatorModel->find($moderatorId);
            
            if (!$moderator) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Moderator not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Update moderator status
            $data = [
                'is_active' => 1,
                'revoked_at' => null,
                'revoked_by' => null,
                'revocation_notes' => null
            ];

            if ($moderatorModel->update($moderatorId, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Moderator has been reactivated successfully'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to reactivate moderator'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            log_message('error', 'Error reactivating moderator: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while reactivating moderator'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove moderator (from add_moderators.php page)
     */
    public function removeModerator()
    {
        try {
            $userId = $this->request->getPost('user_id');
            $forumId = $this->request->getPost('forum_id');
            $notes = $this->request->getPost('notes') ?? '';

            if (!$userId || !$forumId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User ID and Forum ID are required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $moderatorModel = new ForumModerator();

            // Find the moderator record
            $moderator = $moderatorModel->where('user_id', $userId)
                                       ->where('forum_id', $forumId)
                                       ->first();
            
            if (!$moderator) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Moderator not found for this forum'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Soft delete by setting is_active to 0
            $data = [
                'is_active' => 0,
                'revoked_at' => date('Y-m-d H:i:s'),
                'revoked_by' => session()->get('user_id'),
                'revocation_notes' => $notes
            ];

            if ($moderatorModel->update($moderator->id, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Moderator removed successfully'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to remove moderator'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            log_message('error', 'Error removing moderator: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while removing moderator'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //======== Member Management Methods ==========

    /**
     * Get forum members for DataTables
     */
    public function handleGetMembers(string $forumId)
    {
        try {
            log_message('info', 'handleGetMembers called with forumId: ' . $forumId);
            
            $model = model(ForumMember::class);
            $columns = ['id', 'member_name', 'member_profile', 'email', 'joined_at', 'role', 'user_id'];

            // Use DataTableService to handle the request
            $result = $this->dataTableService->handle(
                $model,
                $columns,
                'getForumMembersTable', // Model method to get data
                'countMembers',
                function($item) {  // Optional data formatter
                    // Format or modify data before sending to client (using object syntax)
                    $item->joined_at = $item->joined_at ?: $item->created_at;
                    return $item;
                },
                [$forumId] // Pass forum ID as additional parameter
            );
            
            log_message('info', 'handleGetMembers result: ' . json_encode($result));
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Error in handleGetMembers: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'draw' => $this->request->getPost('draw') ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Promote member to moderator
     */
    public function promoteMember($memberId)
    {
        try {
            $memberModel = new ForumMember();
            $moderatorModel = new ForumModerator();
            $notes = $this->request->getPost('notes') ?? '';

            // Find the member record
            $member = $memberModel->find($memberId);
            
            if (!$member) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Member not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if already a moderator
            $existingModerator = $moderatorModel->where('user_id', $member->user_id)
                                               ->where('forum_id', $member->forum_id)
                                               ->where('is_active', 1)
                                               ->first();
            
            if ($existingModerator) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User is already a moderator for this forum'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Get user details
            $userModel = new \App\Models\SystemUser();
            $user = $userModel->find($member->user_id);

            // Create moderator record
            $moderatorData = [
                'id' => service('uuid')->uuid4()->toString(),
                'forum_id' => $member->forum_id,
                'user_id' => $member->user_id,
                'assigned_by' => session()->get('user_id'),
                'assigned_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'notes' => $notes
            ];

            if ($moderatorModel->insert($moderatorData)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Member has been promoted to moderator successfully'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to promote member'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            log_message('error', 'Error promoting member: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while promoting member: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove member from forum
     */
    public function removeMember($memberId)
    {
        try {
            $memberModel = new ForumMember();
            $notes = $this->request->getPost('notes') ?? '';

            // Find the member record
            $member = $memberModel->find($memberId);
            
            if (!$member) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Member not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Delete the member record (hard delete)
            if ($memberModel->delete($memberId)) {
                // Log the removal
                log_message('info', sprintf(
                    'Member removed from forum. Member ID: %s, Forum ID: %s, Removed by: %s, Notes: %s',
                    $memberId,
                    $member->forum_id,
                    session()->get('user_id'),
                    $notes
                ));

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Member has been removed from the forum successfully'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to remove member'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            log_message('error', 'Error removing member: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while removing member: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Block member from forum
     */
    public function blockMember($memberId)
    {
        try {
            $memberModel = new ForumMember();

            // Find the member record
            $member = $memberModel->find($memberId);
            
            if (!$member) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Member not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Update member to blocked
            if ($memberModel->update($memberId, ['is_blocked' => 1])) {
                log_message('info', sprintf(
                    'Member blocked from forum. Member ID: %s, Forum ID: %s, Blocked by: %s',
                    $memberId,
                    $member->forum_id,
                    session()->get('user_id')
                ));

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Member has been blocked successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to block member'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            log_message('error', 'Error blocking member: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while blocking member: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Unblock member from forum
     */
    public function unblockMember($memberId)
    {
        try {
            $memberModel = new ForumMember();

            // Find the member record
            $member = $memberModel->find($memberId);
            
            if (!$member) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Member not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Update member to unblocked
            if ($memberModel->update($memberId, ['is_blocked' => 0])) {
                log_message('info', sprintf(
                    'Member unblocked from forum. Member ID: %s, Forum ID: %s, Unblocked by: %s',
                    $memberId,
                    $member->forum_id,
                    session()->get('user_id')
                ));

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Member has been unblocked successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to unblock member'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            log_message('error', 'Error unblocking member: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while unblocking member: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function failValidationErrors(array $errors): ResponseInterface
    {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Validation failed. Fix errors in inputs and try again.',
            'errors' => $errors,
            'error_type' => 'validation'
        ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }

    public function updateReportStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        try {
            $reportId = $this->request->getPost('report_id');
            $status = $this->request->getPost('status');
            $actionTaken = $this->request->getPost('action_taken');
            $reviewerId = session()->get('user_id');

            if (!$reportId || !$status) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Report ID and status are required'
                ]);
            }

            $reportModel = model(Report::class);
            
            $updateData = [
                'status' => $status,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => date('Y-m-d H:i:s')
            ];

            if ($actionTaken) {
                $updateData['action_taken'] = $actionTaken;
            }

            if ($reportModel->update($reportId, $updateData)) {
                log_message('info', sprintf(
                    'Report updated. Report ID: %s, Status: %s, Reviewer: %s',
                    $reportId,
                    $status,
                    $reviewerId
                ));

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Report status updated successfully'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update report status'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error updating report status: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating report status'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

// TODO: Implement content purification for discussion content <?= nl2br(esc($content)) ?>