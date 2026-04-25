<?php

// Guest routes (only accessible when NOT logged in)
$routes->group('', ['filter' => 'auth:guest,/ksp'], static function ($routes) {
    // KSP Auth Pages
    $routes->group('ksp', static function ($routes) {
        $routes->get('', 'FrontendV2\KspController::index');
        $routes->get('login', 'FrontendV2\KspController::login');
        $routes->get('signup', 'FrontendV2\KspController::signup');
        $routes->get('pillars', 'FrontendV2\PillarController::index');
        $routes->get('pillars/articles', 'FrontendV2\PillarController::articles');
        $routes->get('pillars/(:segment)', 'FrontendV2\PillarController::pillarView/$1');
        $routes->get('test-pillar/(:segment)', 'FrontendV2\TestController::pillarTest/$1');
        $routes->post('login', 'FrontendV2\KspController::handleClientLogin');
        $routes->get('verify_account', 'FrontendV2\KspController::verifyAccount');
        $routes->get('forget-password', 'FrontendV2\KspController::forgetPassword');
        $routes->post('signup', 'FrontendV2\KspController::handleClientRegistration');
        $routes->get('networking-corner', 'FrontendV2\NetworkCornerController::index');
        $routes->get('verify-reset-code', 'FrontendV2\KspController::verifyResetCode');
        $routes->get('change-password', 'FrontendV2\KspController::updateUserPassword');
        $routes->post('send-reset-code', 'FrontendV2\KspController::handleSendResetCode');
        $routes->post('verify-reset-code', 'FrontendV2\KspController::handleVerifyResetCode');
        $routes->post('update-password', 'FrontendV2\KspController::handleUpdateUserPassword');

        // Download and view attachments - MUST be before pillar-article route to avoid conflicts
        $routes->get('attachments/download/(.*)', 'FrontendV2\PillarController::downloadAttachment/$1');
        $routes->get('attachments/view/(.*)', 'FrontendV2\PillarController::viewAttachment/$1');
        // Download attachments (for discussions - keep original)
        $routes->get('discussions/attachments/download/(.*)', 'FrontendV2\DiscussionController::downloadAttachment/$1');
        $routes->get('discussions/attachments/view/(.*)', 'FrontendV2\DiscussionController::viewAttachment/$1');

        $routes->get('pillar-articles/(:segment)', 'FrontendV2\PillarController::pillarArticles/$1');
        $routes->get('pillar-article/(:segment)', 'FrontendV2\PillarController::pillarArticleDetails/$1');

        // Comment and voting routes (AJAX)
        $routes->post('api/comment/add', 'FrontendV2\PillarController::addComment');
        $routes->post('api/vote/resource-helpful', 'FrontendV2\PillarController::voteResourceHelpful');
        $routes->post('api/vote/comment-helpful', 'FrontendV2\PillarController::voteCommentHelpful');
        $routes->post('api/resource/toggle-bookmark', 'FrontendV2\PillarController::toggleBookmark');
    });
});

// Public Learning Hub Routes (accessible to everyone, no authentication required)
$routes->group('ksp', static function ($routes) {
    // Events Routes (Public)
    $routes->group('events', static function ($routes) {
        $routes->get('', 'FrontendV2\EventsController::index');
        $routes->get('(:segment)', 'FrontendV2\EventsController::details/$1');
        $routes->get('(:segment)/book', 'FrontendV2\EventsController::book/$1');
        $routes->post('process-booking', 'FrontendV2\EventsController::processBooking');
        $routes->post('verify-payment', 'FrontendV2\EventsController::verifyPayment');
        $routes->get('booking/(:segment)/tickets', 'FrontendV2\EventsController::tickets/$1');
        $routes->get('booking/(:segment)/success', 'FrontendV2\EventsController::bookingSuccess/$1');
        $routes->get('ticket/(:segment)/download', 'FrontendV2\EventsController::downloadTicket/$1');
        $routes->post('booking/(:segment)/resend-tickets', 'FrontendV2\EventsController::resendTickets/$1');
    });

    $routes->group('learning-hub', static function ($routes) {
        $routes->get('course/reviews', 'FrontendV2\LearningHubController::getCourseReviews');
        $routes->get('/', 'FrontendV2\LearningHubController::index');
        $routes->get('courses', 'FrontendV2\LearningHubController::courses');
        $routes->get('course/(:segment)', 'FrontendV2\LearningHubController::courseDetails/$1');
    });
});

// Authenticated KSP User routes
$routes->group('', ['filter' => 'auth:auth,/ksp/login'], static function ($routes) {
    $routes->group('ksp', static function ($routes) {
        $routes->get('logout', 'BackendV2\AuthController::handleClientLogout');
        $routes->get('dashboard', 'FrontendV2\LearningHubController::dashboard');

        // Authenticated Learning Hub Routes
        $routes->group('learning-hub', static function ($routes) {
            $routes->post('enroll', 'FrontendV2\LearningHubController::enroll');
            $routes->get('my-courses', 'FrontendV2\LearningHubController::myCourses');
            $routes->get('learn/(:segment)', 'FrontendV2\LearningHubController::coursePlayer/$1');
            $routes->get('lecture/(:segment)/(:segment)', 'FrontendV2\LearningHubController::lecture/$1/$2');
            $routes->post('lecture/(:segment)/(:segment)/complete', 'FrontendV2\LearningHubController::markLectureComplete/$1/$2');
            $routes->get('quiz/(:segment)/(:segment)', 'FrontendV2\LearningHubController::quiz/$1/$2');
            $routes->post('quiz/submit', 'FrontendV2\LearningHubController::submitQuiz');
            $routes->get('certificates', 'FrontendV2\LearningHubController::certificates');
            $routes->get('certificate/(:segment)', 'FrontendV2\LearningHubController::viewCertificate/$1');
            $routes->get('certificate/(:segment)/download', 'FrontendV2\LearningHubController::downloadCertificate/$1');
            $routes->get('attachment/(:segment)', 'FrontendV2\LearningHubController::downloadAttachment/$1');
            $routes->get('profile', 'FrontendV2\LearningHubController::profile');
            $routes->post('profile/update', 'FrontendV2\LearningHubController::updateProfile');
            $routes->post('profile/change-password', 'FrontendV2\LearningHubController::changePassword');
            $routes->post('course/review', 'FrontendV2\LearningHubController::submitCourseReview');
        });

        // Debug Routes (development only)
        $routes->get('debug/payment', 'FrontendV2\DebugController::paymentDebug');

        // Payment Routes
        $routes->post('payment/initiate', 'FrontendV2\PaymentController::initiatePayment');
        $routes->get('payment/callback', 'FrontendV2\PaymentController::paymentCallback');
        $routes->post('payment/verify', 'FrontendV2\PaymentController::verifyPayment');

        // Q&A Routes
        $routes->post('course/question/ask', 'FrontendV2\CourseQuestionController::askQuestion');
        $routes->get('course/(:num)/questions', 'FrontendV2\CourseQuestionController::getQuestions/$1');
        $routes->post('question/(:num)/reply', 'FrontendV2\CourseQuestionController::replyToQuestion/$1');
        $routes->post('question/(:num)/resolve', 'FrontendV2\CourseQuestionController::markAsResolved/$1');

        // Pillar Routes

        // Networking Corner
        $routes->post('join-forum', 'FrontendV2\NetworkCornerController::joinForum');
        $routes->post('leave-forum', 'FrontendV2\NetworkCornerController::leaveForum');
        $routes->post('forum/contact-moderators', 'FrontendV2\NetworkCornerController::contactModerators');
        $routes->get('networking-corner/forums', 'FrontendV2\NetworkCornerController::forums');
        $routes->get('networking-corner-discussions', 'FrontendV2\NetworkCornerController::discussions');
        $routes->get('networking-corner-forum-discussion/(:segment)', 'FrontendV2\NetworkCornerController::discussionForum/$1');

        // AJAX endpoints
        $routes->get('get-forums', 'FrontendV2\NetworkCornerController::getForums');
        $routes->get('get-discussions', 'FrontendV2\NetworkCornerController::getDiscussions');

        // Discussion Routes
        $routes->get('discussion/(:segment)/view', 'FrontendV2\DiscussionController::viewDiscussion/$1');
        $routes->post('discussion/add-reply', 'FrontendV2\DiscussionController::addReply');
        $routes->post('discussion/like-reply', 'FrontendV2\DiscussionController::likeReply');
        $routes->post('discussion/like-discussion', 'FrontendV2\DiscussionController::likeDiscussion');
        $routes->post('discussion/mark-best-answer', 'FrontendV2\DiscussionController::markBestAnswer');
        $routes->post('discussion/report-user', 'FrontendV2\DiscussionController::reportUser');
        $routes->post('discussion/toggle-bookmark', 'FrontendV2\DiscussionController::toggleBookmark');
        $routes->get('discussion/download-attachment/(.*)', 'FrontendV2\DiscussionController::downloadAttachment/$1');
        $routes->get('discussion/view-attachment/(.*)', 'FrontendV2\DiscussionController::viewAttachment/$1');
        $routes->get('discussion/reply-attachments/(:segment)', 'FrontendV2\DiscussionController::getReplyAttachments/$1');
        $routes->post('discussion/create', 'FrontendV2\DiscussionController::createDiscussion');
        $routes->post('discussion/contact-moderators', 'FrontendV2\DiscussionController::contactModerators');

        // Resource bookmark route (for authenticated users)
        $routes->post('api/resource/toggle-bookmark', 'FrontendV2\PillarController::toggleBookmark');
        $routes->post('api/resource/publish', 'FrontendV2\PillarController::publishArticle');

        // Notification Routes
        $routes->group('notifications', static function ($routes) {
            $routes->get('', 'FrontendV2\NotificationController::index');
            $routes->post('get', 'FrontendV2\NotificationController::getNotifications');
            $routes->get('get-recent', 'FrontendV2\NotificationController::getRecent');
            $routes->get('unread-count', 'FrontendV2\NotificationController::getUnreadCount');
            $routes->post('(:segment)/mark-read', 'FrontendV2\NotificationController::markAsRead/$1');
            $routes->post('mark-all-read', 'FrontendV2\NotificationController::markAllAsRead');
            $routes->delete('(:segment)', 'FrontendV2\NotificationController::delete/$1');
            $routes->post('clear-all', 'FrontendV2\NotificationController::clearAll');
        });
    });
});
