<?php

namespace App\Models;

use CodeIgniter\Model;
use Carbon\Carbon;
use App\Libraries\Hash;
use App\Libraries\GenerateIDs;
use Ramsey\Uuid\Uuid;

class UserModel extends Model
{
    protected $table            = 'system_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'role_id',
        'registered_by',
        'employee_number',
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'password',
        'picture',
        'bio',
        'email_verified_at',
        'verification_token',
        'terms',
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
        'account_status',
        'profile_cover_image',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Update password for a user by email
    public function updatePassword($email, $password)
    {
        // Get user by email
        $user = $this->where('email', $email)->first();

        // Check if user exists
        if (!$user) {
            return false;
        }

        // Hash the new password
        $newPassword = Hash::make($password);

        // Update the user's password
        $this->where('email', $email)->update([
            'password' => $newPassword,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);

        return true;
    }

    // Get all admin users from the database
    public function getAdminUsers()
    {
        return $this->findAll();
    }

    // This method creates a new user
    public function createUser($data)
    {
        // Hash the password
        $hash = new Hash();
        $session = session();
        $password = $hash::make($data['password']);
        $fullName = explode(" ", $data['full_name']);
        $firstName = $fullName[0];
        $lastName = $fullName[1];
        $username = strtolower($firstName.''.$lastName);

        // Generate UUID for the user
        $userId = Uuid::uuid4()->toString();

        // Create the user
        $this->insert([
            'id'              => $userId,
            'role_id'         => $data['role_id'],
            'registered_by'   => $session->get('id') ?? Null,
            'employee_number' => $data['employee_number'],
            'first_name'      => $firstName,
            'last_name'       => $lastName,
            'username'        => $data['username'] ?? $username,
            'email'           => $data['email'],
            'phone'           => $data['phone'] ?? null,
            'bio'             => $data['bio'] ?? null,
            'terms'           => $data['terms'] ?? 0,
            'password'        => $password,
            'created_at'      => Carbon::now()->toDateTimeString(),
            'updated_at'      => Carbon::now()->toDateTimeString(),
        ]);

        // Return the user ID
        return $userId;
    }

    // This method verifies users account
    public function verifyAccount($code)
    {
        // Get user by verification code
        $user = $this->where('id', $code)->first();

        // Check if user exists
        if (!$user) {
            $response = [
                'status' => 'error',
                'status_code' => 404,
                'message' => 'User account not found',
            ];
            return $response;
        }

        // Check if user account has been verified
        if ($user['email_verified_at'] !== null) {
            $response = [
                'status' => 'error',
                'status_code' => 403,
                'message' => 'User account has already been verified',
            ];
            return $response;
        }

        // Update the user's account
        $this->where('id', $code)->set([
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        $response = [
            'status' => 'success',
            'status_code' => 200,
            'message' => 'User account verified successfully',
        ];

        return $response;
    }

    // This method gets all users where role_name is Administrator
    public function getAdministrators()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        return $this->where('role_id', 1)
                    ->where('id !=', $loggedInUserId)
                    ->where('deleted_at', null)
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    // This method gets only registered employees
    public function getRegisteredEmployees()
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        return $this->where('role_id', 3)
                    ->where('id !=', $loggedInUserId)
                    ->where('deleted_at', null)
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    // This method performs search for admin users only
    public function searchAdministrators($searchQuery = null)
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        $query = $this->where('role_id', 1)
                    ->where('id !=', $loggedInUserId)
                        ->where('deleted_at', null);

        if ($searchQuery !== null && $searchQuery !== '') {
           
            $query->like('first_name', $searchQuery)
                ->orLike('last_name', $searchQuery)
                ->orLike('email', $searchQuery)
                ->orLike('phone', $searchQuery)
                ->orLike('employee_number', $searchQuery);
        }

        return $query->findAll();
    }

    // This method performs search for registered employees only
    public function searchEmployees($searchQuery = null)
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        if ($searchQuery !== null && $searchQuery !== '') {
            return $this->where('role_id', 3)
                ->where('id !=', $loggedInUserId)
                ->where('deleted_at', null)
                ->like('first_name', $searchQuery)
                ->orLike('last_name', $searchQuery)
                ->orLike('email', $searchQuery)
                ->orLike('phone', $searchQuery)
                ->orLike('employee_number', $searchQuery)
                ->findAll();
        }

        $query = $this->where('role_id', 3)
                    ->where('id !=', $loggedInUserId)
                    ->where('deleted_at', null);

        return $query->findAll();
    }

    // This method gets admin registered users
    public function getAdminRegisteredUsers($adminId)
    {
        return $this->where('registered_by', $adminId)->findAll();
    }

    // This method suspends the user account
    public function suspendUser($userId)
    {
        // Update the user's account
        $this->where('id', $userId)->set([
            'status' => 'inactive',
            'account_status' => 'suspended',
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        // Check if the user's account has been suspended
        $user = $this->where('id', $userId)->first();
        if ($user['status'] === 'inactive') {
            return true;
        } else {
            return false;
        }
    }

    // This method get's all users from the database
    public function getAllUsers($limit = null, $offset = null)
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        if ($limit !== null && $offset !== null) {
            return $this->orderBy('id', 'DESC')
                    ->where('id !=', $loggedInUserId)
                    ->where('deleted_at', null)
                    ->findAll($limit, $offset);
        }

        return $this->orderBy('id', 'DESC')
                    ->where('id !=', $loggedInUserId)
                    ->where('deleted_at', null)
                    ->findAll();
    }

    // This method searches for all users in the database
    public function searchUsers($searchQuery = null)
    {
        $session = session();
        $loggedInUserId = $session->get('id');

        if ($searchQuery !== null && $searchQuery !== '') {
            return $this->like('first_name', $searchQuery)
                ->where('deleted_at', null)
                ->where('id !=', $loggedInUserId)
                ->orderBy('id', 'DESC')
                ->orLike('last_name', $searchQuery)
                ->orLike('email', $searchQuery)
                ->orLike('phone', $searchQuery)
                ->orLike('employee_number', $searchQuery)
                ->findAll();
        }

        return $this->orderBy('id', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->where('id !=', $loggedInUserId)
                    ->where('deleted_at', null)->findAll();
    }

    // This method updates the user's personal information
    public function updatePersonalInformation($data)
    {
        $fullName = explode(" ", $data['full_name']);
        $firstName = $fullName[0];
        $lastName = $fullName[1];

        // Update the user's personal information
        $this->where('id', $data['user_id'])->set([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $data['email'],
            'phone' => $data['phone'],
            'bio' => $data['bio'],
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        // Check if the user's personal information has been updated
        $user = $this->where('id', $data['user_id'])->first();
        if (
            $user['first_name'] === $firstName && 
            $user['last_name'] === $lastName && 
            $user['email'] === $data['email'] && 
            $user['phone'] === $data['phone'] &&
            $user['bio'] === $data['bio']
            ) {
            return true;
        } else {
            return false;
        }
    }

    // This method updates the user's personal information
    public function userDetails()
    {
        return $this->hasOne('UserDetail', 'user_id', 'id');
    }

    // This method updates the user's hired data
    public function updateHiredDate($userId, $date = null, $status = null)
    {
        // Check if account status active then update the status field
        if ($status === 'active') {
            $this->where('id', $userId)->set([
                'status' => 'active',
                'account_status' => $status,
                'created_at' => $date,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ])->update();
        } else {
            // Update the user's hired date
            $this->where('id', $userId)->set([
                'status' => 'inactive',
                'account_status' => $status,
                'created_at' => $date,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ])->update();
        }

        // Check if the user's hired date has been updated
        $user = $this->where('id', $userId)->first();
        if ($user['created_at'] === $date || $user['status'] === $status) {
            return true;
        } else {
            return false;
        }
    }

    // This method makes a user an employee by changing their role and assigning them a new employee number
    public function makeUserEmployee($userId)
    {
        // Generate employee number
        $generateIDs = new GenerateIDs();
        $employeeNumber = $generateIDs::generateEmployeeNumber();

        // Update the user's role
        $this->where('id', $userId)->set([
            'role_id' => 3,
            'employee_number' => $employeeNumber,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        // Check if the user's role has been updated
        $user = $this->where('id', $userId)->first();
        if ($user['role_id'] == 3) {
            return true;
        } else {
            return false;
        }
    }

    // This method checks if the user is an employee of the company
    public function isUserEmployee($userId)
    {
        $user = $this->where('id', $userId)->first();

        if ($user['role_id'] == 3) {
            return true;
        } else {
            return false;
        }
    }

    // This method checks if user has profile image
    public function checkProfilePicture($userId)
    {
        $user = $this->where('id', $userId)->first();

        // Return the file path
        if ($user['picture'] !== null) {
            return $user['picture'];
        } else {
            return false;
        }
    }

    // This method deletes the profile picture of a user
    public function deleteProfilePicture($userId)
    {
        // Update the user's profile picture
        $this->where('id', $userId)->set([
            'picture' => null,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        // Check if the user's profile picture has been deleted
        $user = $this->where('id', $userId)->first();
        if ($user['picture'] === null) {
            return true;
        } else {
            return false;
        }
    }

    // This method saves the profile picture in the database
    public function saveProfilePicture($userId, $filePath)
    {
        // Update the user's profile picture
        $this->where('id', $userId)->set([
            'picture' => $filePath,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        // Check if the user's profile picture has been saved
        $user = $this->where('id', $userId)->first();
        if ($user['picture'] === $filePath) {
            return true;
        } else {
            return false;
        }
    }

    // This method updates logged in user password
    public function updateLoggedInUserPassword($userId, $password)
    {
        // Hash the new password
        $hash = new Hash();
        $newPassword = $hash::make($password);

        // Update the user's password
        $this->where('id', $userId)->set([
            'password' => $newPassword,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        return true;
    }

    // This method checks if the user's profile cover photo exists
    public function checkProfileCoverImage($userId)
    {
        $user = $this->where('id', $userId)->first();

        // Return the file path
        if ($user['profile_cover_image'] !== null) {
            return $user['profile_cover_image'];
        } else {
            return false;
        }
    }

    // This method saves the profile cover image
    public function saveProfileCoverImage($userId, $filePath)
    {
        // Update the user's profile cover image
        $this->where('id', $userId)->set([
            'profile_cover_image' => $filePath,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        // Check if the user's profile cover image has been saved
        $user = $this->where('id', $userId)->first();
        if ($user['profile_cover_image'] === $filePath) {
            return true;
        } else {
            return false;
        }
    }

    // This method retrieves user account
    public function retrieveAccount($userId)
    {
        $this->where('id', $userId)->set([
            'status' => 'active',
            'account_status' => 'active',
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        // Check if the user's status has been updated
        $user = $this->where('id', $userId)->first();
        if ($user['status'] === 'active' && $user['account_status'] === 'active') {
            return true;
        } else {
            return false;
        }
    }

    // This method deletes the user's profile cover image
    public function deleteProfileCoverImage($userId)
    {
        // Update the user's profile cover image
        $this->where('id', $userId)->set([
            'profile_cover_image' => null,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        // Check if the user's profile cover image has been deleted
        $user = $this->where('id', $userId)->first();
        if ($user['profile_cover_image'] === null) {
            return true;
        } else {
            return false;
        }
    }

    // This method checks if the user is an instructor
    public function isUserInstructor($userId)
    {
        $user = $this->where('id', $userId)->first();

        if ($user['role_id'] == 4) {
            return true;
        } else {
            return false;
        }
    }

    // This method makes a user an instructor
    public function makeUserInstructor($userId)
    {
        // Update the user's role
        $this->where('id', $userId)->set([
            'role_id' => 4,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ])->update();

        // Check if the user's role has been updated
        $user = $this->where('id', $userId)->first();
        if ($user['role_id'] == 4) {
            return true;
        } else {
            return false;
        }
    }

    // Find user with details
    public function findUserWithDetails($userId)
    {
        return $this->select('system_users.*, user_details.*')
                ->join('user_details', 'user_details.user_id = system_users.id', 'left')
                ->where('system_users.id', $userId)
                ->where('system_users.deleted_at', null)
                ->first();
    }

    // =========================================== New Dashboard DataTables Methods ===========================================

    public function getUsers($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        // Base query with CONCAT for name and date formatting
        $builder = $this->select("system_users.id, CONCAT(system_users.first_name, ' ', system_users.last_name) as name, system_users.email, system_users.phone, system_users.updated_at as last_login, system_users.created_at as joined, roles.role_name as role, system_users.updated_at as updated_date")
                    ->join('roles', 'roles.role_id = system_users.role_id', 'left', false)
                    ->where('system_users.deleted_at', null)
                    ->orderBy($orderBy, $orderDir);

        // Apply search if provided
        if ($search) {
            $builder->groupStart()
                    ->like('first_name', $search)
                    ->orLike('last_name', $search)
                    ->orLike('email', $search)
                    ->orLike('phone', $search)
                    ->groupEnd();
        }

        // Get the results
        $results = $builder->findAll($length, $start);

        // Format dates for human readability
        foreach ($results as &$row) {
            $row['last_login'] = $this->formatDateTime($row['last_login']);
            $row['joined'] = $this->formatDateTime($row['joined']);
        }

        return $results;
    }

    /**
     * Format datetime to human readable format
     */
    protected function formatDateTime($datetime)
    {
        if (empty($datetime)) {
            return 'Never';
        }

        return Carbon::parse($datetime)->diffForHumans();
    }

    // Count all users
    public function countAllUsers()
    {
        return $this->where('deleted_at', null)->countAllResults();
    }

    // Create new user
    public function createUserInternal($postData)
    {
        $hash           = new Hash();
        $hashedPassword = $hash::make($postData['password']);

        // Generate UUID for the user
        $userId = Uuid::uuid4()->toString();

        $data = [
            'id'                => $userId,
            'first_name'        => $postData['first_name'],
            'last_name'         => $postData['last_name'],
            'employee_number'   => $postData['employee_number'],
            'email'             => $postData['email'],
            'phone'             => $postData['phone'],
            'role_id'           => $postData['role'],
            'bio'               => $postData['bio'],
            'password'          => $hashedPassword,
            'terms'             => 1,
            'registered_by'     => session()->get('id') ?? null,
            'created_at'        => Carbon::now()->toDateTimeString(),
        ];

        $this->insert($data);
        return $userId;
    }
}
