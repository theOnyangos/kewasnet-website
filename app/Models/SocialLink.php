<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class SocialLink extends Model
{
    protected $table            = 'social_links';
    protected $primaryKey       = 'uuid';
    protected $allowedFields    = [
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'whatsapp',
        'website'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $useAutoIncrement = false;

    // This method gets all social links from the database    // Get Social links
    public function getSocialLinks()
    {
        return $this->first();
    }

    // Get Social Media Settings
    public function getSettingsByType(string $type)
    {
        return $this->where('type', $type)->first();
    }

    /**
     * Generates UUID using Ramsey's UUID library
     */
    protected function generateUUID(array $data)
    {
        if (empty($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }
}
