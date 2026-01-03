<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FreshSeedTest extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:fresh-seed-test';
    protected $description = 'Drop, recreate, migrate, and seed test database in one command';

    public function run(array $params)
    {
        CLI::write('===========================================', 'yellow');
        CLI::write('  Fresh Seed Test Database', 'yellow');
        CLI::write('===========================================', 'yellow');
        CLI::newLine();

        // Get database configuration
        $config = config('Database');
        $originalDb = $config->default['database'];
        
        if ($originalDb === 'kewasnet_test') {
            CLI::error('Cannot run this command when default database is kewasnet_test!');
            CLI::error('This would affect your current working database.');
            return;
        }

        CLI::write("✓ Production database (safe): {$originalDb}", 'green');
        CLI::write("→ Working on: kewasnet_test", 'yellow');
        CLI::newLine();

        try {
            // Connect to main database to drop/create test db
            $db = \Config\Database::connect('default', false);
            
            CLI::write('→ Dropping test database...', 'yellow');
            $db->query('DROP DATABASE IF EXISTS kewasnet_test');
            
            CLI::write('→ Creating test database...', 'yellow');
            $db->query('CREATE DATABASE kewasnet_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            CLI::write('✓ Test database recreated', 'green');
            CLI::newLine();

            // Switch to test database
            $config->default['database'] = 'kewasnet_test';
            $testDb = \Config\Database::connect('default', true);
            
            CLI::write('→ Running migrations...', 'yellow');
            $migrate = \Config\Services::migrations(null, $testDb);
            
            if ($migrate->latest()) {
                $tables = $testDb->listTables();
                $tableCount = count($tables);
                CLI::write("✓ Migrations completed: {$tableCount} tables created", 'green');
            } else {
                CLI::error('✗ Migrations failed!');
                return;
            }
            CLI::newLine();

            // Run seeders
            CLI::write('→ Running seeders...', 'yellow');
            CLI::newLine();
            
            $seeder = \Config\Database::seeder();
            $seederClass = $params[0] ?? 'DatabaseSeeder';
            
            $seeder->call($seederClass);

            CLI::newLine();
            CLI::write('===========================================', 'green');
            CLI::write('  ✓ SUCCESS!', 'green');
            CLI::write('===========================================', 'green');
            CLI::newLine();

            // Get record counts
            CLI::write('Record Counts:', 'cyan');
            $tables = [
                'roles',
                'system_users',
                'blogs',
                'blog_posts',
                'events',
                'partners',
                'forums',
                'pillars',
            ];

            foreach ($tables as $table) {
                if ($testDb->tableExists($table)) {
                    $count = $testDb->table($table)->countAll();
                    if ($count > 0) {
                        CLI::write("  {$table}: {$count}", 'white');
                    }
                }
            }

            CLI::newLine();

        } catch (\Exception $e) {
            CLI::error('✗ Error: ' . $e->getMessage());
            CLI::newLine();
            CLI::write('Stack trace:', 'yellow');
            CLI::write($e->getTraceAsString());
        } finally {
            // Restore original database
            $config->default['database'] = $originalDb;
            CLI::write("→ Restored connection to: {$originalDb}", 'cyan');
        }
    }
}
