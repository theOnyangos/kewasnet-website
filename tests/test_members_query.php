<?php

// Simple script to test database query
$host = 'localhost';
$dbname = 'kewasnet_website';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Testing Members Query...\n\n";
    
    // Get first forum
    $stmt = $pdo->query("SELECT * FROM forums LIMIT 1");
    $forum = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$forum) {
        echo "No forums found in database.\n";
        exit;
    }
    
    echo "Testing with Forum ID: {$forum->id} - {$forum->title}\n\n";
    
    // Test the members query
    $sql = "SELECT 
                forum_members.id,
                forum_members.user_id,
                forum_members.forum_id,
                COALESCE(forum_members.joined_at, forum_members.created_at) as joined_at,
                system_users.picture as member_profile, 
                CONCAT(COALESCE(system_users.first_name, ''), ' ', COALESCE(system_users.last_name, '')) as member_name, 
                system_users.email as email,
                IF(forum_moderators.id IS NOT NULL AND forum_moderators.is_active = 1, 'moderator', 'member') as role
            FROM forum_members
            LEFT JOIN system_users ON system_users.id = forum_members.user_id COLLATE utf8mb4_unicode_ci
            LEFT JOIN forum_moderators ON forum_moderators.user_id = forum_members.user_id 
                AND forum_moderators.forum_id = forum_members.forum_id 
                AND forum_moderators.is_active = 1
            WHERE forum_members.forum_id = ?
            GROUP BY forum_members.id
            ORDER BY joined_at DESC
            LIMIT 10";
    
    echo "Executing query...\n\n";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$forum->id]);
    $results = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo "Results Count: " . count($results) . "\n\n";
    
    if (count($results) > 0) {
        echo "Sample Result:\n";
        print_r($results[0]);
        echo "\n\nAll Results:\n";
        foreach ($results as $result) {
            echo "- {$result->member_name} ({$result->email}) - Role: {$result->role}\n";
        }
    } else {
        echo "No members found for this forum.\n\n";
        
        // Check if there are any members at all
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM forum_members");
        $totalMembers = $stmt->fetch(PDO::FETCH_OBJ)->count;
        echo "Total members in database: {$totalMembers}\n";
        
        // Check if there are members for this specific forum
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM forum_members WHERE forum_id = ?");
        $stmt->execute([$forum->id]);
        $forumMembers = $stmt->fetch(PDO::FETCH_OBJ)->count;
        echo "Members for forum {$forum->id}: {$forumMembers}\n";
    }
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
