<?php

namespace App\Libraries;

class AdminAuth {
    public function __construct() {
        // Load the User model
    }

    // Register a new user
    public static function register($name, $email, $password) {
        $userModel = model('UserModel');
        $hash = new Hash();
        $user = [
            'name' => $name,
            'email' => $email,
            'password' => $hash->make($password)
        ];
        $userModel->insert($user);
        return true;
    }

    // Login a user
    public static function attempt($email, $password) {
        $userModel = model('User');
        $user = $userModel->where('email', $email)->first();
        if ($user) {
            $hash = new Hash();
            if ($hash::verify($password, $user['password'])) {
                $session = session();
                $session->set([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'picture' => $user['picture'],
                    'bio' => $user['bio'],
                    'isLoggedIn' => true,
                    'isAdmin' => true
                ]);
                return true;
            } else {
                // Password does not match
                return false;
            }
        } else {
            return false;
        }
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        $session = session();
        if ($session->has('isLoggedIn') && $session->get('isLoggedIn') === true && $session->get('isAdmin') === true) {
            return true;
        } else {
            return false;
        }
    }

    // Get the logged in user
    public static function user() {
        $session = session();
        if ($session->has('isLoggedIn') && $session->get('isLoggedIn') === true) {
            $userModel = model('UserModel');
            $user = $userModel->find($session->get('id'));
            return $user;
        } else {
            return false;
        }
    }

    // Logout a user
    public static function logout() {
        $session = session();
        $session->destroy();
        return true;
    }

}
