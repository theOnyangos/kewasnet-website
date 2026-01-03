<?php

namespace App\Libraries;

use Google\Client;
use Google\Service\Oauth2;
use App\Models\GoogleSettings;

class GoogleAuth {
    
    private $client;

    public function __construct() {
        $GoogleSettings = self::getGoogleSettings();

        $this->client = new Client();
        $this->client->setApplicationName('Login to ' . $GoogleSettings['application_name']);
        $this->client->setClientId($GoogleSettings['client_id']);
        $this->client->setClientSecret($GoogleSettings['client_secret']);
        $this->client->setRedirectUri($GoogleSettings['redirect_uri']);
        $this->client->setScopes('email');
    }

    public function getAuthUrl() {
        return $this->client->createAuthUrl();
    }

    public function checkRedirectCode() {
        if (isset($_GET['code'])) {
            $this->client->authenticate($_GET['code']);
            $token = $this->client->getAccessToken();
            $this->setToken($token);
            return true;
        }
        return false;
    }

    public function setToken($token) {
        $session = session();

        $sessionData = [
            'google_access_token' => $token
        ];

        $session->set($sessionData);

        $this->client->setAccessToken($token);
    }

    public function logout() {
        $session = session();
        $session->remove('google_access_token');
    }

    public function isLoggedIn() {
        $session = session();
        return isset($session->google_access_token) && $session->google_access_token ? true : false;
    }

    public function getPayload() {
        if ($this->isLoggedIn()) {
            $oauth2 = new Oauth2($this->client);
            return $oauth2->userinfo->get();
        }
        return false;
    }

    private static function getGoogleSettings() {
        $googleSettings = ( new GoogleSettings())->find(1);
        return $googleSettings;
    }
}
