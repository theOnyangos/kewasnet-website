<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class EmailSettings extends Model
{
    protected $table            = 'email_settings';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'host',
        'username', 
        'password',
        'encryption',
        'port',
        'from_address',
        'from_name',
        // Additional fields (will be added when migration runs)
        'smtp_timeout',
        'reply_to_email',
        'bcc_email',
        'email_header',
        'email_footer',
        'email_enabled',
        'debug_mode',
        'html_emails'
    ];

    // Dates  
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation (basic for current structure)
    protected $validationRules      = [
        'host' => 'permit_empty|string|max_length[255]',
        'username' => 'permit_empty|valid_email|max_length[255]',
        'password' => 'permit_empty|string|max_length[255]',
        'encryption' => 'permit_empty|in_list[tls,ssl,]',
        'port' => 'permit_empty|integer|greater_than[0]',
        'from_address' => 'permit_empty|valid_email|max_length[255]',
        'from_name' => 'permit_empty|string|max_length[255]'
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];
    
    /**
     * Get the first email settings record or create a default one
     */
    public function getSettings(): ?array
    {
        $settings = $this->first();
        
        if (!$settings) {
            // Create default settings
            $defaultSettings = [
                'host' => '',
                'username' => '',
                'password' => '',
                'encryption' => 'tls',
                'port' => 587,
                'from_address' => '',
                'from_name' => 'KEWASNET',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->insert($defaultSettings);
            $settings = $this->first();
        }
        
        return $settings;
    }
    
    /**
     * Update email settings
     */
    public function updateSettings(array $data): bool
    {
        try {
            $existingSettings = $this->first();
            
            if ($existingSettings) {
                return $this->update($existingSettings['id'], $data);
            } else {
                // If no settings exist, create new ones with defaults
                $defaultData = array_merge([
                    'host' => '',
                    'username' => '',
                    'password' => '',
                    'encryption' => 'tls',
                    'port' => 587,
                    'from_address' => '',
                    'from_name' => ''
                ], $data);
                
                return $this->insert($defaultData) !== false;
            }
        } catch (Exception $e) {
            log_message('error', 'EmailSettings::updateSettings - ' . $e->getMessage());
            return false;
        }
    }
}
