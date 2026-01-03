<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class SeedTestDatabase extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:seed-test';
    protected $description = 'Run seeders on the test database';

    public function run(array $params)
    {
        CLI::write('===========================================', 'green');
        CLI::write('  Running Seeders on Test Database', 'green');
        CLI::write('===========================================', 'green');
        CLI::newLine();

        // Get database configuration
        $config = config('Database');
        $originalDb = $config->default['database'];

        CLI::write("Original database: {$originalDb}");
        CLI::write("Target database: kewasnet_test");
        CLI::newLine();

        try {
            // Switch to test database
            $config->default['database'] = 'kewasnet_test';

            // Force a fresh connection by clearing the connection group
            $db = \Config\Database::connect('default', true);

            // Test connection
            if (!$db->tableExists('migrations')) {
                CLI::error('Error: Test database not found or migrations not run!');
                CLI::write('Please run migrations first: php spark migrate:test');
                return;
            }

            CLI::write('✓ Connected to kewasnet_test', 'green');
            CLI::newLine();

            // Get seeder class name (optional parameter)
            $seederClass = $params[0] ?? 'DatabaseSeeder';

            if (!class_exists("App\\Database\\Seeds\\{$seederClass}")) {
                CLI::error("Seeder class not found: {$seederClass}");
                return;
            }

            CLI::write("Running seeder: {$seederClass}...", 'yellow');
            CLI::newLine();

            // Run the seeder
            $seeder = \Config\Database::seeder();
            $seeder->call($seederClass);

            CLI::newLine();
            CLI::write('✓ Seeders completed successfully!', 'green');
            CLI::newLine();

            // Get record counts from key tables
            CLI::write('Record Counts:', 'yellow');
            $tables = [
                'system_users',
                'roles',
                'blogs',
                'blog_posts',
                'courses',
                'forums',
                'discussions',
                'events',
                'job_opportunities',
                'resources',
                'pillars',
                'settings'
            ];

            foreach ($tables as $table) {
                if ($db->tableExists($table)) {
                    $count = $db->table($table)->countAll();
                    if ($count > 0) {
                        CLI::write("  ✓ {$table}: {$count} records", 'green');
                    }
                }
            }

            CLI::newLine();
            CLI::write('===========================================', 'green');
            CLI::write('  SUCCESS!', 'green');
            CLI::write('===========================================', 'green');
            CLI::write('All seeders ran successfully on test DB');
            CLI::newLine();

        } catch (\Exception $e) {
            CLI::error('Error running seeders: ' . $e->getMessage());
            CLI::write($e->getTraceAsString());
        } finally {
            // Restore original database
            $config->default['database'] = $originalDb;
            CLI::write("Restored connection to: {$originalDb}");
        }
    }
}
