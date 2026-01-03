<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UserBookmarkSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get sample user IDs from system_users table
        $users = $db->table('system_users')
                  ->select('id')
                  ->limit(5)
                  ->get()
                  ->getResult();
        
        // Get sample resource IDs
        $resources = $db->table('resources')
                      ->select('id')
                      ->limit(10)
                      ->get()
                      ->getResult();
        
        if (empty($users)) {
            $users = [ (object) ['id' => Uuid::uuid4()->toString()] ]; // Fallback dummy user
        }
        
        if (empty($resources)) {
            echo "No resources found. Please run ResourceSeeder first.\n";
            return;
        }
        
        // Create random bookmarks
        foreach ($users as $user) {
            $bookmarkCount = rand(2, 5);
            $randomResources = array_rand((array) $resources, $bookmarkCount);
            
            if (!is_array($randomResources)) {
                $randomResources = [$randomResources];
            }
            
            foreach ($randomResources as $index) {
                $this->createBookmark($user->id, $resources[$index]->id);
            }
        }
    }
    
    protected function createBookmark($userId, $resourceId)
    {
        $db = \Config\Database::connect();
        
        // Check if bookmark already exists
        $exists = $db->table('user_bookmarks')
                   ->where('user_id', $userId)
                   ->where('resource_id', $resourceId)
                   ->countAllResults();
        
        if (!$exists) {
            $bookmarkData = [
                'id' => Uuid::uuid4()->toString(),
                'user_id' => $userId,
                'resource_id' => $resourceId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('user_bookmarks')->insert($bookmarkData);
        }
    }
}