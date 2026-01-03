<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class BlogNewsletterSubscriptionsSeeder extends Seeder
{
    public function run()
    {
        $emails = [
            'user1@example.com',
            'user2@example.com',
            'user3@example.com',
            'subscriber@domain.com',
            'test.user@gmail.com',
            'newsletter.fan@outlook.com',
            'wash.enthusiast@yahoo.com'
        ];

        $subscriptions = [];

        foreach ($emails as $email) {
            $subscriptions[] = [
                'id' => Uuid::uuid4()->toString(),
                'email' => $email,
                'token' => bin2hex(random_bytes(32)),
                'is_active' => 1,
                'subscribed_at' => date('Y-m-d H:i:s', strtotime('-'.rand(1, 365).' days')),
                'unsubscribed_at' => null, // Add this field to maintain consistency
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        // Add some inactive subscriptions
        $inactiveEmails = [
            'unsubscribed@example.com',
            'former.user@domain.net'
        ];

        foreach ($inactiveEmails as $email) {
            $subscriptions[] = [
                'id' => Uuid::uuid4()->toString(),
                'email' => $email,
                'token' => bin2hex(random_bytes(32)),
                'is_active' => 0,
                'subscribed_at' => date('Y-m-d H:i:s', strtotime('-'.rand(100, 400).' days')),
                'unsubscribed_at' => date('Y-m-d H:i:s', strtotime('-'.rand(1, 90).' days')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        $this->db->table('blog_newsletter_subscriptions')->insertBatch($subscriptions);
    }
}
