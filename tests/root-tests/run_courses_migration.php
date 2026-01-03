<?php
require_once __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

require_once FCPATH . '../vendor/autoload.php';

// Get database connection
$db = \Config\Database::connect();

// Check if courses table exists
if ($db->tableExists('courses')) {
    echo "Courses table already exists. Dropping it first...\n";
    $db->query('DROP TABLE IF EXISTS courses');
    echo "Courses table dropped.\n";
}

// Run the migration directly
echo "Creating courses table...\n";

$forge = \Config\Database::forge();

$forge->addField([
    'id' => [
        'type' => 'VARCHAR',
        'constraint' => 36,
    ],
    'user_id' => [
        'type' => 'INT',
        'constraint' => 5,
        'unsigned' => true,
    ],
    'category_id' => [
        'type' => 'INT',
        'constraint' => 5,
    ],
    'sub_category_id' => [
        'type' => 'INT',
        'constraint' => 5,
        'null' => true,
    ],
    'title' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
    ],
    'summary' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
    ],
    'certificate' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 0
    ],
    'level' => [
        'type' => 'ENUM',
        'constraint' => ['beginner', 'intermediate', 'advanced'],
        'default' => 'beginner'
    ],
    'price' => [
        'type' => 'DECIMAL',
        'constraint' => '10,2',
    ],
    'is_paid' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 0,
    ],
    'discount_price' => [
        'type' => 'DECIMAL',
        'constraint' => '10,2',
    ],
    'duration' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
    ],
    'resources' => [
        'type' => 'TEXT',
    ],
    'description' => [
        'type' => 'TEXT',
    ],
    'image_url' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
    ],
    'status' => [
        'type' => 'INT',
        'constraint' => 1,
        'default' => 0
    ],
    'slug' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
    ],
    'star_rating' => [
        'type' => 'DECIMAL',
        'constraint' => '3,2',
    ],
    'goals' => [
        'type' => 'TEXT',
    ],
    'instructor_id' => [
        'type' => 'TEXT',
        'null' => true,
    ],
    'preview_video_url' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
    ],
    'vimeo_embed_settings' => [
        'type' => 'JSON',
        'null' => true,
    ],
    'language' => [
        'type' => 'ENUM',
        'constraint' => ['english', 'french', 'spanish', 'swahili', 'german', 'italian', 'portuguese', 'russian', 'chinese', 'arabic'],
        'default' => 'english'
    ],
    'created_at' => [
        'type' => 'DATETIME',
        'null' => true
    ],
    'updated_at' => [
        'type' => 'DATETIME',
        'null' => true
    ],
    'deleted_at' => [
        'type' => 'DATETIME',
        'null' => true
    ],
]);

$forge->addKey('id', true);
$forge->addKey('is_paid');
$forge->createTable('courses');

echo "Courses table created successfully!\n";

// Verify the table was created
if ($db->tableExists('courses')) {
    echo "Verification: Courses table exists.\n";
    
    // Show table structure
    $fields = $db->getFieldNames('courses');
    echo "Table columns: " . implode(', ', $fields) . "\n";
} else {
    echo "ERROR: Courses table was not created.\n";
}
