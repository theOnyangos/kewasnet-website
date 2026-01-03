<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Public routes (no authentication required)
$routes->group('', static function ($routes) {
    // General frontend pages
    $routes->get('/', 'FrontendV2\Home::index');
    $routes->get('news', 'FrontendV2\Home::news');
    $routes->get('faq', 'FrontendV2\Faq::index');
    $routes->get('about', 'FrontendV2\Home::aboutUs');
    $routes->get('cookies', 'FrontendV2\Home::cookies');
    $routes->get('sitemap', 'FrontendV2\Sitemap::view');
    $routes->get('sitemap.xml', 'FrontendV2\Sitemap::index');
    $routes->get('sitemap/generate', 'FrontendV2\Sitemap::generate');
    $routes->get('sitemap/statistics', 'FrontendV2\Sitemap::statistics');
    $routes->get('sitemap/api', 'FrontendV2\Sitemap::api');
    $routes->put('sitemap/update/(:segment)', 'FrontendV2\Sitemap::update/$1');
    $routes->delete('sitemap/delete/(:segment)', 'FrontendV2\Sitemap::delete/$1');
    $routes->get('programs', 'Programs::index');
    $routes->get('programs/(:segment)', 'Programs::detail/$1');
    $routes->get('resources', 'FrontendV2\Home::resources');
    $routes->get('getResourcesByCategory', 'FrontendV2\Home::getResourcesByCategory');
    $routes->post('incrementDownloadCount', 'FrontendV2\Home::incrementDownloadCount');
    $routes->get('contact-us', 'FrontendV2\Home::contactUs');
    $routes->post('contact-us/submit', 'FrontendV2\ContactsController::submitContact');
    $routes->post('newsletter/subscribe', 'BackendV2\BlogsController::subscribeNewsletter');
    $routes->get('opportunities', 'FrontendV2\Home::opportunities');
    $routes->get('opportunities/explore', 'FrontendV2\OpportunitiesController::explore');
    $routes->get('opportunities/(:segment)', 'FrontendV2\OpportunitiesController::view/$1');
    $routes->post('opportunities/apply/(:segment)', 'FrontendV2\OpportunitiesController::apply/$1');
    $routes->get('google-privacy', 'FrontendV2\Home::googlePrivacy');
    $routes->get('terms-of-service', 'FrontendV2\Home::termsOfService');
    $routes->get('privacy-and-policies', 'FrontendV2\Home::privacyAndPolicies');
    $routes->get('news-details/(:segment)', 'FrontendV2\Home::newsDetails/$1');
    $routes->get('best-practices', 'FrontendV2\Home::bestPractices');
    $routes->get('policy-briefs', 'FrontendV2\Home::policyBriefs');
    $routes->get('help-center', 'FrontendV2\Home::helpCenter');
    
    $routes->post('summernote/upload', 'FrontendV2\SummernoteUpload::uploadImage');
    $routes->post('summernote/delete', 'FrontendV2\SummernoteUpload::deleteImage');
    
    $routes->get('faq/get', 'FrontendV2\Faq::handleGetFaqs');
    
    // Test route for debugging
    $routes->get('test-attachments', 'TestController::testAttachments');

    // Download & View Resource URLs
    $routes->get('client/download/download-attachment/(.*)', 'FilesController::downloadAttachment/$1');
    $routes->get('client/view/preview-attachment/(.*)', 'FilesController::viewAttachment/$1');

    $routes->get('api/resources/search', 'FrontendV2\Home::searchResources');
    $routes->post('api/resources/increment-view-count', 'FrontendV2\Home::incrementViewCount');
    $routes->post('api/partners/create', 'BackendV2\PartnersController::createPartner');
});

// Public certificate verification (no auth required)
$routes->get('certificate/verify', 'FrontendV2\CertificateController::verify');
$routes->get('certificate/verify/(:any)', 'FrontendV2\CertificateController::verify/$1');

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
        $routes->get('pillar-articles/(:segment)', 'FrontendV2\PillarController::pillarArticles/$1');
        $routes->get('pillar-article/(:segment)', 'FrontendV2\PillarController::pillarArticleDetails/$1');
        
        // Comment and voting routes (AJAX)
        $routes->post('api/comment/add', 'FrontendV2\PillarController::addComment');
        $routes->post('api/vote/resource-helpful', 'FrontendV2\PillarController::voteResourceHelpful');
        $routes->post('api/vote/comment-helpful', 'FrontendV2\PillarController::voteCommentHelpful');

        // Download attachments
        $routes->get('attachments/download/(:any)', 'FrontendV2\DiscussionController::downloadAttachment/$1');
    });
});

// Authenticated KSP User routes
$routes->group('', ['filter' => 'auth:auth,/ksp/login'], static function ($routes) {
    $routes->group('ksp', static function ($routes) {
        $routes->get('logout', 'BackendV2\AuthController::handleClientLogout');
        $routes->get('dashboard', 'FrontendV2\LearningHubController::dashboard');
        
        // Learning Hub Routes
        $routes->group('learning-hub', static function ($routes) {
            $routes->get('/', 'FrontendV2\LearningHubController::index');
            $routes->get('courses', 'FrontendV2\LearningHubController::courses');
            $routes->get('course/(:segment)', 'FrontendV2\LearningHubController::courseDetails/$1');
            $routes->post('enroll', 'FrontendV2\LearningHubController::enroll');
            $routes->get('my-courses', 'FrontendV2\LearningHubController::myCourses');
            $routes->get('learn/(:segment)', 'FrontendV2\LearningHubController::coursePlayer/$1');
            $routes->get('lecture/(:segment)/(:segment)', 'FrontendV2\LearningHubController::lecture/$1/$2');
            $routes->get('quiz/(:segment)/(:segment)', 'FrontendV2\LearningHubController::quiz/$1/$2');
            $routes->post('quiz/submit', 'FrontendV2\LearningHubController::submitQuiz');
            $routes->get('certificates', 'FrontendV2\LearningHubController::certificates');
            $routes->get('certificate/(:segment)/download', 'FrontendV2\LearningHubController::downloadCertificate/$1');
            $routes->get('attachment/(:segment)', 'FrontendV2\LearningHubController::downloadAttachment/$1');
            $routes->get('profile', 'FrontendV2\LearningHubController::profile');
            $routes->post('profile/update', 'FrontendV2\LearningHubController::updateProfile');
            $routes->post('profile/change-password', 'FrontendV2\LearningHubController::changePassword');
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
    });
});

// Admin authenticated routes
$routes->group('auth', ['filter' => 'auth:auth,/auth/login'], static function ($routes) {
    $routes->get('users', 'BackendV2\UsersController::index');
    $routes->get('blogs', 'BackendV2\BlogsController::index');
    $routes->get('courses', 'BackendV2\CoursesController::index');
    $routes->get('pillars', 'BackendV2\PillarsController::index');
    $routes->get('partners', 'BackendV2\PartnersController::index');
    $routes->get('dashboard', 'BackendV2\DashboardController::index');

    // Notification Routes
    $routes->group('notifications', static function ($routes) {
        $routes->get('', 'BackendV2\NotificationController::index');
        $routes->post('get', 'BackendV2\NotificationController::getNotifications');
        $routes->get('get-recent', 'BackendV2\NotificationController::getRecent');
        $routes->get('unread-count', 'BackendV2\NotificationController::getUnreadCount');
        $routes->post('(:segment)/mark-read', 'BackendV2\NotificationController::markAsRead/$1');
        $routes->post('mark-all-read', 'BackendV2\NotificationController::markAllAsRead');
        $routes->delete('(:segment)', 'BackendV2\NotificationController::delete/$1');
        $routes->post('clear-all', 'BackendV2\NotificationController::clearAll');
    });

    $routes->get('resources', 'BackendV2\ResourcesController::index');
    $routes->get('resources/categories', 'BackendV2\ResourcesController::categories');
    $routes->get('resources/edit/(:segment)', 'BackendV2\ResourcesController::edit/$1');
    $routes->post('resources/get', 'BackendV2\ResourcesController::getResourcesData');
    $routes->post('resources/get-categories', 'BackendV2\ResourcesController::getResourceCategories');
    $routes->post('resources/create', 'BackendV2\ResourcesController::create');
    $routes->post('resources/create-category', 'BackendV2\ResourcesController::createCategory');
    $routes->post('resources/toggle-featured/(:segment)', 'BackendV2\ResourcesController::toggleFeatured/$1');
    $routes->delete('resources/delete/(:segment)', 'BackendV2\ResourcesController::deleteResource/$1');
    $routes->get('resources/get/(:segment)', 'BackendV2\ResourcesController::getResource/$1');
    $routes->post('resources/update/(:segment)', 'BackendV2\ResourcesController::updateResource/$1');
    $routes->get('activity-dashboard', 'BackendV2\ActivityDashboardController::index');
    
    // Settings Panel Routes
    $routes->group('settings', static function ($routes) {
        $routes->get('', 'BackendV2\SettingsController::index');
        $routes->get('social-media', 'BackendV2\SettingsController::socialMedia');
        $routes->get('youtube', 'BackendV2\SettingsController::youtube');
        $routes->get('partners', 'BackendV2\SettingsController::partners');
        $routes->get('payments', 'BackendV2\SettingsController::payments');
        $routes->get('email', 'BackendV2\SettingsController::email');
        $routes->get('sms', 'BackendV2\SettingsController::sms');
        $routes->get('google', 'BackendV2\SettingsController::google');
        $routes->get('sitemap', 'BackendV2\SettingsController::sitemap');
        $routes->get('faqs', 'BackendV2\SettingsController::faqs');

        // Sitemap Endpoints
        $routes->post('generateSitemap', 'BackendV2\SettingsController::generateSitemap');
        $routes->get('getSitemapStatus', 'BackendV2\SettingsController::getSitemapStatus');
        $routes->post('saveSitemapConfig', 'BackendV2\SettingsController::saveSitemapConfig');
        $routes->get('getSitemapConfig', 'BackendV2\SettingsController::getSitemapConfig');

        // Social Endpoints
        $routes->get('get-social-media', 'BackendV2\SettingsController::getSocialMedia');
        $routes->post('save-social-media/(:segment)', 'BackendV2\SettingsController::saveSocialMedia/$1');
        
        // YouTube Endpoints
        $routes->post('youtube-get-links', 'BackendV2\SettingsController::getYouTubeLinks');
        $routes->post('youtube-save', 'BackendV2\SettingsController::saveYouTubeLink');
        $routes->get('youtube/get/(:segment)', 'BackendV2\SettingsController::getYouTubeLink/$1');
        $routes->delete('youtube/delete/(:segment)', 'BackendV2\SettingsController::deleteYouTubeLink/$1');

        // Partner Endpoints
        $routes->post('get-partners', 'BackendV2\SettingsController::getPartners');
        $routes->post('partner-save', 'BackendV2\SettingsController::savePartner');
        $routes->get('partner/get/(:segment)', 'BackendV2\SettingsController::getPartner/$1');
        $routes->delete('partner/delete/(:segment)', 'BackendV2\SettingsController::deletePartner/$1');
    });
        
    // FAQs Settings
    $routes->group('settings', static function ($routes) {
        // FAQ CRUD routes (following partner panel pattern)
        $routes->post('get-faqs', 'BackendV2\SettingsController::getFaqs');
        $routes->post('faq-save', 'BackendV2\SettingsController::saveFaq');
        $routes->get('faq/get/(:segment)', 'BackendV2\SettingsController::getFaq/$1');
        $routes->delete('faq/delete/(:segment)', 'BackendV2\SettingsController::deleteFaq/$1');
    });

    // System Settings Email, Payments, SMS, Google
    $routes->group('settings', static function ($routes) {
        $routes->get('email', 'BackendV2\SystemSettingsController::email');
        $routes->get('payments', 'BackendV2\SystemSettingsController::payments');
        $routes->get('sms', 'BackendV2\SystemSettingsController::sms');
        $routes->get('google', 'BackendV2\SystemSettingsController::google');

        // Payment Settings API Routes
        $routes->get('getPaystackSettings', 'BackendV2\SystemSettingsController::getPaystackSettings');
        $routes->post('savePaystackSettings', 'BackendV2\SystemSettingsController::savePaystackSettings');
        $routes->post('testPaystackConnection', 'BackendV2\SystemSettingsController::testPaystackConnection');
        $routes->get('getMpesaSettings', 'BackendV2\SystemSettingsController::getMpesaSettings');
        $routes->post('saveMpesaSettings', 'BackendV2\SystemSettingsController::saveMpesaSettings');
        $routes->post('testMpesaConnection', 'BackendV2\SystemSettingsController::testMpesaConnection');

        // SMS Settings API Routes
        $routes->get('getSmsSettings', 'BackendV2\SystemSettingsController::getSmsSettings');
        $routes->post('saveSmsSettings', 'BackendV2\SystemSettingsController::saveSmsSettings');
        $routes->post('testSmsConnection', 'BackendV2\SystemSettingsController::testSmsConnection');

        $routes->post('verifyPasswordForSensitiveData', 'BackendV2\SystemSettingsController::verifyPasswordForSensitiveData');
    });

    // Email Settings
    $routes->group('settings', static function ($routes) {
        $routes->get('email', 'BackendV2\EmailSettingController::index');
        $routes->get('getEmailSettings', 'BackendV2\EmailSettingController::getEmailSettings');
        $routes->post('saveEmailSettings', 'BackendV2\EmailSettingController::saveEmailSettings');
        $routes->post('sendTestEmail', 'BackendV2\EmailSettingController::sendTestEmail');
        $routes->post('verifyEmailPasswordForSensitiveData', 'BackendV2\EmailSettingController::verifyPasswordForSensitiveData');
    });

    // Admin Programs Routes
    $routes->group('programs', static function ($routes) {
        $routes->get('', 'BackendV2\ProgramsController::index');
        $routes->get('create', 'BackendV2\ProgramsController::createForm');
        $routes->get('edit/(:segment)', 'BackendV2\ProgramsController::edit/$1');
        $routes->post('getPrograms', 'BackendV2\ProgramsController::getPrograms');
        $routes->get('getStats', 'BackendV2\ProgramsController::getStats');
        $routes->post('create', 'BackendV2\ProgramsController::create');
        $routes->post('update/(:segment)', 'BackendV2\ProgramsController::update/$1');
        $routes->post('toggleStatus/(:segment)', 'BackendV2\ProgramsController::toggleStatus/$1');
        $routes->delete('delete/(:segment)', 'BackendV2\ProgramsController::delete/$1');
    });

    // Admin Forum Routes
    $routes->group('forums', static function ($routes) {
        $routes->get('', 'BackendV2\ForumsController::index');
        $routes->post('get', 'BackendV2\ForumsController::getForums');
        
        // Specific named routes - MUST come before generic (:segment) routes
        $routes->get('create-forum', 'BackendV2\ForumsController::getCreateForum');
        $routes->post('create', 'BackendV2\ForumsController::handleCreateForum');
        $routes->get('reports', 'BackendV2\ForumsController::reports');
        $routes->get('moderators', 'BackendV2\ForumsController::moderators');
        $routes->get('discussions', 'BackendV2\ForumsController::recentDiscussions');

        // POST ROUTES
        $routes->post('discussions/(:segment)/get', 'BackendV2\ForumsController::handleGetDiscussions/$1');
        $routes->post('moderators/(:segment)/get', 'BackendV2\ForumsController::handleGetModerators/$1');
        $routes->post('members/(:segment)/get', 'BackendV2\ForumsController::handleGetMembers/$1');
        
        // Discussion management routes
        $routes->get('discussions/(:segment)/edit', 'BackendV2\ForumsController::editDiscussion/$1');
        $routes->post('discussions/(:segment)/update', 'BackendV2\ForumsController::updateDiscussion/$1');
        $routes->delete('discussions/(:segment)/delete', 'BackendV2\ForumsController::deleteDiscussion/$1');
        $routes->post('attachments/(:segment)/delete', 'BackendV2\ForumsController::deleteAttachment/$1');
        
        // System users and moderator management
        $routes->post('system-users/get', 'BackendV2\ForumsController::getSystemUsers');
        $routes->post('moderators/add', 'BackendV2\ForumsController::handleCreateModerators/$1');
        $routes->post('moderators/revoke/(:segment)', 'BackendV2\ForumsController::revokeModerator/$1');
        $routes->post('moderators/reactivate/(:segment)', 'BackendV2\ForumsController::reactivateModerator/$1');
        $routes->post('moderators/remove', 'BackendV2\ForumsController::removeModerator');
        
        // Member management routes
        $routes->post('members/promote/(:segment)', 'BackendV2\ForumsController::promoteMember/$1');
        $routes->post('members/remove/(:segment)', 'BackendV2\ForumsController::removeMember/$1');
        $routes->post('members/block/(:segment)', 'BackendV2\ForumsController::blockMember/$1');
        $routes->post('members/unblock/(:segment)', 'BackendV2\ForumsController::unblockMember/$1');
        
        // Report management routes
        $routes->post('reports/update-status', 'BackendV2\ForumsController::updateReportStatus');
        
        // Generic (:segment) routes - MUST come LAST
        $routes->patch('(:segment)/update', 'BackendV2\ForumsController::updateForumDetails/$1');
        $routes->get('(:segment)/discussions/create', 'BackendV2\ForumsController::addDiscussions/$1');
        $routes->post('(:segment)/discussions/create', 'BackendV2\ForumsController::handleCreateDiscussions/$1');
        $routes->get('(:segment)/moderators/create', 'BackendV2\ForumsController::addModerators/$1');
        $routes->get('(:segment)/moderators/get', 'BackendV2\ForumsController::handleGetForumModerators/$1');
        $routes->get('(:segment)/edit', 'BackendV2\ForumsController::editForum/$1');
        $routes->get('(:segment)', 'BackendV2\ForumsController::forumDetails/$1');
        
        // Delete routes - keep at bottom
        $routes->delete('(:segment)', 'BackendV2\ForumsController::deleteForum/$1');
        $routes->post('(:segment)/delete', 'BackendV2\ForumsController::deleteForum/$1');
        $routes->post('(:segment)', 'BackendV2\ForumsController::deleteForum/$1');
    });

    // Newsletter Subscribers shortcut route (redirects to blogs/newsletters)
    $routes->get('newsletters/subscribers', static function() {
        return redirect()->to('auth/blogs/newsletters');
    });

    // Admin Blogs Routes
    $routes->group('blogs', static function ($routes) {
        $routes->get('', 'BackendV2\BlogsController::index');
        $routes->get('blogs', 'BackendV2\BlogsController::blogs');
        $routes->get('newsletters', 'BackendV2\BlogsController::newsletters');
        $routes->get('deleted', 'BackendV2\BlogsController::deleted');
        $routes->post('deleted/get', 'BackendV2\BlogsController::getDeletedPosts');
        $routes->post('restore/(:segment)', 'BackendV2\BlogsController::restore/$1');
        $routes->delete('permanent-delete/(:segment)', 'BackendV2\BlogsController::permanentDelete/$1');
        $routes->post('get', 'BackendV2\BlogsController::getBlogPosts');
        $routes->post('get-categories', 'BackendV2\BlogsController::getBlogCategories');
        $routes->post('create-category', 'BackendV2\BlogsController::createCategory');
        $routes->get('get-category/(:segment)', 'BackendV2\BlogsController::getCategory/$1');
        $routes->post('update-category/(:segment)', 'BackendV2\BlogsController::updateCategory/$1');
        $routes->delete('delete-category/(:segment)', 'BackendV2\BlogsController::deleteCategory/$1');

        // Newsletter Subscription routes
        $routes->post('newsletters/get', 'BackendV2\BlogsController::getNewsletters');
        $routes->post('newsletters/reactivate/(:segment)', 'BackendV2\BlogsController::reactivateSubscription/$1');
        $routes->post('newsletters/unsubscribe/(:segment)', 'BackendV2\BlogsController::unsubscribeUser/$1');
        $routes->delete('newsletters/delete/(:segment)', 'BackendV2\BlogsController::deleteSubscriber/$1');

        // Newsletter Management routes
        $routes->get('newsletters/create', 'BackendV2\NewsletterController::create');
        $routes->post('newsletters/create', 'BackendV2\NewsletterController::store');
        $routes->post('newsletters/save-draft', 'BackendV2\NewsletterController::saveDraft');
        $routes->post('newsletters/send-test', 'BackendV2\NewsletterController::sendTest');
        $routes->get('newsletters/sent', 'BackendV2\NewsletterController::sentNewsletters');
        $routes->post('newsletters/data', 'BackendV2\NewsletterController::getNewsletters');
        $routes->get('newsletters/(:segment)/view', 'BackendV2\NewsletterController::view/$1');
        $routes->get('newsletters/(:segment)/edit', 'BackendV2\NewsletterController::edit/$1');
        $routes->post('newsletters/(:segment)/update', 'BackendV2\NewsletterController::update/$1');
        $routes->post('newsletters/(:segment)/send', 'BackendV2\NewsletterController::send/$1');
        $routes->delete('newsletters/(:segment)', 'BackendV2\NewsletterController::delete/$1');

        $routes->get('create', 'BackendV2\BlogsController::create');
        $routes->post('create', 'BackendV2\BlogsController::handleCreateBlog');
        $routes->post('duplicate/(:segment)', 'BackendV2\BlogsController::duplicate/$1');
        $routes->post('toggle-status/(:segment)', 'BackendV2\BlogsController::toggleStatus/$1');
        $routes->get('(:segment)/edit', 'BackendV2\BlogsController::edit/$1');
        $routes->get('(:segment)', 'BackendV2\BlogsController::details/$1');
        $routes->post('(:segment)/edit', 'BackendV2\BlogsController::handleEdit/$1');
        $routes->delete('(:segment)', 'BackendV2\BlogsController::delete/$1');
    });

    // Admin Courses Routes (Learning Hub)
    $routes->group('courses', static function ($routes) {
        $routes->get('', 'BackendV2\CoursesController::index');
        $routes->get('courses', 'BackendV2\CoursesController::courses');
        $routes->get('sections', 'BackendV2\CoursesController::sections');
        $routes->get('lectures', 'BackendV2\CoursesController::lectures');
        $routes->get('enrollments', 'BackendV2\CoursesController::enrollments');
        $routes->get('enrolled-students/(:segment)', 'BackendV2\CoursesController::enrolledStudents/$1');

        // DataTable endpoints
        $routes->post('get-courses', 'BackendV2\CoursesController::getCourses');
        $routes->post('get-enrolled-students/(:segment)', 'BackendV2\CoursesController::getEnrolledStudents/$1');
        $routes->post('get-sections', 'BackendV2\CoursesController::getSections');
        $routes->post('get-lectures', 'BackendV2\CoursesController::getLectures');
        $routes->post('get-enrollments', 'BackendV2\CoursesController::getEnrollments');        $routes->get('get-recent-courses', 'BackendV2\\CoursesController::getRecentCourses');
        $routes->get('get-top-enrolled-courses', 'BackendV2\\CoursesController::getTopEnrolledCourses');
        // CRUD operations
        $routes->get('create', 'BackendV2\CoursesController::create');
        $routes->post('create', 'BackendV2\CoursesController::handleCreateCourse');
        $routes->get('edit/(:segment)', 'BackendV2\CoursesController::edit/$1');
        $routes->post('edit/(:segment)', 'BackendV2\CoursesController::handleUpdateCourse/$1');
        $routes->post('delete/(:segment)', 'BackendV2\CoursesController::deleteCourse/$1');

        // Section management
        $routes->get('sections/get/(:segment)', 'BackendV2\CoursesController::getSection/$1');
        $routes->post('sections/create', 'BackendV2\CoursesController::createSection');
        $routes->post('sections/update/(:segment)', 'BackendV2\CoursesController::updateSection/$1');
        $routes->post('sections/delete/(:segment)', 'BackendV2\CoursesController::deleteSection/$1');

        // Lecture management
        $routes->get('lectures/get/(:segment)', 'BackendV2\CoursesController::getLecture/$1');
        $routes->post('lectures/create', 'BackendV2\CoursesController::createLecture');
        $routes->post('lectures/update/(:segment)', 'BackendV2\CoursesController::updateLecture/$1');
        $routes->post('lectures/delete/(:segment)', 'BackendV2\CoursesController::deleteLecture/$1');
    });

    // Admin Pillars Routes
    $routes->group('pillars', static function ($routes) {
        $routes->get('', 'BackendV2\PillarsController::index');
        $routes->get('resources', 'BackendV2\PillarsController::resources');
        $routes->get('articles', 'BackendV2\PillarsController::articles');
        $routes->get('document-types', 'BackendV2\PillarsController::documentTypes');
        $routes->get('create-resource-category', 'BackendV2\PillarsController::createResourceCategory');
        $routes->post('create-resource-category', 'BackendV2\PillarsController::handleCreateResourceCategory');
        $routes->post('create-document-type', 'BackendV2\PillarsController::handleCreateDocumentType');

        // DataTable endpoints
        $routes->post('get-pillars', 'BackendV2\PillarsController::getPillars');
        $routes->post('get-articles', 'BackendV2\PillarsController::getArticles');
        $routes->post('get-document-types', 'BackendV2\PillarsController::getDocumentTypes');
        $routes->post('get-categories', 'BackendV2\PillarsController::getCategories');
        $routes->get('download-attachment/(.*)', 'FrontendV2\DiscussionController::downloadAttachment/$1');
        
        // Creation routes
        $routes->get('create', 'BackendV2\PillarsController::create');
        $routes->post('create', 'BackendV2\PillarsController::handleCreatePillar');
        $routes->get('create-pillar-article', 'BackendV2\PillarsController::createPillarArticle');
        $routes->post('create-pillar-article', 'BackendV2\PillarsController::handleCreatePillarArticle');
        
        // Action routes
        $routes->post('update-status/(:segment)', 'BackendV2\PillarsController::updatePillarStatus/$1');
        $routes->post('update-article-status/(:segment)', 'BackendV2\PillarsController::updateArticleStatus/$1');
        $routes->delete('delete/(:segment)', 'BackendV2\PillarsController::deletePillar/$1');
        $routes->delete('delete-article/(:segment)', 'BackendV2\PillarsController::deleteArticle/$1');
        $routes->delete('delete-document-type/(:segment)', 'BackendV2\PillarsController::deleteDocumentType/$1');
        $routes->post('duplicate-document-type/(:segment)', 'BackendV2\PillarsController::duplicateDocumentType/$1');
        $routes->get('download/(:segment)', 'BackendV2\PillarsController::downloadArticle/$1');
        $routes->post('delete-category/(:segment)', 'BackendV2\PillarsController::handleDeleteResourceCategory/$1');
        $routes->post('delete-document-type/(:segment)', 'BackendV2\PillarsController::handleDeleteDocumentType/$1');

        // Detail routes (most generic - keep last)
        $routes->get('(:segment)/edit', 'BackendV2\PillarsController::edit/$1');
        $routes->get('(:segment)', 'BackendV2\PillarsController::details/$1');
        $routes->post('(:segment)/edit', 'BackendV2\PillarsController::handleEdit/$1');
    });

    // API endpoints
    $routes->group('users', static function ($routes) {
        $routes->post('get', 'BackendV2\UsersController::getUsersData');
        $routes->get('create', 'BackendV2\UsersController::create');
        $routes->post('create', 'BackendV2\UsersController::createUser');
        $routes->get('view/(:segment)', 'BackendV2\UsersController::view/$1');
        $routes->post('login-activities/(:segment)', 'BackendV2\UsersController::getLoginActivities/$1');
        $routes->get('edit/(:segment)', 'BackendV2\UsersController::edit/$1');
        $routes->post('update/(:segment)', 'BackendV2\UsersController::updateUser/$1');
        $routes->post('change-password/(:segment)', 'BackendV2\UsersController::changePassword/$1');
        $routes->post('reset-password/(:segment)', 'BackendV2\UsersController::resetPassword/$1');
        $routes->post('send-verification/(:segment)', 'BackendV2\UsersController::sendVerificationEmail/$1');
        $routes->post('toggle-status/(:segment)', 'BackendV2\UsersController::toggleAccountStatus/$1');
        $routes->post('upload-profile-image/(:segment)', 'BackendV2\UsersController::uploadProfileImage/$1');
        $routes->delete('remove-profile-image/(:segment)', 'BackendV2\UsersController::removeProfileImage/$1');
        $routes->delete('delete/(:segment)', 'BackendV2\UsersController::deleteUser/$1');
    });

    // Partners Routes
    $routes->group('partners', static function ($routes) {
        $routes->get('create', 'BackendV2\PartnersController::create');
        $routes->get('edit/(:segment)', 'BackendV2\PartnersController::edit/$1');
        $routes->post('get', 'BackendV2\PartnersController::getPartnersData');
        $routes->post('create', 'BackendV2\PartnersController::createPartner');
        $routes->post('update/(:segment)', 'BackendV2\PartnersController::updatePartner/$1');
        $routes->delete('remove-logo/(:segment)', 'BackendV2\PartnersController::removeLogo/$1');
        $routes->delete('delete/(:segment)', 'BackendV2\PartnersController::deletePartner/$1');
    });

    // Resources Routes
    $routes->group('resources', static function ($routes) {
        $routes->get('', 'BackendV2\ResourcesController::index');
        $routes->get('categories', 'BackendV2\ResourcesController::categories');
        $routes->get('create', 'BackendV2\ResourcesController::createPage');
        $routes->get('edit/(:segment)', 'BackendV2\ResourcesController::edit/$1');
        
        $routes->post('get', 'BackendV2\ResourcesController::getResourcesData');
        $routes->post('create', 'BackendV2\ResourcesController::create');
        $routes->post('update/(:segment)', 'BackendV2\ResourcesController::update/$1');
        $routes->delete('delete/(:segment)', 'BackendV2\ResourcesController::delete/$1');
        $routes->post('toggle-featured/(:segment)', 'BackendV2\ResourcesController::toggleFeatured/$1');
        $routes->delete('remove-file/(:segment)', 'BackendV2\ResourcesController::removeFile/$1');
        
        $routes->post('get-categories', 'BackendV2\ResourcesController::getResourceCategories');
        $routes->post('create-category', 'BackendV2\ResourcesController::createResourceCategory');
    });

    // Opportunities Routes
    $routes->group('opportunities', static function ($routes) {
        // Opportunities management
        $routes->get('/', 'BackendV2\OpportunitiesController::index');
        $routes->get('applications', 'BackendV2\OpportunitiesController::applications');
        $routes->get('create', 'BackendV2\OpportunitiesController::create');
        $routes->get('view/(:segment)', 'BackendV2\OpportunitiesController::view/$1');
        $routes->get('edit/(:segment)', 'BackendV2\OpportunitiesController::edit/$1');
        
        // Deleted opportunities (admin only)
        $routes->get('deleted', 'BackendV2\OpportunitiesController::deleted');
        $routes->post('deleted/get', 'BackendV2\OpportunitiesController::getDeletedOpportunities');
        $routes->post('restore/(:segment)', 'BackendV2\OpportunitiesController::restore/$1');
        $routes->delete('permanent-delete/(:segment)', 'BackendV2\OpportunitiesController::permanentDelete/$1');
        
        // API endpoints for opportunities
        $routes->post('get', 'BackendV2\OpportunitiesController::getJobOpportunities');
        $routes->post('create', 'BackendV2\OpportunitiesController::createOpportunity');
        $routes->post('update/(:segment)', 'BackendV2\OpportunitiesController::update/$1');
        $routes->delete('delete/(:segment)', 'BackendV2\OpportunitiesController::delete/$1');
        
        // Applications endpoints
        $routes->post('applications/get', 'BackendV2\OpportunitiesController::getApplications');
        $routes->get('applications/view/(:segment)', 'BackendV2\OpportunitiesController::viewApplication/$1');
        $routes->get('applications/review/(:segment)', 'BackendV2\OpportunitiesController::reviewApplication/$1');
        $routes->get('applications/edit/(:segment)', 'BackendV2\OpportunitiesController::editApplication/$1');
        $routes->post('applications/update-status/(:segment)', 'BackendV2\OpportunitiesController::updateApplicationStatus/$1');
        $routes->delete('applications/delete/(:segment)', 'BackendV2\OpportunitiesController::deleteApplication/$1');
    });
});

// Activity Tracking API Routes (No authentication required for tracking)
$routes->group('api/tracking', static function ($routes) {
    $routes->post('init-session', 'API\TrackingController::initSession');
    $routes->post('track-page', 'API\TrackingController::trackPage');
    $routes->post('update-page', 'API\TrackingController::updatePage');
    $routes->post('track-event', 'API\TrackingController::trackEvent');
    $routes->post('batch-track', 'API\TrackingController::batchTrack');
    
    // Admin routes (authentication handled in controller)
    $routes->group('admin', static function ($routes) {
        $routes->get('dashboard', 'API\TrackingController::dashboard');
        $routes->get('real-time', 'API\TrackingController::realTime');
    });
});

// Test routes (only in development)
if (ENVIRONMENT === 'development') {
    $routes->get('tracking-test', 'TrackingTestController::index');
    $routes->get('api-test', 'TrackingTestController::apiTest');
    $routes->get('api/tracking/debug/real-time', 'API\TrackingController::debugRealTime');
    $routes->get('api/tracking/debug/dashboard', 'API\TrackingController::debugDashboard');
}

// Common auth routes (available regardless of auth state)
$routes->group('auth', ['filter' => 'auth:guest,/auth/dashboard'], static function ($routes) {
    $routes->get('logout', 'BackendV2\AuthController::logoutHandler');
    $routes->get('change-password', 'BackendV2\AuthController::changePassword');
    $routes->get('verify-reset-code', 'BackendV2\AuthController::verifyResetCode');
    $routes->post('verify-otp', 'BackendV2\AuthController::handleVerifyResetCode');
    $routes->post('change-password', 'BackendV2\AuthController::handleUpdateUserPassword');

    // Admin auth pages
    $routes->get('login', 'BackendV2\AuthController::login');
    $routes->post('login', 'BackendV2\AuthController::handleLogin');
    $routes->get('forgot-password', 'BackendV2\AuthController::forgetPassword');
    $routes->post('forgot-password', 'BackendV2\AuthController::handleForgetPassword');
});

