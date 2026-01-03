<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\NotificationService;
use App\Models\UserModel;

class CreateTestNotification extends BaseCommand
{
    protected $group       = 'Notifications';
    protected $name        = 'notification:test';
    protected $description = 'Create a test notification for the current admin user';

    public function run(array $params)
    {
        $notificationService = new NotificationService();
        $userModel = new UserModel();

        // Get the first admin user
        $admin = $userModel->where('role', 'admin')->first();

        if (!$admin) {
            CLI::error('No admin user found in the database.');
            CLI::write('Please create an admin user first.');
            return;
        }

        CLI::write('Creating test notification for user: ' . $admin['first_name'] . ' ' . $admin['last_name'], 'yellow');

        try {
            // Create a test notification
            $notificationService->create(
                $admin['id'],
                'This is a test notification to verify the notification system is working correctly.',
                [
                    'type' => 'info',
                    'title' => 'Test Notification',
                    'icon' => 'bell',
                    'action_url' => base_url('auth/notifications'),
                    'action_text' => 'View All Notifications',
                ]
            );

            CLI::write('✓ Test notification created successfully!', 'green');
            CLI::write('User ID: ' . $admin['id']);
            CLI::write('Log in to the dashboard to see the notification.');

        } catch (\Exception $e) {
            CLI::error('Failed to create test notification: ' . $e->getMessage());
        }
    }
}
