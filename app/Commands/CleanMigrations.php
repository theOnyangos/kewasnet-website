<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanMigrations extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:clean-migrations';
    protected $description = 'Removes migration files that have not been executed';

    public function run(array $params)
    {
        CLI::write('Migration Cleanup Tool', 'yellow');
        CLI::write('This will remove migration files that have never been executed', 'yellow');
        CLI::newLine();
        
        $db = \Config\Database::connect();
        $migrationsPath = APPPATH . 'Database/Migrations/';
        
        // Get all migration files
        $migrationFiles = glob($migrationsPath . '*.php');
        sort($migrationFiles);
        
        // Get executed migrations from database
        $executedMigrations = [];
        $migrations = $db->table('migrations')->orderBy('id', 'ASC')->get()->getResult();
        foreach ($migrations as $migration) {
            $executedMigrations[$migration->version] = true;
        }
        
        // Find unexecuted migrations
        $unexecutedFiles = [];
        $executedFiles = [];
        
        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            // Extract timestamp - format: YYYY-MM-DD-HHIISS
            $parts = explode('_', $filename);
            if (count($parts) >= 1) {
                $version = $parts[0]; // e.g., "2024-01-15-062423"
                
                if (isset($executedMigrations[$version])) {
                    $executedFiles[] = $filename;
                } else {
                    $unexecutedFiles[] = [
                        'file' => $filename,
                        'path' => $file,
                        'version' => $version
                    ];
                }
            }
        }
        
        CLI::write("Total migration files: " . count($migrationFiles), 'cyan');
        CLI::write("Executed migrations: " . count($executedFiles), 'green');
        CLI::write("Unexecuted migrations: " . count($unexecutedFiles), 'yellow');
        CLI::newLine();
        
        if (empty($unexecutedFiles)) {
            CLI::write('✓ All migration files have been executed!', 'green');
            CLI::write('Nothing to clean up.', 'cyan');
            return;
        }
        
        CLI::write('Unexecuted migration files to be removed:', 'yellow');
        foreach ($unexecutedFiles as $migration) {
            CLI::write("  - {$migration['file']}", 'red');
        }
        CLI::newLine();
        
        $confirm = CLI::prompt('Do you want to remove these unexecuted migration files?', ['y', 'n'], 'required');
        
        if ($confirm !== 'y') {
            CLI::write('Cleanup cancelled', 'yellow');
            return;
        }
        
        CLI::newLine();
        CLI::write('Removing unexecuted migration files...', 'green');
        
        $removed = 0;
        $failed = 0;
        
        foreach ($unexecutedFiles as $migration) {
            try {
                if (unlink($migration['path'])) {
                    CLI::write("✓ Removed: {$migration['file']}", 'green');
                    $removed++;
                } else {
                    CLI::write("✗ Failed to remove: {$migration['file']}", 'red');
                    $failed++;
                }
            } catch (\Exception $e) {
                CLI::write("✗ Error removing {$migration['file']}: " . $e->getMessage(), 'red');
                $failed++;
            }
        }
        
        CLI::newLine();
        CLI::write("Cleanup complete!", 'green');
        CLI::write("Removed: $removed files", 'green');
        if ($failed > 0) {
            CLI::write("Failed: $failed files", 'red');
        }
        CLI::write("Remaining: " . count($executedFiles) . " migration files", 'cyan');
    }
}
