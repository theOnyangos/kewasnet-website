<?php

namespace App\Models;

use CodeIgniter\Model;

class UserSessionModel extends Model
{
    protected $table         = 'user_sessions';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = false;
    protected $returnType    = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'session_id', 'user_id', 'ip_address', 'user_agent', 'browser', 'device', 
        'os', 'country', 'city', 'referrer', 'analytics_consent', 'marketing_consent',
        'session_start', 'session_end', 'page_views', 'is_bounce'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'session_id'    => 'required|max_length[128]',
        'ip_address'    => 'required|valid_ip',
        'session_start' => 'required|valid_date',
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
     * Get active sessions (sessions without end time or recent activity)
     */
    public function getActiveSessions()
    {
        return $this->where('session_end', null)
                   ->where('session_start >', date('Y-m-d H:i:s', strtotime('-24 hours')))
                   ->findAll();
    }

    /**
     * Get session statistics for a date range
     */
    public function getSessionStats($startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        
        if ($startDate && $endDate) {
            $builder->where('session_start >=', $startDate)
                   ->where('session_start <=', $endDate);
        }

        $totalSessions = $builder->countAllResults(false);
        $bounceSessions = $builder->where('is_bounce', 1)->countAllResults(false);
        
        $avgDuration = $this->db->table($this->table)
                           ->select('AVG(TIMESTAMPDIFF(SECOND, session_start, session_end)) as avg_duration')
                           ->where('session_end IS NOT NULL')
                           ->where('session_start >=', $startDate)
                           ->where('session_start <=', $endDate)
                           ->get()
                           ->getRow()
                           ->avg_duration ?? 0;

        $avgPageViews = $this->db->table($this->table)
                           ->selectAvg('page_views')
                           ->where('session_start >=', $startDate)
                           ->where('session_start <=', $endDate)
                           ->get()
                           ->getRow()
                           ->page_views ?? 0;

        return [
            'total_sessions' => $totalSessions,
            'bounce_sessions' => $bounceSessions,
            'bounce_rate' => $totalSessions > 0 ? round(($bounceSessions / $totalSessions) * 100, 2) : 0,
            'avg_duration' => round($avgDuration, 2),
            'avg_page_views' => round($avgPageViews, 2)
        ];
    }

    /**
     * Get sessions by device type
     */
    public function getSessionsByDevice($startDate = null, $endDate = null)
    {
        $builder = $this->select('device, COUNT(*) as count')
                       ->groupBy('device');
        
        if ($startDate && $endDate) {
            $builder->where('session_start >=', $startDate)
                   ->where('session_start <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Get top countries by sessions
     */
    public function getTopCountries($limit = 10, $startDate = null, $endDate = null)
    {
        $builder = $this->select('country, COUNT(*) as count')
                       ->where('country IS NOT NULL')
                       ->groupBy('country')
                       ->orderBy('count', 'DESC')
                       ->limit($limit);
        
        if ($startDate && $endDate) {
            $builder->where('session_start >=', $startDate)
                   ->where('session_start <=', $endDate);
        }

        return $builder->get()->getResult();
    }

    /**
     * Update session end time and calculate duration
     */
    public function endSession($sessionId)
    {
        $session = $this->where('session_id', $sessionId)->first();
        
        if ($session) {
            $endTime = date('Y-m-d H:i:s');
            $startTime = $session->session_start;
            $duration = strtotime($endTime) - strtotime($startTime);
            
            return $this->update($session->id, [
                'session_end' => $endTime,
                'is_bounce' => $session->page_views <= 1
            ]);
        }
        
        return false;
    }
}
