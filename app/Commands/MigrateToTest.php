<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MigrateToTest extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:test';
    protected $description = 'Run migrations on test database';

    public function run(array $params)
    {
        CLI::write('===========================================', 'yellow');
        CLI::write('  Running Migrations on Test Database', 'yellow');
        CLI::write('===========================================', 'yellow');
        CLI::newLine();

        // Get database config
        $config = config('Database');
        $originalDb = $config->default['database'];
        
        CLI::write("Original database: $originalDb", 'cyan');
        CLI::write("Target database: kewasnet_test", 'cyan');
        CLI::newLine();
        
        // Temporarily switch database in config
        $config->default['database'] = 'kewasnet_test';
        
        // Create new connection with test database
        $db = \Config\Database::connect('default', false);
        
        // Verify connection
        try {
            $db->query('SELECT 1');
            CLI::write("✓ Connected to kewasnet_test", 'green');
        } catch (\Exception $e) {
            CLI::write("✗ Failed to connect to test database", 'red');
            CLI::write("Error: " . $e->getMessage(), 'red');
            $config->default['database'] = $originalDb;
            return;
        }
        
        CLI::newLine();
        CLI::write('Running migrations...', 'yellow');
        CLI::newLine();
        
        // Get migration runner with test database connection
        $migrate = \Config\Services::migrations(null, $db);
        
        try {
            // Run all migrations
            if ($migrate->latest()) {
                CLI::write('✓ Migrations completed successfully!', 'green');
                CLI::newLine();
                
                // Show what was created
                $tables = $db->listTables();
                CLI::write('Tables created: ' . count($tables), 'cyan');
                
                // Show migrations run
                $migrations = $db->table('migrations')->orderBy('id', 'ASC')->get()->getResult();
                CLI::write('Migrations executed: ' . count($migrations), 'cyan');
                CLI::newLine();
                
                // Show migration details
                CLI::write('Migration Details:', 'yellow');
                foreach ($migrations as $migration) {
                    CLI::write("  ✓ " . basename($migration->class), 'green');
                }
                
                CLI::newLine();
                
                // Verify UUID tables
                $uuidQuery = "
                    SELECT table_name 
                    FROM information_schema.columns 
                    WHERE table_schema = 'kewasnet_test'
                    AND column_name = 'id' 
                    AND data_type = 'varchar' 
                    AND character_maximum_length = 36
                    AND (
                        table_name LIKE 'course%' 
                        OR table_name LIKE 'quiz%'
                        OR table_name IN ('lecture_attachments', 'lecture_links', 'vimeo_videos', 'user_progress')
                    )
                ";
                
                $uuidTables = $db->query($uuidQuery)->getResult();
                CLI::write('UUID Tables in Learning Hub:', 'yellow');
                CLI::write('Found ' . count($uuidTables) . ' tables with UUID primary keys', 'cyan');
                
                if (count($uuidTables) > 0) {
                    foreach ($uuidTables as $table) {
                        CLI::write("  ✓ " . $table->table_name, 'green');
                    }
                }
                
                CLI::newLine();
                CLI::write('===========================================', 'yellow');
                CLI::write('  SUCCESS!', 'green');
                CLI::write('===========================================', 'yellow');
                CLI::write('All migrations ran successfully on test DB', 'green');
                
            } else {
                CLI::write('✗ Migration failed or nothing to migrate', 'yellow');
            }
        } catch (\Exception $e) {
            CLI::write('✗ Migration Error!', 'red');
            CLI::write('Error: ' . $e->getMessage(), 'red');
            CLI::newLine();
            
            // Show file and line for debugging
            CLI::write('File: ' . $e->getFile(), 'yellow');
            CLI::write('Line: ' . $e->getLine(), 'yellow');
        }
        
        CLI::newLine();
        
        // Restore original database
        $config->default['database'] = $originalDb;
        CLI::write("Restored connection to: $originalDb", 'cyan');
    }
}
