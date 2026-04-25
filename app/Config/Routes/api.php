<?php

// Activity Tracking API Routes (No authentication required for tracking)
$routes->group('api/tracking', static function ($routes) {
    $routes->post('init-session', 'API\TrackingController::initSession');
    $routes->post('track-page', 'API\TrackingController::trackPage');
    $routes->post('update-page', 'API\TrackingController::updatePage');
    $routes->post('track-event', 'API\TrackingController::trackEvent');
    $routes->post('batch-track', 'API\TrackingController::batchTrack');
    $routes->get('debug-status', 'API\TrackingController::debugStatus');

    // Admin routes (authentication handled in controller)
    $routes->group('admin', static function ($routes) {
        $routes->get('dashboard', 'API\TrackingController::dashboard');
        $routes->get('real-time', 'API\TrackingController::realTime');
        $routes->post('activities-datatable', 'API\TrackingController::activitiesDataTable');
    });
});

// AI Agent API routes (authentication handled in controller)
$routes->group('api/ai', static function ($routes) {
    $routes->post('chat', 'API\AIAgentController::chat');
    $routes->get('conversations', 'API\AIAgentController::getConversations');
    $routes->get('conversations/(:segment)', 'API\AIAgentController::getConversation');
    $routes->delete('conversations/(:segment)', 'API\AIAgentController::deleteConversation');
    $routes->post('conversations/(:segment)/regenerate', 'API\AIAgentController::regenerate');
});
