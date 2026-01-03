<?php

namespace App\Controllers\FrontendV2;

use App\Models\UserModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\NetworkCornerService;

class NetworkCornerController extends BaseController
{
    protected $db;
    protected $session;
    protected $userModel;
    protected $networkCornerService;

    // Initialize Data
    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();
        $this->networkCornerService = new NetworkCornerService();
        $this->db = \Config\Database::connect();
    }

    // Load the networking corner page
    public function index()
    {
        $title          = "KEWASNET - Welcome to Networking Corner";
        $description    = "Connect with peers and share insights.";

        $recentForums = $this->networkCornerService->getRecentForums(6); // Limit to 6 forums
        $recentDiscussions = $this->getDiscussions();

        $data = [
            'title'             => $title,
            'description'       => $description,
            'recentForums'      => $recentForums,
            'recentDiscussions' => $recentDiscussions
        ];

        return view('frontendV2/ksp/pages/network-corner/landing/index', $data);
    }

    // Load the forums page with filters
    public function forums()
    {
        $title          = "KEWASNET - Join the Discussion Forums";
        $description    = "Explore various topics and engage with the community.";

        // Get filter parameters
        $search = $this->request->getGet('search') ?? '';
        $sortBy = $this->request->getGet('sort') ?? 'latest';

        // Get forum model
        $forumModel = model('Forum');

        // Build filtered query
        $builder = $forumModel->select('forums.*, COUNT(DISTINCT discussions.id) as discussion_count')
                ->join('discussions', 'discussions.forum_id = forums.id AND discussions.status = "active"', 'left')
                ->groupBy('forums.id');

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('forums.name', $search)
                    ->orLike('forums.description', $search)
                    ->groupEnd();
        }

        // Apply sorting
        switch ($sortBy) {
            case 'name':
                $builder->orderBy('forums.name', 'ASC');
                break;
            case 'popular':
                $builder->orderBy('discussion_count', 'DESC');
                break;
            case 'oldest':
                $builder->orderBy('forums.created_at', 'ASC');
                break;
            default: // latest
                $builder->orderBy('forums.created_at', 'DESC');
        }

        // Paginate results
        $forums = $builder->paginate(12);
        $pager = $forumModel->pager;

        $data = [
            'title'         => $title,
            'description'   => $description,
            'forums'        => $forums,
            'pager'         => $pager,
            'search'        => $search,
            'sortBy'        => $sortBy
        ];

        return view('frontendV2/ksp/pages/network-corner/forums/list', $data);
    }

    // Load the discussions page
    public function discussions()
    {
        $title          = "KEWASNET - Engage in Discussions";
        $description    = "Join the conversation and share your thoughts.";

        // Get filter parameters
        $search = $this->request->getGet('search') ?? '';
        $forum = $this->request->getGet('forum') ?? '';
        $sortBy = $this->request->getGet('sort') ?? 'latest';
        
        $discussionModel = model('App\Models\Discussion');
        
        // Build query
        $builder = $discussionModel->select('discussions.id, discussions.slug, discussions.title, discussions.content, discussions.reply_count, discussions.like_count, discussions.view_count, discussions.created_at, CONCAT(first_name, " ", last_name) as posted_by, forums.name as forum_name, forums.id as forum_id, system_users.picture as user_avatar')
                    ->join('system_users', 'system_users.id = discussions.user_id', 'left')
                    ->join('forums', 'forums.id = discussions.forum_id', 'left')
                    ->where('discussions.status', 'active')
                    ->groupBy('discussions.id');
        
        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('discussions.title', $search)
                    ->orLike('discussions.content', $search)
                    ->groupEnd();
        }
        
        // Apply forum filter
        if (!empty($forum)) {
            $builder->where('forums.id', $forum);
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'popular':
                $builder->orderBy('discussions.like_count', 'DESC');
                break;
            case 'mostReplies':
                $builder->orderBy('discussions.reply_count', 'DESC');
                break;
            case 'mostViewed':
                $builder->orderBy('discussions.view_count', 'DESC');
                break;
            case 'latest':
            default:
                $builder->orderBy('discussions.created_at', 'DESC');
                break;
        }
        
        // Paginate results
        $discussions = $builder->paginate(12, 'default');
        $pager = $discussionModel->pager;
        
        // Get all forums for filter dropdown
        $forums = $this->networkCornerService->getAllForums();

        $data = [
            'title'         => $title,
            'description'   => $description,
            'discussions'   => $discussions,
            'pager'         => $pager,
            'forums'        => $forums,
            'search'        => $search,
            'selectedForum' => $forum,
            'sortBy'        => $sortBy
        ];

        return view('frontendV2/ksp/pages/network-corner/discussions/list', $data);
    }
    
    // Load a discussion forum page
    public function discussionForum($slug)
    {
        try {
            // Set reasonable time and memory limits
            set_time_limit(30);
            ini_set('memory_limit', '256M');

            // Get forum
            $forum = $this->networkCornerService->getForumBySlug($slug);

            if (!$forum) {
                throw new \Exception('Forum not found');
            }

            // log_message('info', 'Fetched forum: ' . json_encode($forum, JSON_PRETTY_PRINT));

            $forumDiscussionCount   = $this->networkCornerService->getForumDiscussionCount($forum->id);
            $member                 = $this->networkCornerService->checkUserMembership($forum->id, session()->get('id'));
            $moderators             = $this->networkCornerService->getForumModerators($forum->id);
            $moderator              = $this->networkCornerService->checkUserIsModerator($forum->id, session()->get('id'));
            $discussions            = $this->networkCornerService->getForumDiscussions($forum->id);
            $paginatedDiscussions   = $this->networkCornerService->getForumClientDiscussionByForumId($forum->id);
            $forums                 = $this->networkCornerService->getAllForums();

            // Check bookmark status for each discussion
            $userId = session()->get('id');
            $bookmarkModel = new \App\Models\Bookmark();
            
            if ($userId && !empty($paginatedDiscussions['discussions'])) {
                foreach ($paginatedDiscussions['discussions'] as $discussion) {
                    $bookmark = $bookmarkModel->where('user_id', $userId)
                                             ->where('discussion_id', $discussion->id)
                                             ->first();
                    $discussion->isBookmarked = !empty($bookmark);
                }
            }

            $title          = "KEWASNET - " . ($forum->name ?? 'Forum Details');
            $description    = "Details for Forum ID: {$forum->description}";
            $forum          = $forum;

            // Extract moderator emails for contact form
            $moderatorEmails = [];
            if (!empty($moderators)) {
                foreach ($moderators as $moderator) {
                    if (!empty($moderator->moderator_email)) {
                        $moderatorEmails[] = $moderator->moderator_email;
                    }
                }
            }

            $data = [
                'title'                 => $title,
                'description'           => $description,
                'forum'                 => $forum,
                'forumDiscussionCount'  => $forumDiscussionCount,
                'member'                => $member,
                'moderators'            => $moderators,
                'moderatorEmails'       => $moderatorEmails,
                'moderator'             => $moderator,
                'discussions'           => $discussions,
                'paginatedDiscussions'  => $paginatedDiscussions,
                'forums'                => $forums
            ];

            return view('frontendV2/ksp/pages/network-corner/forums/details', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching article details: ' . $e->getMessage());
            
            // Return 404 or error page
            $data = [
                'title'       => "KEWASNET - Forum Not Found",
                'description' => "The requested forum could not be found.",
                'error'       => 'Forum not found'
            ];
            
            return view('frontendV2/ksp/pages/network-corner/forums/details', $data);
        }
    }

    // Function to join forum
    public function joinForum()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $forumId = $this->request->getPost('forum_id');
            $userId  = session()->get('id');

            // Validate input
            if (empty($forumId) || empty($userId)) {
                return $this->generateJsonResponse('error', ResponseInterface::HTTP_BAD_REQUEST, 'Invalid input: Please ensure you are in the right forum.', []);
            }

            // Check user membership
            $member = $this->networkCornerService->checkUserMembership($forumId, $userId);
            if ($member['is_member']) {
                return $this->generateJsonResponse('error', ResponseInterface::HTTP_FORBIDDEN, 'You are already a member of this forum.', []);
            }

            // Check if forum exists
            $forum = $this->networkCornerService->getForumById($forumId);
            if (!$forum) {
                return $this->generateJsonResponse('error', ResponseInterface::HTTP_NOT_FOUND, 'Forum not found', []);
            }

            // Join the forum
            $this->networkCornerService->joinForum($forumId, $userId);

            return $this->generateJsonResponse('success', ResponseInterface::HTTP_OK, 'Successfully joined forum: ' . esc($forum->name), []);
        } catch (\Exception $e) {
            return $this->generateJsonResponse('error', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), [], $e);
        }
    }

    // Function to Leave Forum
    public function leaveForum()
    {
        if (!$this->isValidAjaxRequest()) return $this->respondToNonAjax();

        try {
            $forumId = $this->request->getPost('forum_id');
            $userId  = session()->get('id');

            // Validate input
            if (empty($forumId) || empty($userId)) {
                return $this->generateJsonResponse('error', ResponseInterface::HTTP_BAD_REQUEST, 'Invalid input: Please ensure you are in the right forum.', []);
            }

            // Check if forum exists
            $forum = $this->networkCornerService->getForumById($forumId);
            if (!$forum) {
                return $this->generateJsonResponse('error', ResponseInterface::HTTP_NOT_FOUND, 'Forum not found', []);
            }

            // Leave the forum
            $this->networkCornerService->leaveForum($forumId, $userId);

            return $this->generateJsonResponse('success', ResponseInterface::HTTP_OK, 'Successfully left forum: ' . esc($forum->name), []);
        } catch (\Exception $e) {
            return $this->generateJsonResponse('error', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), [], $e);
        }
    }

    // Create a Discussion
    public function createDiscussion()
    {
        $this->respondToAjax();

        try {
            $isValid = $this->validate($this->validateDiscussionForm());

            if (!$isValid) {
                return $this->response->setJSON([
                    'status'        => "error",
                    'status_code'   => ResponseInterface::HTTP_BAD_REQUEST,
                    'errors'        => $this->validator->getErrors(),
                    'message'       => 'Please fix some errors in your input fields to continue.'
                ]);
            }

            return $this->generateJsonResponse('error', ResponseInterface::HTTP_NOT_FOUND, 'Some message!', []);
        } catch (\Throwable $th) {
            return $this->generateJsonResponse('error', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), [], $e);
        }
    }

    // Get Discussion Forums
    public function getForums()
    {
        $this->respondToNonAjax();

        try {
            return $this->generateJsonResponse('error', ResponseInterface::HTTP_NOT_FOUND, 'Some message!', []);
        } catch (\Exception $e) {
            return $this->generateJsonResponse('error', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), [], $e);
        }
    }

    // Get Discussion for a Forum
    public function getDiscussions()
    {
        $discussionModel = model('App\Models\Discussion');
        $discussions = $discussionModel->select('discussions.id, discussions.slug, discussions.title, discussions.content, discussions.reply_count, discussions.created_at, CONCAT(first_name, " ", last_name) as posted_by, forums.name as forum_name, system_users.picture as user_avatar')
                    ->join('system_users', 'system_users.id = discussions.user_id', 'left')
                    ->join('forums', 'forums.id = discussions.forum_id', 'left')
                    ->orderBy('discussions.created_at', 'DESC')
                    ->groupBy('discussions.id')
                    ->findAll(10);
        return $discussions;
    }

    private function validateDiscussionForm()
    {
        return [
            'topic' => [
                'rules'     => 'required',
                'errors'    => []
            ],
            'forum' => [
                'rules'     => 'required',
                'errors'    => []
            ],
            'discussion' => [
                'rules'     => 'required',
                'errors'    => []
            ],
            'tags'=> [
                'rules'     => 'required',
                'errors'    => []
            ],
        ];
    }

    public function contactModerators()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $userId = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to contact moderators'
            ]);
        }

        $input = $this->request->getJSON(true);
        
        // Log the input for debugging
        log_message('info', 'Contact moderators input: ' . json_encode($input));
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'subject' => 'required|min_length[3]|max_length[200]',
            'message' => 'required|min_length[10]|max_length[2000]',
            'moderator_emails' => 'required',
            'forum_id' => 'required'
        ]);

        if (!$validation->run($input)) {
            log_message('error', 'Validation errors: ' . json_encode($validation->getErrors()));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please fill all required fields correctly',
                'errors' => $validation->getErrors()
            ]);
        }

        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Log moderator emails for debugging
        log_message('info', 'Moderator emails to send to: ' . json_encode($input['moderator_emails']));
        
        // Convert to array if not already
        $recipientsList = is_array($input['moderator_emails']) ? $input['moderator_emails'] : [$input['moderator_emails']];
        
        // Build email content
        $emailBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: #f5f5f5;
                    margin: 0;
                    padding: 0;
                }
                .email-container {
                    max-width: 600px;
                    margin: 20px auto;
                    background-color: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }
                .email-header {
                    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
                    color: #ffffff;
                    padding: 30px 20px;
                    text-align: center;
                }
                .email-header h2 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .email-body {
                    padding: 30px 20px;
                }
                .info-box {
                    background-color: #f8fafc;
                    border-left: 4px solid #3B82F6;
                    padding: 15px;
                    margin-bottom: 20px;
                    border-radius: 4px;
                }
                .info-item {
                    margin: 10px 0;
                }
                .info-label {
                    font-weight: 600;
                    color: #1e293b;
                    display: inline-block;
                    min-width: 80px;
                }
                .info-value {
                    color: #475569;
                }
                .message-box {
                    background-color: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 6px;
                    padding: 20px;
                    margin: 20px 0;
                }
                .message-label {
                    font-weight: 600;
                    color: #1e293b;
                    margin-bottom: 10px;
                    display: block;
                }
                .message-content {
                    color: #334155;
                    line-height: 1.8;
                }
                .email-footer {
                    background-color: #f8fafc;
                    padding: 20px;
                    text-align: center;
                    border-top: 1px solid #e2e8f0;
                }
                .email-footer p {
                    margin: 5px 0;
                    color: #64748b;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <h2>Forum Moderator Contact</h2>
                </div>
                <div class='email-body'>
                    <div class='info-box'>
                        <div class='info-item'>
                            <span class='info-label'>From:</span>
                            <span class='info-value'>{$user['first_name']} {$user['last_name']}</span>
                        </div>
                        <div class='info-item'>
                            <span class='info-label'>Email:</span>
                            <span class='info-value'>{$user['email']}</span>
                        </div>
                        <div class='info-item'>
                            <span class='info-label'>Subject:</span>
                            <span class='info-value'>{$input['subject']}</span>
                        </div>
                    </div>
                    
                    <div class='message-box'>
                        <span class='message-label'>Message:</span>
                        <div class='message-content'>" . nl2br(htmlspecialchars($input['message'])) . "</div>
                    </div>
                </div>
                <div class='email-footer'>
                    <p>This message was sent via the KEWASNET forum contact form.</p>
                    <p>&copy; " . date('Y') . " KEWASNET. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Queue the email instead of sending immediately
        $emailQueueModel = new \App\Models\EmailQueue();
        $queued = $emailQueueModel->queueEmail(
            env('EMAIL_FROM_ADDRESS'),
            '[Forum Contact] ' . $input['subject'],
            $emailBody,
            $recipientsList,
            env('EMAIL_FROM_ADDRESS'),
            env('EMAIL_FROM_NAME')
        );

        if ($queued) {
            log_message('info', 'Email queued successfully for moderators (BCC): ' . implode(', ', $recipientsList));
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Your message has been sent to the moderators'
            ]);
        } else {
            log_message('error', 'Failed to send moderator contact email: ' . $email->printDebugger(['headers']));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to send message. Please try again later.'
            ]);
        }
    }

    protected function isValidAjaxRequest(): bool
    {
        return $this->request->isAJAX() && $this->request->getPost(csrf_token());
    }
}
