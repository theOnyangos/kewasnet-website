<?php

namespace App\Libraries;

use Facebook\Facebook;

class FacebookAuth 
{
    private $fb;

    public function __construct()
    {
        $this->fb = new Facebook([
            'app_id' => getenv('FACEBOOK_CLIENT_ID'),
            'app_secret' => getenv('FACEBOOK_APP_SECRET'),
            'default_graph_version' => 'v2.10',
        ]);
    }

    public function getAuthUrl()
    {
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl(base_url('auth/fb_profile'), $permissions); // Use the login url provided in the facebook app
        return $loginUrl;
    }

    public function checkRedirectCode()
    {
        $helper = $this->fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            return false;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return false;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
                return false;
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
                return false;
            }
            exit;
        }

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $this->fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId(getenv('FACEBOOK_APP_ID'));
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                return false;
                exit;
            }
        }

        $_SESSION['fb_access_token'] = (string) $accessToken;
        return true;

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->fb->get('/me?fields=id,name,email', $accessToken);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $user = $response->getGraphUser();

        $userData = [
            'profile_pic' => 'https://graph.facebook.com/'.$user['id'].'/picture?type=large',
            'user_name' => $user['name'],
            'email' => $user['email'],
        ];

        return $userData;
    }
}