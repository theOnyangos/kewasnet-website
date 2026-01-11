<?php

namespace App\Controllers\FrontendV2;

use CodeIgniter\Files\File;
use App\Models\FileAttachment;
use App\Models\DiscussionView;
use App\Models\Like;
use App\Controllers\BaseController;
use App\Exceptions\ValidationException;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\ClientDiscussionService;
use CodeIgniter\Exceptions\PageNotFoundException;

class DiscussionController extends BaseController
{
    protected ClientDiscussionService $discussionService;
    protected FileAttachment $fileAttachmentModel;
    protected DiscussionView $discussionViewModel;
    protected Like $replyLikeModel;

    public function __construct()
    {
        $this->discussionService = new ClientDiscussionService();
        $this->fileAttachmentModel = new FileAttachment();
        $this->discussionViewModel = new DiscussionView();
        $this->replyLikeModel = new Like();
    }

    public function viewDiscussion(string $discussionSlug)
    {
        // Fetch discussion details using the service
        $discussion = $this->discussionService->getDiscussionBySlug($discussionSlug);

        if (!$discussion || $discussion->status !== 'active') {
            throw new PageNotFoundException('Discussion not found');
        }

        // Track view using session-based tracking (simpler and more intuitive)
        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Check if this user/session has already viewed this discussion
        $viewedDiscussions = session()->get('viewed_discussions') ?? [];
        
        if (!in_array($discussion->id, $viewedDiscussions)) {
            // Mark as viewed in session
            $viewedDiscussions[] = $discussion->id;
            session()->set('viewed_discussions', $viewedDiscussions);
            
            // Record the view in tracking table
            $ipAddress = $this->request->getIPAddress();
            $userAgent = $this->request->getUserAgent()->getAgentString();
            
            $this->discussionViewModel->recordView(
                $discussion->id,
                $userId,
                $ipAddress,
                $userAgent
            );
            
            // Increment the discussion view count
            $this->discussionService->incrementViews($discussion->id);
            
            log_message('info', "Discussion {$discussion->id} view incremented for " . ($userId ?? 'guest'));
        } else {
            log_message('info', "Discussion {$discussion->id} already viewed in this session");
        }

        // Fetch attachments for this discussion
        $attachmentModel = new \App\Models\FileAttachment();
        $attachments = $attachmentModel->getAttachmentsFor('discussion', $discussion->id);
        
        // Add attachment URLs
        foreach ($attachments as &$attachment) {
            $attachment['download_url'] = base_url("ksp/discussion/download-attachment/" . $attachment['file_path']);
            $attachment['view_url'] = base_url("ksp/discussion/view-attachment/" . $attachment['file_path']);
        }
        
        // Add attachments to discussion object
        $discussion->attachments = $attachments;

        $replies    = $this->discussionService->getRepliesWithUserInfo($discussion->id);
        $author     = $this->discussionService->getDiscussionAuthorInformation($discussion->id);

        // Fetch forum moderators
        $forumModeratorModel = new \App\Models\ForumModerator();
        $moderators = $forumModeratorModel->getModeratorsByForumId($discussion->forum_id);

        // Get forum details to get the slug
        $forumModel = model('Forum');
        $forum = $forumModel->find($discussion->forum_id);

        // Check if user is a member of the forum
        $forumMemberModel = new \App\Models\ForumMember();
        $isMember = false;
        $isBlocked = false;
        $isBookmarked = false;
        if ($userId) {
            $membership = $forumMemberModel->where('forum_id', $discussion->forum_id)
                                          ->where('user_id', $userId)
                                          ->first();
            $isMember = !empty($membership);
            $isBlocked = $membership && $membership->is_blocked == 1;
            
            // Check if discussion is bookmarked
            $bookmarkModel = new \App\Models\Bookmark();
            $bookmark = $bookmarkModel->where('user_id', $userId)
                                     ->where('discussion_id', $discussion->id)
                                     ->first();
            $isBookmarked = !empty($bookmark);
        }

        $data = [
            'title'         => $discussion->title,
            'description'   => 'You are viewing the discussion: ' . $discussion->title,
            'replies'       => $replies['replies'] ?? [],
            'nestedReplies' => $replies['nestedReplies'] ?? [],
            'discussion'    => $discussion,
            'author'        => $author,
            'userId'        => $userId,
            'isLocked'      => $discussion->is_locked,
            'forumId'       => $discussion->forum_id,
            'moderators'    => $moderators,
            'isMember'      => $isMember,
            'isBlocked'     => $isBlocked,
            'isBookmarked'  => $isBookmarked,
            'forumSlug'     => $forum->slug ?? '',
            'forumName'     => $forum->name ?? 'Forum'
        ];

        return view('frontendV2/ksp/pages/network-corner/discussions/index', $data);
    }

    public function addReply()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $discussionId = $this->request->getPost('discussion_id');
        $content      = $this->request->getPost('content');
        $parentId     = $this->request->getPost('parent_id');
        $files        = $this->request->getFiles();

        // Check if user is logged in
        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'You must be logged in to reply'
            ]);
        }

        // Get discussion to find forum_id
        $discussionModel = model('Discussion');
        $discussion = $discussionModel->find($discussionId);
        
        if (!$discussion) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Discussion not found'
            ]);
        }

        // Check if user is a member of the forum
        $forumMemberModel = new \App\Models\ForumMember();
        $membership = $forumMemberModel->where('forum_id', $discussion->forum_id)
                                      ->where('user_id', $userId)
                                      ->first();
        
        if (!$membership) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You must be a member of this forum to post replies. Please join the forum first.'
            ]);
        }
        
        // Check if user is blocked
        if ($membership->is_blocked == 1) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You have been blocked from participating in this forum'
            ]);
        }

        $result = $this->discussionService->addReply($discussionId, $content, $parentId, $files);

        // Send notifications after successful reply
        if (isset($result['success']) && $result['success'] === true) {
            try {
                $notificationService = new \App\Services\NotificationService();
                $userModel = model('UserModel');
                $replierUser = $userModel->find($userId);
                $replierName = $replierUser ? ($replierUser['first_name'] . ' ' . $replierUser['last_name']) : 'Someone';

                // Truncate content for preview (first 100 characters)
                $replyPreview = !empty($content) ? substr(strip_tags($content), 0, 100) . (strlen($content) > 100 ? '...' : '') : '';

                // Notify discussion author if it's not the user replying to their own discussion
                if ($discussion->user_id != $userId) {
                    $notificationService->notifyDiscussionReply($discussion->user_id, $discussion->title, $replierName, $discussionId, $replyPreview);
                }

                // If this is a reply to a reply, notify the parent reply author
                if (!empty($parentId)) {
                    $replyModel = model('Reply');
                    $parentReply = $replyModel->find($parentId);
                    if ($parentReply && $parentReply->user_id != $userId && $parentReply->user_id != $discussion->user_id) {
                        // Only notify if it's not the discussion author and not the replier themselves
                        $notificationService->notifyReplyReply($parentReply->user_id, $replierName, $discussionId);
                    }
                }
            } catch (\Exception $notificationError) {
                log_message('error', "Error sending discussion reply notifications: " . $notificationError->getMessage());
                // Don't fail reply if notification fails
            }
        }

        return $this->response->setJSON($result);
    }

    public function downloadAttachment()
    {
        // Get authenticated user
        $userId = session()->get('id') ?? session()->get('user_id');
        
        if (!$userId) {
            throw new PageNotFoundException('Access denied');
        }

        // Always get file path from URI segments
        $uri = service('uri');
        $segmentCount = $uri->getTotalSegments();
        $pathParts = [];
        
        // Start from segment 4 (ksp = 1, discussion = 2, download-attachment = 3, path starts at 4)
        for ($i = 4; $i <= $segmentCount; $i++) {
            $pathParts[] = $uri->getSegment($i);
        }
        
        $filePath = implode('/', $pathParts);
        log_message('debug', 'downloadAttachment - Constructed file path: ' . $filePath);
        
        // Find attachment record to get original name and check membership
        $attachment = $this->fileAttachmentModel->where('file_path', $filePath)->first();
        
        if (!$attachment) {
            log_message('error', 'Attachment not found in database: ' . $filePath);
            throw new PageNotFoundException('Attachment not found');
        }
        
        // Get the discussion to check forum membership
        $discussionModel = new \App\Models\Discussion();
        $discussion = null;
        
        // Check if attachment belongs to a discussion or a reply
        if ($attachment['attachable_type'] === 'discussion') {
            $discussion = $discussionModel->find($attachment['attachable_id']);
        } elseif ($attachment['attachable_type'] === 'reply') {
            // Get the discussion from the reply
            $replyModel = new \App\Models\Reply();
            $reply = $replyModel->find($attachment['attachable_id']);
            if ($reply) {
                $discussion = $discussionModel->find($reply->discussion_id);
            }
        }
        
        if (!$discussion) {
            log_message('error', 'Discussion not found for attachment: ' . $filePath);
            throw new PageNotFoundException('Discussion not found');
        }
        
        // Check if user is a member of the forum
        $forumMemberModel = new \App\Models\ForumMember();
        $membership = $forumMemberModel->where('forum_id', $discussion->forum_id)
                                      ->where('user_id', $userId)
                                      ->first();
        
        if (!$membership) {
            log_message('warning', 'User ' . $userId . ' attempted to download attachment without forum membership');
            return redirect()->back()->with('error', 'You must be a member of this forum to download attachments.');
        }
        
        // Add uploads prefix like FilesController does
        $fullPath = WRITEPATH . 'uploads/' . $filePath;
        log_message('debug', 'downloadAttachment - Full file path: ' . $fullPath);

        if (!file_exists($fullPath)) {
            log_message('error', 'File not found: ' . $fullPath);
            throw new PageNotFoundException('File not found');
        }

        // Ensure it's a file and not a directory
        if (!is_file($fullPath)) {
            log_message('error', 'Path is not a file: ' . $fullPath);
            throw new PageNotFoundException('Invalid file path');
        }

        $originalName = $attachment['original_name'] ?? basename($filePath);

        // Increment download count
        $this->fileAttachmentModel->incrementDownloadCount($attachment['id']);

        return $this->response->download($fullPath, null)->setFileName($originalName);
    }

    public function viewAttachment()
    {
        // Get authenticated user
        $userId = session()->get('id') ?? session()->get('user_id');
        
        if (!$userId) {
            throw new PageNotFoundException('Access denied');
        }

        // Always get file path from URI segments
        $uri = service('uri');
        $segmentCount = $uri->getTotalSegments();
        
        $pathParts = [];
        
        // Start from segment 4 (ksp = 1, discussion = 2, view-attachment = 3, path starts at 4)
        for ($i = 4; $i <= $segmentCount; $i++) {
            $segment = $uri->getSegment($i);
            $pathParts[] = $segment;
        }
        
        $filePath = implode('/', $pathParts);
        log_message('debug', 'viewAttachment - Constructed file path: ' . $filePath);
        
        // Get the attachment from database to find which discussion it belongs to
        $attachment = $this->fileAttachmentModel->where('file_path', $filePath)->first();
        
        if (!$attachment) {
            log_message('error', 'Attachment not found in database: ' . $filePath);
            throw new PageNotFoundException('Attachment not found');
        }
        
        // Get the discussion to check forum membership
        $discussionModel = new \App\Models\Discussion();
        $discussion = null;
        
        // Check if attachment belongs to a discussion or a reply
        if ($attachment['attachable_type'] === 'discussion') {
            $discussion = $discussionModel->find($attachment['attachable_id']);
        } elseif ($attachment['attachable_type'] === 'reply') {
            // Get the discussion from the reply
            $replyModel = new \App\Models\Reply();
            $reply = $replyModel->find($attachment['attachable_id']);
            if ($reply) {
                $discussion = $discussionModel->find($reply->discussion_id);
            }
        }
        
        if (!$discussion) {
            log_message('error', 'Discussion not found for attachment: ' . $filePath);
            throw new PageNotFoundException('Discussion not found');
        }
        
        // Check if user is a member of the forum
        $forumMemberModel = new \App\Models\ForumMember();
        $membership = $forumMemberModel->where('forum_id', $discussion->forum_id)
                                      ->where('user_id', $userId)
                                      ->first();
        
        if (!$membership) {
            log_message('warning', 'User ' . $userId . ' attempted to access attachment without forum membership');
            return redirect()->back()->with('error', 'You must be a member of this forum to view attachments.');
        }
        
        // Add uploads prefix like FilesController does
        $fullPath = WRITEPATH . 'uploads/' . $filePath;
        log_message('debug', 'viewAttachment - Full file path: ' . $fullPath);

        if (!file_exists($fullPath)) {
            log_message('error', 'File not found: ' . $fullPath);
            throw new PageNotFoundException('File not found at: ' . $fullPath);
        }

        // Ensure it's a file and not a directory
        if (!is_file($fullPath)) {
            log_message('error', 'Path is not a file: ' . $fullPath);
            throw new PageNotFoundException('Invalid file path: ' . $fullPath);
        }

        $file = new File($fullPath);
        $mime = $file->getMimeType();

        return $this->response->setContentType($mime)
                            ->setBody(file_get_contents($fullPath));
    }

    public function likeReply()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $replyId = $this->request->getPost('reply_id');
        $userId = session()->get('id') ?? session()->get('user_id');
        
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to like replies'
            ]);
        }
        
        // Get reply to find discussion and forum
        $replyModel = model('Reply');
        $reply = $replyModel->find($replyId);
        
        if (!$reply) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Reply not found'
            ]);
        }
        
        // Get discussion to find forum_id
        $discussionModel = model('Discussion');
        $discussion = $discussionModel->find($reply->discussion_id);
        
        if (!$discussion) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Discussion not found'
            ]);
        }
        
        // Check if user is a member and not blocked
        $forumMemberModel = new \App\Models\ForumMember();
        $membership = $forumMemberModel->where('forum_id', $discussion->forum_id)
                                      ->where('user_id', $userId)
                                      ->first();
        
        if (!$membership) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You must be a member of this forum to like replies'
            ]);
        }
        
        if ($membership->is_blocked == 1) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You have been blocked from participating in this forum'
            ]);
        }
        
        // Check if user already liked this reply
        $existingLike = $this->replyLikeModel->where([
            'likeable_type' => 'reply',
            'likeable_id' => $replyId,
            'user_id' => $userId
        ])->first();
        
        if ($existingLike) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You already liked this reply'
            ]);
        }
        
        // Add like
        $this->replyLikeModel->insert([
            'likeable_type' => 'reply',
            'likeable_id' => $replyId,
            'user_id' => $userId
        ]);
        
        // Update reply like count
        $replyModel->set('like_count', 'like_count + 1', false)
                   ->where('id', $replyId)
                   ->update();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Reply liked successfully'
        ]);
    }

    public function markBestAnswer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $replyId = $this->request->getPost('reply_id');
        $discussionId = $this->request->getPost('discussion_id');
        $userId = session()->get('id') ?? session()->get('user_id');
        
        // Verify user owns the discussion
        $discussion = $this->discussionModel->find($discussionId);
        if (!$discussion || $discussion->user_id !== $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only discussion owner can mark best answer'
            ]);
        }
        
        // Check if user is a member and not blocked
        $forumMemberModel = new \App\Models\ForumMember();
        $membership = $forumMemberModel->where('forum_id', $discussion->forum_id)
                                      ->where('user_id', $userId)
                                      ->first();
        
        if (!$membership) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You must be a member of this forum to mark best answer'
            ]);
        }
        
        if ($membership->is_blocked == 1) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You have been blocked from participating in this forum'
            ]);
        }
        
        // Remove previous best answer if any
        $this->replyModel->where('discussion_id', $discussionId)
                        ->set('is_best_answer', false)
                        ->update();
        
        // Mark this reply as best answer
        $this->replyModel->update($replyId, [
            'is_best_answer' => true
        ]);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Marked as best answer successfully'
        ]);
    }

    public function likeDiscussion()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $discussionId = $this->request->getPost('discussion_id');
        $userId = session()->get('id') ?? session()->get('user_id');
        
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to like discussions'
            ]);
        }
        
        // Get discussion to check forum membership
        $discussionModel = model('Discussion');
        $discussion = $discussionModel->find($discussionId);
        
        if (!$discussion) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Discussion not found'
            ]);
        }
        
        // Check if user is a member and not blocked
        $forumMemberModel = new \App\Models\ForumMember();
        $membership = $forumMemberModel->where('forum_id', $discussion->forum_id)
                                      ->where('user_id', $userId)
                                      ->first();
        
        if (!$membership) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You must be a member of this forum to like discussions'
            ]);
        }
        
        if ($membership->is_blocked == 1) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You have been blocked from participating in this forum'
            ]);
        }
        
        // Check if user already liked this discussion
        $existingLike = $this->replyLikeModel->where([
            'likeable_type' => 'discussion',
            'likeable_id' => $discussionId,
            'user_id' => $userId
        ])->first();
        
        if ($existingLike) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You already liked this discussion'
            ]);
        }
        
        // Add like
        $this->replyLikeModel->insert([
            'likeable_type' => 'discussion',
            'likeable_id' => $discussionId,
            'user_id' => $userId
        ]);
        
        // Update discussion like count
        $discussionModel->set('like_count', 'like_count + 1', false)
                        ->where('id', $discussionId)
                        ->update();

        // Send notification to discussion author (if it's not the user liking their own discussion)
        try {
            if ($discussion->user_id != $userId) {
                $notificationService = new \App\Services\NotificationService();
                $userModel = model('UserModel');
                $likerUser = $userModel->find($userId);
                $likerName = $likerUser ? ($likerUser['first_name'] . ' ' . $likerUser['last_name']) : 'Someone';

                // Get forum name for notification
                $forumModel = model('Forum');
                $forum = $forumModel->find($discussion->forum_id);
                $forumName = $forum ? $forum->name : '';

                $notificationService->notifyDiscussionLiked($discussion->user_id, $discussion->title, $likerName, $discussionId, $forumName);
            }
        } catch (\Exception $notificationError) {
            log_message('error', "Error sending discussion like notification: " . $notificationError->getMessage());
            // Don't fail like if notification fails
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Discussion liked successfully'
        ]);
    }

    public function createDiscussion()
    {
        // Validate request
        if (!$this->request->isAJAX()) {
            return $this->fail('Only AJAX requests are allowed', 400);
        }
        
        try {
            // Get form data
            $data   = $this->request->getPost();
            $files  = $this->request->getFiles();
            
            // Create discussion
            $result = $this->discussionService->createDiscussion($data, $files);

            // log_message('debug', 'Discussion created: ' . json_encode($result, JSON_PRETTY_PRINT));

            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => 'Discussion: ' . $result['discussion']->title . ' created successfully',
                'redirect'  => base_url('ksp/discussion/' . $result['discussion']->slug . '/view')
            ]);
            
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

    protected function failValidationErrors(array $errors): ResponseInterface
    {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Validation failed. Fix errors in inputs and try again.',
            'errors' => $errors,
            'error_type' => 'validation'
        ])->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function contactModerators()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        // Get logged in user
        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to contact moderators'
            ]);
        }

        // Get JSON input
        $input = $this->request->getJSON(true);
        
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'subject' => 'required|min_length[3]|max_length[200]',
            'message' => 'required|min_length[10]|max_length[2000]',
            'moderator_emails' => 'required',
            'discussion_id' => 'required|numeric',
            'discussion_title' => 'required'
        ]);

        if (!$validation->run($input)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please fill all required fields correctly',
                'errors' => $validation->getErrors()
            ]);
        }

        // Get user information
        $userModel = new \App\Models\User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Prepare and queue email
        $emailQueueModel = new \App\Models\EmailQueue();
        
        // Ensure moderator emails is an array
        $recipientsList = is_array($input['moderator_emails']) ? $input['moderator_emails'] : [$input['moderator_emails']];
        
        $subject = '[Forum Contact] ' . $input['subject'];
        
        // Email body
        $emailBody = view('emails/contact_moderators', [
            'userName' => $user['first_name'] . ' ' . $user['last_name'],
            'userEmail' => $user['email'],
            'subject' => $input['subject'],
            'message' => $input['message'],
            'discussionTitle' => $input['discussion_title'],
            'discussionUrl' => base_url('ksp/discussion/' . $input['discussion_id'])
        ]);
        
        $fromEmail = env('EMAIL_FROM_ADDRESS');
        $fromName = env('EMAIL_FROM_NAME');

        // Queue email with BCC to all moderators
        if ($emailQueueModel->queueEmail($fromEmail, $subject, $emailBody, $recipientsList, $fromEmail, $fromName)) {
            // Send notifications after successful email queuing
            try {
                // Get forum ID from discussion
                $discussionModel = model('Discussion');
                $discussion = $discussionModel->find($input['discussion_id']);
                $forumId = $discussion ? $discussion->forum_id : null;

                if ($forumId) {
                    $notificationService = new \App\Services\NotificationService();
                    $userName = $user['first_name'] . ' ' . $user['last_name'];

                    // Get forum name
                    $forumModel = model('Forum');
                    $forum = $forumModel->find($forumId);
                    $forumName = $forum ? $forum->name : 'Forum';

                    // Notify user about confirmation
                    $notificationService->notifyModeratorContactConfirmation($userId, $forumName);

                    // Notify moderators about contact
                    $forumModeratorModel = new \App\Models\ForumModerator();
                    $moderators = $forumModeratorModel->getModeratorsByForumId($forumId);
                    if (!empty($moderators)) {
                        $moderatorIds = array_column($moderators, 'user_id');
                        $notificationService->notifyModeratorsContact($moderatorIds, $userName, $forumName, $forumId);
                    }
                }
            } catch (\Exception $notificationError) {
                log_message('error', "Error sending moderator contact notifications: " . $notificationError->getMessage());
                // Don't fail email queuing if notification fails
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Your message has been queued and will be sent to the moderators'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to queue message. Please try again later.'
            ]);
        }
    }

    public function reportUser()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $userId = session()->get('id') ?? session()->get('user_id');
        
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to report a user'
            ]);
        }

        $input = $this->request->getJSON(true);

        // Validation rules
        $rules = [
            'reported_user_id' => 'required',
            'reason' => 'required|max_length[100]',
            'description' => 'permit_empty|max_length[500]',
            'context_type' => 'required|in_list[discussion,reply]',
            'context_id' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Get forum ID from context to check if user is blocked
        $forumId = null;
        if ($input['context_type'] === 'discussion') {
            $discussionModel = model('Discussion');
            $discussion = $discussionModel->find($input['context_id']);
            if ($discussion) {
                $forumId = $discussion->forum_id;
            }
        } elseif ($input['context_type'] === 'reply') {
            $replyModel = model('Reply');
            $reply = $replyModel->find($input['context_id']);
            if ($reply) {
                $discussionModel = model('Discussion');
                $discussion = $discussionModel->find($reply->discussion_id);
                if ($discussion) {
                    $forumId = $discussion->forum_id;
                }
            }
        }

        // Check if user is blocked from the forum
        if ($forumId) {
            $forumMemberModel = new \App\Models\ForumMember();
            $membership = $forumMemberModel->where('forum_id', $forumId)
                                          ->where('user_id', $userId)
                                          ->first();
            
            if ($membership && $membership->is_blocked == 1) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'You have been blocked from participating in this forum'
                ]);
            }
        }

        // Check if user has already reported this user in this context
        $reportModel = new \App\Models\Report();
        $existingReport = $reportModel->where('reporter_id', $userId)
                                     ->where('reportable_type', 'user')
                                     ->where('reportable_id', $input['reported_user_id'])
                                     ->where('status', 'pending')
                                     ->first();

        if ($existingReport) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You have already reported this user. Please wait for moderation review.'
            ]);
        }

        // Create the report
        $reportData = [
            'reporter_id' => $userId,
            'reportable_type' => 'user',
            'reportable_id' => $input['reported_user_id'],
            'reason' => $input['reason'],
            'description' => $input['description'] ?? null,
            'status' => 'pending'
        ];

        $reportId = $reportModel->insert($reportData);
        if ($reportId) {
            // Log the report for admin review
            log_message('info', "User {$userId} reported user {$input['reported_user_id']} for: {$input['reason']}");

            // Queue email notification to the reported user
            try {
                $userModel = model('UserModel');
                $reportedUser = $userModel->find($input['reported_user_id']);
                
                if ($reportedUser && isset($reportedUser['email'])) {
                    $emailData = [
                        'reported_user_name' => $reportedUser['first_name'] . ' ' . $reportedUser['last_name'],
                        'reporter_name' => 'Anonymous Member',
                        'reason' => $input['reason'],
                        'description' => $input['description'] ?? 'No additional details provided',
                        'context_type' => $input['context_type'],
                        'report_date' => date('F j, Y \a\t g:i a')
                    ];
                    
                    $subject = 'You Have Been Reported - KEWASNET Forum';
                    $message = view('emails/user_reported_notification', $emailData);
                    
                    // Add email to queue
                    $emailQueue = model('EmailQueue');
                    $queued = $emailQueue->queueEmail(
                        $reportedUser['email'],
                        $subject,
                        $message,
                        null,
                        getenv('EMAIL_FROM_ADDRESS'),
                        getenv('EMAIL_FROM_NAME')
                    );
                    
                    if ($queued) {
                        log_message('info', "Report notification email queued for user {$input['reported_user_id']}");
                    } else {
                        log_message('error', "Failed to queue report notification email for user {$input['reported_user_id']}");
                    }
                }
            } catch (\Exception $e) {
                log_message('error', "Error queuing report notification email: " . $e->getMessage());
                // Don't fail the report submission if email queueing fails
            }

            // Notify admins about the new report
            try {
                $userModel = model('UserModel');
                $reporter = $userModel->find($userId);
                $reportedUser = $userModel->find($input['reported_user_id']);
                
                if ($reporter && $reportedUser) {
                    $reporterName = $reporter['first_name'] . ' ' . $reporter['last_name'];
                    $reportedUserName = $reportedUser['first_name'] . ' ' . $reportedUser['last_name'];
                    
                    // Get all admin users
                    $adminUsers = $userModel->getAdministrators();
                    
                    // Send notification to each admin
                    $notificationService = new \App\Services\NotificationService();
                    foreach ($adminUsers as $admin) {
                        $notificationService->notifyForumReport(
                            $admin['id'],
                            $reporterName,
                            $reportedUserName,
                            $input['reason'],
                            $reportId
                        );
                    }
                }
            } catch (\Exception $e) {
                log_message('error', "Error sending admin notification for forum report: " . $e->getMessage());
                // Don't fail the report submission if notification fails
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User reported successfully. Moderators will review your report.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to submit report. Please try again.'
            ]);
        }
    }

    /**
     * Toggle bookmark for a discussion
     */
    public function toggleBookmark()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'You must be logged in to bookmark discussions'
            ]);
        }

        $discussionId = $this->request->getPost('discussion_id');
        
        if (!$discussionId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Discussion ID is required'
            ]);
        }

        $bookmarkModel = new \App\Models\Bookmark();
        
        // Check if already bookmarked
        $existing = $bookmarkModel->where('user_id', $userId)
                                 ->where('discussion_id', $discussionId)
                                 ->first();

        if ($existing) {
            // Remove bookmark
            $bookmarkModel->delete($existing->id);
            
            return $this->response->setJSON([
                'success' => true,
                'bookmarked' => false,
                'message' => 'Bookmark removed'
            ]);
        } else {
            // Add bookmark
            $data = [
                'user_id' => $userId,
                'discussion_id' => $discussionId
            ];
            
            if ($bookmarkModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'bookmarked' => true,
                    'message' => 'Discussion bookmarked'
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to bookmark discussion'
                ]);
            }
        }
    }
}
