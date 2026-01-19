<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class UserEventModel extends Model
{
    protected $table         = 'user_events';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = false;
    protected $returnType    = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id', 'session_id', 'user_id', 'event_type', 'event_category', 'event_action',
        'event_label', 'event_value', 'page_url', 'occurred_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    // Important: BaseModel only checks for '' (empty string), not null.
    // Setting null causes it to insert an empty column name, leading to SQL errors.
    protected $updatedField  = ''; // Table doesn't have updated_at column

    // Validation
    protected $validationRules = [
        'session_id'   => 'required',
        'event_type'   => 'required|max_length[50]', // More flexible - allow any event type
        'event_action' => 'required|max_length[100]',
        'page_url'     => 'required|max_length[500]',
        'occurred_at'  => 'required|valid_date',
    ];

    protected $beforeInsert = ['addUuid'];

    protected function addUuid(array $data)
    {
        if (!isset($data['data']['id']) || empty($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                // Fallback to database UUID function
                unset($data['data']['id']);
            }
        }
        return $data;
    }

    /**
     * Get event statistics by type
     */
    public function getEventStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('event_type, COUNT(*) as count')
                       ->groupBy('event_type')
                       ->orderBy('count', 'DESC');
        
        if ($startDate && $endDate) {
            $builder->where('occurred_at >=', $startDate)
                   ->where('occurred_at <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Get most clicked elements
     */
    public function getTopClicks($limit = 10, $startDate = null, $endDate = null)
    {
        $builder = $this->select('event_label, COUNT(*) as clicks')
                       ->where('event_type', 'click')
                       ->where('event_label IS NOT NULL')
                       ->groupBy('event_label')
                       ->orderBy('clicks', 'DESC')
                       ->limit($limit);
        
        if ($startDate && $endDate) {
            $builder->where('occurred_at >=', $startDate)
                   ->where('occurred_at <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Get form submission statistics
     */
    public function getFormStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('event_label as form_name, COUNT(*) as submissions')
                       ->where('event_type', 'form_submit')
                       ->groupBy('event_label')
                       ->orderBy('submissions', 'DESC');
        
        if ($startDate && $endDate) {
            $builder->where('occurred_at >=', $startDate)
                   ->where('occurred_at <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Get download statistics
     */
    public function getDownloadStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('event_label as file_name, COUNT(*) as downloads')
                       ->where('event_type', 'download')
                       ->groupBy('event_label')
                       ->orderBy('downloads', 'DESC');
        
        if ($startDate && $endDate) {
            $builder->where('occurred_at >=', $startDate)
                   ->where('occurred_at <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Get search queries
     */
    public function getSearchQueries($limit = 20, $startDate = null, $endDate = null)
    {
        $builder = $this->select('event_value as search_query, COUNT(*) as searches')
                       ->where('event_type', 'search')
                       ->where('event_value IS NOT NULL')
                       ->where('event_value !=', '')
                       ->groupBy('event_value')
                       ->orderBy('searches', 'DESC')
                       ->limit($limit);
        
        if ($startDate && $endDate) {
            $builder->where('occurred_at >=', $startDate)
                   ->where('occurred_at <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Get new user registrations
     */
    public function getNewRegistrations($startDate = null, $endDate = null)
    {
        $builder = $this->select('DATE(occurred_at) as date, COUNT(*) as registrations')
                       ->where('event_type', 'registration')
                       ->groupBy('DATE(occurred_at)')
                       ->orderBy('date', 'DESC');
        
        if ($startDate && $endDate) {
            $builder->where('occurred_at >=', $startDate)
                   ->where('occurred_at <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Get top events by action, type, and category
     */
    public function getTopEvents($limit = 10, $startDate = null, $endDate = null)
    {
        $builder = $this->select('event_action, event_type, event_category, event_label, COUNT(*) as event_count')
                       ->groupBy('event_action, event_type, event_category, event_label')
                       ->orderBy('event_count', 'DESC')
                       ->limit($limit);
        
        if ($startDate && $endDate) {
            $builder->where('occurred_at >=', $startDate)
                   ->where('occurred_at <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Get events by category
     */
    public function getEventsByCategory($startDate = null, $endDate = null)
    {
        $builder = $this->select('event_category, COUNT(*) as count')
                       ->where('event_category IS NOT NULL')
                       ->where('event_category !=', '')
                       ->groupBy('event_category')
                       ->orderBy('count', 'DESC');
        
        if ($startDate && $endDate) {
            $builder->where('occurred_at >=', $startDate)
                   ->where('occurred_at <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Track custom event
     */
    public function trackEvent($sessionId, $eventType, $eventAction, $eventLabel = null, $eventValue = null, $eventCategory = null, $pageViewId = null, $pageUrl = null)
    {
        // Get current page URL if not provided
        if (empty($pageUrl)) {
            $pageUrl = current_url();
            if (empty($pageUrl) || $pageUrl === '/') {
                $pageUrl = $_SERVER['REQUEST_URI'] ?? '/';
            }
        }
        
        // Clean the URL (remove query strings for consistency)
        $parsedUrl = parse_url($pageUrl);
        $pageUrl = $parsedUrl['path'] ?? '/';
        
        // Generate UUID for the event
        try {
            $eventId = Uuid::uuid4()->toString();
        } catch (\Exception $e) {
            log_message('error', 'UUID generation failed: ' . $e->getMessage());
            return false;
        }
        
        // Use query builder directly to avoid model timestamp/callback issues
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        
        $data = [
            'id' => $eventId,
            'session_id' => $sessionId,
            'event_type' => $eventType,
            'event_category' => $eventCategory,
            'event_action' => $eventAction,
            'event_label' => $eventLabel,
            'event_value' => $eventValue,
            'page_url' => $pageUrl,
            'occurred_at' => $now,
            'created_at' => $now
        ];
        
        // Remove null values to avoid issues
        $data = array_filter($data, function($value) {
            return $value !== null;
        });
        
        return $db->table('user_events')->insert($data);
    }
}
