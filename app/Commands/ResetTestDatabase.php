<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetTestDatabase extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:reset-test';
    protected $description = 'Drop and recreate test database, then run migrations';

    public function run(array $params)
    {
        CLI::write('===========================================', 'yellow');
        CLI::write('  Resetting Test Database', 'yellow');
        CLI::write('===========================================', 'yellow');
        CLI::newLine();

        // Get database configuration
        $config = config('Database');
        $originalDb = $config->default['database'];
        
        if ($originalDb === 'kewasnet_test') {
            CLI::error('Cannot run this command when default database is kewasnet_test!');
            return;
        }

        CLI::write("Production database (safe): {$originalDb}", 'green');
        CLI::write("Target database: kewasnet_test", 'yellow');
        CLI::newLine();

        try {
            // Connect to main database (not the one we're dropping)
            $db = \Config\Database::connect('default', false);
            
            // Drop and recreate test database
            CLI::write('Dropping test database...', 'yellow');
            $db->query('DROP DATABASE IF EXISTS kewasnet_test');
            
            CLI::write('Creating test database...', 'yellow');
            $db->query('CREATE DATABASE kewasnet_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            
            CLI::write('✓ Test database recreated', 'green');
            CLI::newLine();

            // Now switch to test database and run migrations
            $config->default['database'] = 'kewasnet_test';
            $testDb = \Config\Database::connect('default', false);
            
            CLI::write('Running migrations on test database...', 'yellow');
            $migrate = \Config\Services::migrations(null, $testDb);
            
            if ($migrate->latest()) {
                CLI::write('✓ Migrations completed successfully!', 'green');
                
                // Count tables
                $tables = $testDb->listTables();
                $tableCount = count($tables);
                CLI::write("Tables created: {$tableCount}", 'green');
            } else {
                CLI::error('Migrations failed!');
            }

            CLI::newLine();
            CLI::write('===========================================', 'green');
            CLI::write('  Test Database Ready for Seeding!', 'green');
            CLI::write('===========================================', 'green');
            CLI::write('Run: php spark db:seed-test');
            CLI::newLine();

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            CLI::write($e->getTraceAsString());
        } finally {
            // Restore original database
            $config->default['database'] = $originalDb;
            CLI::write("Restored connection to: {$originalDb}");
        }
    }
}
