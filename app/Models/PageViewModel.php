<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class PageViewModel extends Model
{
    protected $table         = 'page_views';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = false;
    protected $returnType    = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id', 'session_id', 'user_id', 'page_url', 'page_title', 'page_category',
        'time_on_page', 'scroll_depth', 'exit_page', 'viewed_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'session_id' => 'required',
        'page_url'   => 'required|max_length[500]',
        'viewed_at'  => 'required|valid_date',
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
     * Get most popular pages
     */
    public function getPopularPages($limit = 10, $startDate = null, $endDate = null)
    {
        try {
            $builder = $this->select('page_url, page_title, COUNT(*) as views, AVG(time_on_page) as avg_time')
                           ->groupBy('page_url')
                           ->orderBy('views', 'DESC')
                           ->limit($limit);
            
            if ($startDate && $endDate) {
                $builder->where('viewed_at >=', $startDate)
                       ->where('viewed_at <=', $endDate);
            }

            return $builder->get()->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getPopularPages error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get page views by category
     */
    public function getViewsByCategory($startDate = null, $endDate = null)
    {
        try {
            $builder = $this->select('page_category, COUNT(*) as views')
                           ->where('page_category IS NOT NULL')
                           ->groupBy('page_category')
                           ->orderBy('views', 'DESC');
            
            if ($startDate && $endDate) {
                $builder->where('viewed_at >=', $startDate)
                       ->where('viewed_at <=', $endDate);
            }

            return $builder->get()->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getViewsByCategory error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get page performance metrics
     */
    public function getPageMetrics($pageUrl = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
            COUNT(*) as total_views,
            AVG(time_on_page) as avg_time_on_page,
            AVG(scroll_depth) as avg_scroll_depth,
            COUNT(CASE WHEN exit_page = 1 THEN 1 END) as exit_count
        ');
        
        if ($pageUrl) {
            $builder->where('page_url', $pageUrl);
        }
        
        if ($startDate && $endDate) {
            $builder->where('viewed_at >=', $startDate)
                   ->where('viewed_at <=', $endDate);
        }

        $result = $builder->get()->getRow();
        
        if ($result) {
            $result->exit_rate = $result->total_views > 0 ? 
                round(($result->exit_count / $result->total_views) * 100, 2) : 0;
        }

        return $result;
    }

    /**
     * Get hourly page views for a specific date
     */
    public function getHourlyViews($date)
    {
        return $this->select('HOUR(viewed_at) as hour, COUNT(*) as views')
                   ->where('DATE(viewed_at)', $date)
                   ->groupBy('HOUR(viewed_at)')
                   ->orderBy('hour', 'ASC')
                   ->get()
                   ->getResult();
    }

    /**
     * Get daily page views timeline
     */
    public function getDailyViewsTimeline($startDate = null, $endDate = null)
    {
        try {
            $builder = $this->select('DATE(viewed_at) as date, COUNT(*) as views')
                           ->groupBy('DATE(viewed_at)')
                           ->orderBy('date', 'ASC');
            
            if ($startDate && $endDate) {
                $builder->where('viewed_at >=', $startDate)
                       ->where('viewed_at <=', $endDate);
            }

            return $builder->get()->getResult();
        } catch (\Exception $e) {
            log_message('error', 'getDailyViewsTimeline error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user journey (page flow)
     */
    public function getUserJourney($sessionId)
    {
        return $this->select('page_url, page_title, time_on_page, scroll_depth, viewed_at')
                   ->where('session_id', $sessionId)
                   ->orderBy('viewed_at', 'ASC')
                   ->get()
                   ->getResult();
    }

    /**
     * Update time on page when user leaves
     */
    public function updateTimeOnPage($pageViewId, $timeOnPage, $scrollDepth = null, $isExit = false)
    {
        $data = [
            'time_on_page' => $timeOnPage,
            'exit_page' => $isExit
        ];
        
        if ($scrollDepth !== null) {
            $data['scroll_depth'] = $scrollDepth;
        }
        
        return $this->update($pageViewId, $data);
    }
}
