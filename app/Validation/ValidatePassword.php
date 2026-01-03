<?php

namespace App\Validation;


class ValidatePassword
{
    public function validatePassword(string $currentPassword)
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('id', session()->get('id'))->first();

        if (!password_verify($currentPassword, $user['password'])) {
            return false;
        }

        return true;
    }
}
