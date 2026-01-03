<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'role_id';
    protected $allowedFields    = [
        'role_name',
        'role_description',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // This function generates a unique ID for the role
    public function generateID(): string
    {
        // Generate a random string
        $randomString = random_string('alnum', 8);

        // Check if the ID exists
        $role = $this->where('role_id', $randomString)->first();

        // If the ID exists, generate a new one
        if ($role) {
            return $this->generateID();
        }

        // If the ID does not exist, return it
        return $randomString;
    }

    // This method gets all roles
    public function getRoles()
    {
        
    }
}
