<?php
// Database setup script for comment system

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "kewasnet";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully to database: $dbname\n\n";
    
    // Read SQL file
    $sql = file_get_contents('comment_system_tables.sql');
    
    // Split SQL into individual queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($queries as $query) {
        if (empty($query) || strpos($query, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($query);
            echo "✓ Query executed successfully\n";
        } catch (PDOException $e) {
            echo "✗ Error executing query: " . $e->getMessage() . "\n";
            echo "Query: " . substr($query, 0, 100) . "...\n\n";
        }
    }
    
    echo "\nDatabase setup completed!\n";
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
