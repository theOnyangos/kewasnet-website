<?php

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
    // Events Routes (more specific routes first)
    $routes->get('events', 'FrontendV2\EventsController::index');
    $routes->post('events/process-booking', 'FrontendV2\EventsController::processBooking');
    $routes->post('events/verify-payment', 'FrontendV2\EventsController::verifyPayment');
    $routes->get('events/booking/(:segment)/tickets', 'FrontendV2\EventsController::tickets/$1');
    $routes->get('events/booking/(:segment)/success', 'FrontendV2\EventsController::bookingSuccess/$1');
    $routes->post('events/booking/(:segment)/resend-tickets', 'FrontendV2\EventsController::resendTickets/$1');
    $routes->get('events/ticket/(:segment)/download', 'FrontendV2\EventsController::downloadTicket/$1');
    $routes->get('events/(:segment)/book', 'FrontendV2\EventsController::book/$1');
    $routes->get('events/(:segment)', 'FrontendV2\EventsController::details/$1');
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
