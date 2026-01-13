<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class UserSessionModel extends Model
{
    protected $table         = 'user_sessions';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = false;
    protected $returnType    = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id', 'session_id', 'user_id', 'ip_address', 'user_agent', 'browser', 'device', 
        'os', 'country', 'city', 'referrer', 'analytics_consent', 'marketing_consent',
        'session_start', 'session_end', 'total_duration', 'page_views', 'is_bounce'
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
        try {
            $builder = $this->builder();
            
            if ($startDate && $endDate) {
                $builder->where('session_start >=', $startDate)
                       ->where('session_start <=', $endDate);
            }

            $totalSessions = $builder->countAllResults(false);
            
            // Reset builder for bounce sessions query
            $bounceBuilder = $this->builder();
            if ($startDate && $endDate) {
                $bounceBuilder->where('session_start >=', $startDate)
                             ->where('session_start <=', $endDate);
            }
            $bounceSessions = $bounceBuilder->where('is_bounce', 1)->countAllResults(false);
            
            $avgDuration = 0;
            $avgPageViews = 0;
            
            if ($startDate && $endDate) {
                $durationResult = $this->db->table($this->table)
                                   ->select('AVG(TIMESTAMPDIFF(SECOND, session_start, session_end)) as avg_duration')
                                   ->where('session_end IS NOT NULL')
                                   ->where('session_start >=', $startDate)
                                   ->where('session_start <=', $endDate)
                                   ->get()
                                   ->getRow();
                
                $avgDuration = $durationResult ? ($durationResult->avg_duration ?? 0) : 0;

                $pageViewsResult = $this->db->table($this->table)
                                   ->selectAvg('page_views')
                                   ->where('session_start >=', $startDate)
                                   ->where('session_start <=', $endDate)
                                   ->get()
                                   ->getRow();
                
                $avgPageViews = $pageViewsResult ? ($pageViewsResult->page_views ?? 0) : 0;
            }

            return [
                'total_sessions' => (int) $totalSessions,
                'bounce_sessions' => (int) $bounceSessions,
                'bounce_rate' => $totalSessions > 0 ? round(($bounceSessions / $totalSessions) * 100, 2) : 0,
                'avg_duration' => round((float) $avgDuration, 2),
                'avg_page_views' => round((float) $avgPageViews, 2)
            ];
        } catch (\Exception $e) {
            log_message('error', 'getSessionStats error: ' . $e->getMessage());
            return [
                'total_sessions' => 0,
                'bounce_sessions' => 0,
                'bounce_rate' => 0,
                'avg_duration' => 0,
                'avg_page_views' => 0
            ];
        }
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
