<?php

namespace App\Models;

use CodeIgniter\Model;

class UserEventModel extends Model
{
    protected $table         = 'user_events';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = false;
    protected $returnType    = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'session_id', 'page_view_id', 'event_type', 'event_category', 'event_action',
        'event_label', 'event_value', 'event_metadata', 'occurred_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'session_id'   => 'required',
        'event_type'   => 'required|in_list[click,form_submit,download,search,registration,login,logout,contact,newsletter,custom]',
        'event_action' => 'required|max_length[100]',
        'page_url'     => 'required|max_length[500]',
        'occurred_at'  => 'required|valid_date',
    ];

    protected $beforeInsert = ['addUuid'];

    protected function addUuid(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = $this->generateUuid();
        }
        return $data;
    }

    private function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
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
     * Track custom event
     */
    public function trackEvent($sessionId, $eventType, $eventAction, $eventLabel = null, $eventValue = null, $eventCategory = null, $pageViewId = null)
    {
        return $this->insert([
            'session_id' => $sessionId,
            'page_view_id' => $pageViewId,
            'event_type' => $eventType,
            'event_category' => $eventCategory,
            'event_action' => $eventAction,
            'event_label' => $eventLabel,
            'event_value' => $eventValue,
            'occurred_at' => date('Y-m-d H:i:s')
        ]);
    }
}
