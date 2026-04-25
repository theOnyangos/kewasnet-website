<?php

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
