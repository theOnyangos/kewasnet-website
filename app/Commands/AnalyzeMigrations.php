<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AnalyzeMigrations extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:analyze-migrations';
    protected $description = 'Analyzes and reports on migration files';

    public function run(array $params)
    {
        CLI::write('Migration Analysis Tool', 'yellow');
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
            $executedMigrations[$migration->version] = [
                'class' => $migration->class,
                'batch' => $migration->batch,
                'time' => $migration->time
            ];
        }
        
        // Categorize migrations
        $categories = [
            'core' => [],
            'learning_hub' => [],
            'blog' => [],
            'events' => [],
            'resources' => [],
            'chat' => [],
            'payment' => [],
            'settings' => [],
            'other' => []
        ];
        
        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            $version = substr($filename, 0, 19); // Extract timestamp
            
            $isExecuted = isset($executedMigrations[$version]);
            $batch = $isExecuted ? $executedMigrations[$version]['batch'] : null;
            
            // Categorize based on filename
            if (strpos($filename, 'Course') !== false || strpos($filename, 'Quiz') !== false || 
                strpos($filename, 'Lecture') !== false || strpos($filename, 'Vimeo') !== false) {
                $categories['learning_hub'][] = ['file' => $filename, 'executed' => $isExecuted, 'batch' => $batch];
            } elseif (strpos($filename, 'Blog') !== false) {
                $categories['blog'][] = ['file' => $filename, 'executed' => $isExecuted, 'batch' => $batch];
            } elseif (strpos($filename, 'Event') !== false) {
                $categories['events'][] = ['file' => $filename, 'executed' => $isExecuted, 'batch' => $batch];
            } elseif (strpos($filename, 'Resource') !== false) {
                $categories['resources'][] = ['file' => $filename, 'executed' => $isExecuted, 'batch' => $batch];
            } elseif (strpos($filename, 'Chat') !== false) {
                $categories['chat'][] = ['file' => $filename, 'executed' => $isExecuted, 'batch' => $batch];
            } elseif (strpos($filename, 'Payment') !== false || strpos($filename, 'Mpesa') !== false || 
                      strpos($filename, 'Paystack') !== false || strpos($filename, 'Order') !== false) {
                $categories['payment'][] = ['file' => $filename, 'executed' => $isExecuted, 'batch' => $batch];
            } elseif (strpos($filename, 'Settings') !== false) {
                $categories['settings'][] = ['file' => $filename, 'executed' => $isExecuted, 'batch' => $batch];
            } elseif (strpos($filename, 'Users') !== false || strpos($filename, 'Role') !== false || 
                      strpos($filename, 'Password') !== false || strpos($filename, 'Account') !== false) {
                $categories['core'][] = ['file' => $filename, 'executed' => $isExecuted, 'batch' => $batch];
            } else {
                $categories['other'][] = ['file' => $filename, 'executed' => $isExecuted, 'batch' => $batch];
            }
        }
        
        // Display report
        $totalFiles = count($migrationFiles);
        $totalExecuted = count($executedMigrations);
        $totalPending = $totalFiles - $totalExecuted;
        
        CLI::write("Total migration files: $totalFiles", 'cyan');
        CLI::write("Executed: $totalExecuted", 'green');
        CLI::write("Pending: $totalPending", 'yellow');
        CLI::newLine();
        
        foreach ($categories as $category => $migrations) {
            if (empty($migrations)) continue;
            
            $executed = array_filter($migrations, fn($m) => $m['executed']);
            $pending = array_filter($migrations, fn($m) => !$m['executed']);
            
            CLI::write(strtoupper($category) . " ({count(executed)}/" . count($migrations) . " executed)", 'yellow');
            
            if (!empty($executed)) {
                CLI::write("  Executed:", 'green');
                foreach ($executed as $migration) {
                    $batch = $migration['batch'] ?? 'N/A';
                    CLI::write("    ✓ {$migration['file']} [Batch: $batch]", 'green');
                }
            }
            
            if (!empty($pending)) {
                CLI::write("  Pending:", 'yellow');
                foreach ($pending as $migration) {
                    CLI::write("    ⊘ {$migration['file']}", 'yellow');
                }
            }
            
            CLI::newLine();
        }
    }
}
