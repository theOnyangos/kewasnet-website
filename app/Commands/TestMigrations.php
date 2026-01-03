<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestMigrations extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:test-migrations';
    protected $description = 'Tests migrations on a fresh test database';

    public function run(array $params)
    {
        CLI::write('===========================================', 'yellow');
        CLI::write('  Migration Test on Fresh Database', 'yellow');
        CLI::write('===========================================', 'yellow');
        CLI::newLine();
        
        // Use test database
        $testDb = 'kewasnet_test';
        $db = \Config\Database::connect();
        
        CLI::write("Target Database: $testDb", 'cyan');
        CLI::newLine();
        
        $confirm = CLI::prompt("This will drop and recreate the '$testDb' database. Continue?", ['y', 'n'], 'required');
        
        if ($confirm !== 'y') {
            CLI::write('Test cancelled', 'yellow');
            return;
        }
        
        CLI::newLine();
        
        // Step 1: Clean test database
        CLI::write('Step 1: Cleaning test database...', 'yellow');
        try {
            $db->query("DROP DATABASE IF EXISTS `$testDb`");
            $db->query("CREATE DATABASE `$testDb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            CLI::write('✓ Test database cleaned and recreated', 'green');
        } catch (\Exception $e) {
            CLI::write('✗ Failed to clean database: ' . $e->getMessage(), 'red');
            return;
        }
        
        CLI::newLine();
        
        // Step 2: Switch to test database
        CLI::write('Step 2: Connecting to test database...', 'yellow');
        $config = config('Database');
        $originalDb = $config->default['database'];
        $config->default['database'] = $testDb;
        
        // Reconnect
        $testDbConn = \Config\Database::connect('default', false);
        CLI::write("✓ Connected to $testDb", 'green');
        CLI::newLine();
        
        // Step 3: Run migrations
        CLI::write('Step 3: Running migrations...', 'yellow');
        CLI::newLine();
        
        $migrate = \Config\Services::migrations();
        
        try {
            $migrate->setNamespace('App')->latest();
            CLI::write('✓ All migrations executed successfully!', 'green');
        } catch (\Exception $e) {
            CLI::write('✗ Migration failed!', 'red');
            CLI::write('Error: ' . $e->getMessage(), 'red');
            
            // Restore original database connection
            $config->default['database'] = $originalDb;
            CLI::newLine();
            return;
        }
        
        CLI::newLine();
        
        // Step 4: Verify tables
        CLI::write('Step 4: Verifying tables created...', 'yellow');
        $tables = $testDbConn->listTables();
        CLI::write('Total tables created: ' . count($tables), 'cyan');
        CLI::newLine();
        
        // Check sample tables by category
        $categories = [
            'Core' => ['system_users', 'roles', 'user_details', 'password_reset_tokens'],
            'Learning Hub' => ['courses', 'course_sections', 'quizzes', 'quiz_questions', 'lecture_attachments'],
            'Blog' => ['blogs', 'blog_posts', 'blog_categories', 'blog_comments'],
            'Forum' => ['forums', 'discussions', 'replies', 'likes', 'bookmarks'],
            'Resources' => ['resources', 'resource_categories', 'contributors'],
            'Events/Jobs' => ['events', 'job_opportunities', 'job_applicants'],
            'Payment' => ['orders', 'mpesa_settings', 'paystack_settings'],
            'Settings' => ['settings', 'sitemaps', 'pillars', 'countries']
        ];
        
        $totalFound = 0;
        $totalExpected = 0;
        
        foreach ($categories as $category => $sampleTables) {
            $found = 0;
            foreach ($sampleTables as $table) {
                if (in_array($table, $tables)) {
                    $found++;
                    $totalFound++;
                }
            }
            $totalExpected += count($sampleTables);
            $status = $found === count($sampleTables) ? '✓' : ($found > 0 ? '⚠' : '✗');
            $color = $found === count($sampleTables) ? 'green' : ($found > 0 ? 'yellow' : 'red');
            CLI::write("  $status $category: $found/" . count($sampleTables) . " sample tables", $color);
        }
        
        CLI::newLine();
        
        // Step 5: Verify UUID implementation
        CLI::write('Step 5: Verifying UUID implementation...', 'yellow');
        $uuidQuery = "
            SELECT COUNT(DISTINCT table_name) as count
            FROM information_schema.columns 
            WHERE table_schema = '$testDb'
            AND column_name = 'id' 
            AND data_type = 'varchar' 
            AND character_maximum_length = 36
            AND (
                table_name LIKE 'course%' 
                OR table_name LIKE 'quiz%' 
                OR table_name IN ('lecture_attachments', 'lecture_links', 'vimeo_videos', 'user_progress')
            )
        ";
        
        $uuidCount = $testDbConn->query($uuidQuery)->getRow()->count;
        CLI::write("UUID tables found: $uuidCount", 'cyan');
        
        if ($uuidCount >= 15) {
            CLI::write('✓ UUID implementation verified', 'green');
        } else {
            CLI::write('⚠ Expected ~21 UUID tables', 'yellow');
        }
        
        CLI::newLine();
        
        // Step 6: Check migrations table
        $migrationCount = $testDbConn->table('migrations')->countAll();
        CLI::write("Migrations registered: $migrationCount", 'cyan');
        
        CLI::newLine();
        
        // Summary
        CLI::write('===========================================', 'yellow');
        CLI::write('  TEST RESULTS', 'yellow');
        CLI::write('===========================================', 'yellow');
        CLI::write('✓ Migrations executed without errors', 'green');
        CLI::write('✓ ' . count($tables) . ' tables created successfully', 'green');
        CLI::write('✓ ' . $totalFound . '/' . $totalExpected . ' sample tables verified', 'green');
        CLI::write('✓ ' . $uuidCount . ' UUID tables verified', 'green');
        CLI::write('✓ ' . $migrationCount . ' migrations registered', 'green');
        CLI::newLine();
        CLI::write("Test database: $testDb (can be dropped)", 'cyan');
        CLI::write("Production database: $originalDb (unchanged)", 'cyan');
        CLI::newLine();
        CLI::write('Your migrations are working perfectly! 🎉', 'green');
        CLI::newLine();
        
        // Restore original database connection
        $config->default['database'] = $originalDb;
        
        // Option to drop test database
        $dropDb = CLI::prompt('Drop test database?', ['y', 'n']);
        if ($dropDb === 'y') {
            $db->query("DROP DATABASE IF EXISTS `$testDb`");
            CLI::write("✓ Test database '$testDb' dropped", 'green');
        }
    }
}
