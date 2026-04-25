<?php

// Test routes (only in development)
if (ENVIRONMENT === 'development') {
    $routes->get('tracking-test', 'TrackingTestController::index');
    $routes->get('api-test', 'TrackingTestController::apiTest');
    $routes->get('api/tracking/debug/real-time', 'API\TrackingController::debugRealTime');
    $routes->get('api/tracking/debug/dashboard', 'API\TrackingController::debugDashboard');
}
