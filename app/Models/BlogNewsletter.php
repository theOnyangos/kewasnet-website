<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class BlogNewsletter extends Model
{
    protected $table = 'blog_newsletter_subscriptions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['id', 'email', 'token', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $beforeInsert = ['generateUUID', 'generateToken'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    protected function generateToken(array $data)
    {
        if (!isset($data['data']['token'])) {
            $data['data']['token'] = bin2hex(random_bytes(32));
        }
        return $data;
    }


    public function subscribe($email)
    {
        // Check if email already exists
        $existing = $this->where('email', $email)->first();

        if ($existing) {
            if (!$existing['is_active']) {
                // Reactivate existing subscription
                $result = $this->update($existing['id'], [
                    'is_active' => true,
                    'token' => bin2hex(random_bytes(32))
                ]);
                
                if ($result) {
                    return [
                        'success' => true,
                        'message' => 'Welcome back! Your subscription has been reactivated.',
                        'data' => $existing
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to reactivate your subscription. Please try again.'
                    ];
                }
            }
            return [
                'success' => false,
                'message' => 'This email is already subscribed to our newsletter.'
            ];
        }

        // Create new subscription
        $result = $this->insert([
            'email' => $email,
            'is_active' => true
        ]);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!',
                'data' => [
                    'id' => $result,
                    'email' => $email
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to subscribe. Please try again.'
            ];
        }
    }

    public function unsubscribe($token)
    {
        $subscription = $this->where('token', $token)->first();

        if ($subscription && $subscription['is_active']) {
            return $this->update($subscription['id'], [
                'is_active' => false
            ]);
        }

        return false;
    }

    public function verifySubscription($token)
    {
        return $this->where('token', $token)
                   ->where('is_active', true)
                   ->first();
    }

    public function getActiveSubscribers()
    {
        return $this->where('is_active', true)->findAll();
    }

    public function getSubscriptionByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getNewslettersTable($start, $length, $search = null, $orderBy = 'created_at', $orderDir = 'DESC')
    {
        $builder = $this->builder()->select('id, email, is_active, created_at, updated_at')
                        ->where('1=1'); // Dummy condition for easier chaining

        // Apply search filter
        if ($search) {
            $builder->like('email', $search);
        }

        // Apply ordering
        $builder->orderBy($orderBy, $orderDir);
        
        // Apply pagination
        $builder->limit($length, $start);
        
        return $builder->get()->getResultArray(); // Use getResultArray to ensure arrays are returned
    }

    public function countNewsletters(): int
    {
        // Return count of all newsletter subscriptions
        return $this->countAllResults();
    }

    public function getNewsletterStats()
    {
        $totalSubscribers = $this->countAllResults();
        $activeSubscribers = $this->where('is_active', true)->countAllResults();
        $inactiveSubscribers = $totalSubscribers - $activeSubscribers;
        
        // Calculate subscription trends (last 30 days)
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        $recentSubscriptions = $this->where('created_at >=', $thirtyDaysAgo)->countAllResults();
        
        // Get total campaigns sent from newsletters table
        $totalCampaigns = $this->db->table('newsletters')
            ->where('status', 'sent')
            ->where('deleted_at', null)
            ->countAllResults();
        
        // Get average open and click rates
        $campaignStats = $this->db->table('newsletters')
            ->select('AVG(open_rate) as avg_open_rate, AVG(click_rate) as avg_click_rate')
            ->where('status', 'sent')
            ->where('deleted_at', null)
            ->get()
            ->getRow();
        
        return (object) [
            'total_newsletters' => $totalSubscribers, // Total subscriptions
            'total_subscribers' => $activeSubscribers,
            'inactive_subscribers' => $inactiveSubscribers,
            'recent_subscriptions' => $recentSubscriptions,
            'total_campaigns' => $totalCampaigns,
            'avg_open_rate' => round($campaignStats->avg_open_rate ?? 0, 2),
            'avg_click_rate' => round($campaignStats->avg_click_rate ?? 0, 2),
            'recent_campaigns' => 0 // Will need to add time-based query for this
        ];
    }
}