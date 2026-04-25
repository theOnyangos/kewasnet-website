<?php

// Standalone Ticket Scanner App (separate auth/session from backend/frontend)
$routes->group('scanner', static function ($routes) {
    // Public (login)
    $routes->get('login', 'Scanner\ScannerAuthController::login');
    $routes->post('login', 'Scanner\ScannerAuthController::handleLogin');

    // Protected (scanner session)
    $routes->group('', ['filter' => 'scannerAuth'], static function ($routes) {
        $routes->get('', 'Scanner\ScannerController::index');
        $routes->post('logout', 'Scanner\ScannerAuthController::logout');

        // Data endpoints used by scanner UI
        $routes->get('events', 'Scanner\ScannerController::events');
        $routes->post('verify', 'Scanner\ScannerController::verify');
        $routes->post('decode-image', 'Scanner\ScannerController::decodeImage');
    });
});
