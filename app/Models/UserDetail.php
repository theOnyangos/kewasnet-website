<?php

namespace App\Models;

use CodeIgniter\Model;

class UserDetail extends Model
{
    protected $table            = 'user_details';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id',
        'company_name',
        'designation',
        'department',
        'occupation',
        'email',
        'phone',
        'address',
        'county',
        'website',
        'facebook_link',
        'twitter_link',
        'linkedin_link',
        'instagram_link',
        'created_at',
        'updated_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo('UserModel', 'user_id', 'id');
    }

    // This method updates user's work details
    public function updateWorkInformation($data)
    {
        $userDetail = $this->where('user_id', $data['user_id'])->first();

        if ($userDetail) {
            $workData = array();
            $workData['company_name'] = $data['company'];
            $workData['department'] = $data['department'];
            $workData['designation'] = $data['designation'];
            $workData['occupation'] = $data['occupation'] ?? null;
            $workData['email'] = $data['email'];
            $workData['phone'] = $data['phone'];

            $this->where('user_id', $data['user_id'])->set($workData)->update();

            return true;
        } else {
            $this->insert($data);

            return true;
        }
    }

    // This method updates social media information
    public function updateSocialMediaInformation($data)
    {
        $userDetail = $this->where('user_id', $data['user_id'])->first();

        if ($userDetail) {
            $socialData = array();
            $socialData['facebook_link'] = $data['facebook'];
            $socialData['twitter_link'] = $data['twitter'];
            $socialData['linkedin_link'] = $data['linkedin'];
            $socialData['instagram_link'] = $data['instagram'];

            $this->where('user_id', $data['user_id'])->set($socialData)->update();

            return true;
        } else {
            $this->insert($data);

            return true;
        }
    }
}
