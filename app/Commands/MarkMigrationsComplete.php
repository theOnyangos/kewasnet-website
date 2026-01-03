<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MarkMigrationsComplete extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:mark-migrations-complete';
    protected $description = 'Marks all migration files as executed without running them';

    public function run(array $params)
    {
        CLI::write('Mark Migrations as Complete', 'yellow');
        CLI::write('This will register all migration files as executed in the database', 'yellow');
        CLI::newLine();
        
        $db = \Config\Database::connect();
        $migrationsPath = APPPATH . 'Database/Migrations/';
        
        // Get all migration files
        $migrationFiles = glob($migrationsPath . '*.php');
        sort($migrationFiles);
        
        if (empty($migrationFiles)) {
            CLI::write('No migration files found!', 'red');
            return;
        }
        
        CLI::write("Found " . count($migrationFiles) . " migration files", 'cyan');
        CLI::newLine();
        
        $confirm = CLI::prompt('Do you want to mark these migrations as executed?', ['y', 'n'], 'required');
        
        if ($confirm !== 'y') {
            CLI::write('Operation cancelled', 'yellow');
            return;
        }
        
        CLI::newLine();
        CLI::write('Marking migrations as executed...', 'green');
        
        $batch = 1;
        $marked = 0;
        
        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            $parts = explode('_', $filename);
            $version = $parts[0]; // e.g., "2025-12-27-180000"
            
            // Extract class name from filename
            $className = str_replace('.php', '', implode('_', array_slice($parts, 1)));
            $fullClassName = "App\\Database\\Migrations\\$className";
            
            // Check if already exists
            $exists = $db->table('migrations')
                ->where('version', $version)
                ->countAllResults();
            
            if ($exists > 0) {
                CLI::write("⊘ Already marked: $filename", 'yellow');
                continue;
            }
            
            // Insert migration record
            $db->table('migrations')->insert([
                'version' => $version,
                'class' => $fullClassName,
                'group' => 'default',
                'namespace' => 'App',
                'time' => time(),
                'batch' => $batch
            ]);
            
            CLI::write("✓ Marked: $filename", 'green');
            $marked++;
        }
        
        CLI::newLine();
        CLI::write("Complete! Marked $marked migrations as executed", 'green');
    }
}
