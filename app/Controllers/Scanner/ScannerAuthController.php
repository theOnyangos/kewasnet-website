<?php

namespace App\Controllers\Scanner;

use App\Controllers\BaseController;
use App\Libraries\Hash;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class ScannerAuthController extends BaseController
{
    public function login()
    {
        if (session()->get('scanner_is_authed') === true) {
            return redirect()->to(base_url('scanner'));
        }

        return view('scanner/login', [
            'title' => 'Ticket Scanner Login',
        ]);
    }

    public function handleLogin()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return $this->failLogin('Email and password are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->failLogin('Please enter a valid email address.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->failLogin('Invalid credentials.');
        }

        // Admin only (matches CIAuth convention: role_id = 1)
        if ((int) ($user['role_id'] ?? 0) !== 1) {
            return $this->failLogin('You are not authorized to access the ticket scanner.');
        }

        // Basic status checks similar to CIAuth
        if (empty($user['email_verified_at'])) {
            return $this->failLogin('Your admin account is not verified.');
        }
        if (!empty($user['deleted_at'])) {
            return $this->failLogin('Your admin account is suspended.');
        }

        if (!Hash::verify($password, (string) ($user['password'] ?? ''))) {
            return $this->failLogin('Invalid credentials.');
        }

        // Create a separate scanner-only session
        session()->regenerate(true);
        session()->set([
            'scanner_is_authed' => true,
            'scanner_user_id' => $user['id'],
            'scanner_user_name' => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['email'] ?? 'Admin'),
            'scanner_logged_in_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('scanner'));
    }

    public function logout()
    {
        session()->remove([
            'scanner_is_authed',
            'scanner_user_id',
            'scanner_user_name',
            'scanner_logged_in_at',
        ]);
        session()->regenerate(true);

        return redirect()->to(base_url('scanner/login'));
    }

    private function failLogin(string $message)
    {
        // Standalone app: keep it simple with flash + redirect
        return redirect()->to(base_url('scanner/login'))->with('error', $message);
    }
}

