<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table            = 'sitemap_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'id',
        'setting_key',
        'setting_value',
        'setting_type',
        'description'
    ];

    // Dates  
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'setting_key' => 'required|string|max_length[255]',
        'setting_value' => 'permit_empty|string',
        'setting_type' => 'permit_empty|in_list[string,integer,boolean,array,json]',
        'description' => 'permit_empty|string'
    ];

    protected $validationMessages   = [
        'setting_key' => [
            'required' => 'Setting key is required',
            'max_length' => 'Setting key cannot exceed 255 characters'
        ]
    ];

    // UUID generation
    protected $beforeInsert = ['generateUUID'];

    /**
     * Generate UUID for new records
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get a setting value
     */
    public function getSetting($key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        // Convert value based on type
        return $this->convertValue($setting['setting_value'], $setting['setting_type'] ?? 'string');
    }

    /**
     * Set a setting value
     */
    public function setSetting($key, $value, $type = null, $description = null)
    {
        // Auto-detect type if not provided
        if ($type === null) {
            $type = $this->detectType($value);
        }

        // Convert value to string for storage
        $stringValue = $this->convertToString($value, $type);

        $data = [
            'setting_key' => $key,
            'setting_value' => $stringValue,
            'setting_type' => $type
        ];

        if ($description !== null) {
            $data['description'] = $description;
        }

        // Check if setting exists
        $existing = $this->where('setting_key', $key)->first();
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Get multiple settings
     */
    public function getSettings($keys = null)
    {
        $builder = $this;
        
        if ($keys !== null) {
            $builder = $builder->whereIn('setting_key', $keys);
        }
        
        $settings = $builder->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->convertValue(
                $setting['setting_value'], 
                $setting['setting_type'] ?? 'string'
            );
        }
        
        return $result;
    }

    /**
     * Convert string value back to original type
     */
    private function convertValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'array':
            case 'json':
                return json_decode($value, true) ?? [];
            default:
                return $value;
        }
    }

    /**
     * Convert value to string for storage
     */
    private function convertToString($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'array':
            case 'json':
                return json_encode($value);
            default:
                return (string) $value;
        }
    }

    /**
     * Auto-detect value type
     */
    private function detectType($value)
    {
        if (is_bool($value)) {
            return 'boolean';
        } elseif (is_int($value)) {
            return 'integer';
        } elseif (is_array($value)) {
            return 'array';
        } else {
            return 'string';
        }
    }
}
