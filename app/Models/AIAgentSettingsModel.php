<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class AIAgentSettingsModel extends Model
{
    protected $table            = 'ai_agent_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'setting_key',
        'setting_value',
        'description',
        'updated_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = null;
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['generateUuid', 'handleValue'];
    protected $beforeUpdate = ['handleValue', 'updateTimestamp'];

    /**
     * Generate UUID for new settings
     */
    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['id']) || empty($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                unset($data['data']['id']);
            }
        }
        return $data;
    }

    /**
     * Handle setting value (convert array to JSON if needed)
     */
    protected function handleValue(array $data)
    {
        if (isset($data['data']['setting_value']) && is_array($data['data']['setting_value'])) {
            $data['data']['setting_value'] = json_encode($data['data']['setting_value']);
        }
        return $data;
    }

    /**
     * Update timestamp on update
     */
    protected function updateTimestamp(array $data)
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Get setting by key
     */
    public function getSetting($key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        // Try to decode as JSON
        if (!empty($setting['setting_value'])) {
            $decoded = json_decode($setting['setting_value'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        
        return $setting['setting_value'] ?? $default;
    }

    /**
     * Set or update a setting
     */
    public function setSetting($key, $value, $description = null)
    {
        $existing = $this->where('setting_key', $key)->first();
        
        $data = [
            'setting_key' => $key,
            'setting_value' => is_array($value) ? json_encode($value) : $value,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        if ($description !== null) {
            $data['description'] = $description;
        }
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }
}
