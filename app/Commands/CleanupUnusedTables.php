<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanupUnusedTables extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:cleanup';
    protected $description = 'Removes unused tables from the database';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Database Cleanup Tool', 'yellow');
        CLI::write('This will remove unused/duplicate tables from the database', 'yellow');
        CLI::newLine();
        
        // Tables to definitely remove (confirmed unused)
        $tablesToRemove = [
            // Backup/temporary tables
            'youtube_links_backup',
            'youtube_links_new',
            
            // Duplicate tables
            'role', // duplicate of 'roles'
            'categories', // duplicate of blog_categories or course_categories
            
            // Old/unused feature tables (confirm with user first)
            'answers', // Not part of quiz system (quiz_answers exists)
            'questions', // Not part of quiz system (quiz_questions exists)
            'comments', // Replaced by specific comment tables
            'connections', // Feature not implemented
            'blocked_users', // Feature not implemented
            'archived_events', // Old feature
            'careers', // Feature removed
            'enquiries', // Old feature
        ];
        
        CLI::write('Tables to be removed:', 'yellow');
        foreach ($tablesToRemove as $table) {
            $exists = $db->tableExists($table);
            $count = 0;
            if ($exists) {
                $count = $db->table($table)->countAll();
            }
            $status = $exists ? "EXISTS (rows: $count)" : 'NOT FOUND';
            CLI::write("  - $table [$status]", $exists ? 'green' : 'red');
        }
        CLI::newLine();
        
        $confirm = CLI::prompt('Do you want to proceed with cleanup?', ['y', 'n'], 'required');
        
        if ($confirm !== 'y') {
            CLI::write('Cleanup cancelled', 'yellow');
            return;
        }
        
        CLI::newLine();
        CLI::write('Starting cleanup...', 'green');
        
        foreach ($tablesToRemove as $table) {
            if ($db->tableExists($table)) {
                try {
                    $db->query("DROP TABLE IF EXISTS `$table`");
                    CLI::write("✓ Dropped table: $table", 'green');
                } catch (\Exception $e) {
                    CLI::write("✗ Failed to drop $table: " . $e->getMessage(), 'red');
                }
            } else {
                CLI::write("⊘ Table $table does not exist", 'yellow');
            }
        }
        
        CLI::newLine();
        CLI::write('Cleanup complete!', 'green');
        CLI::newLine();
        
        // Show statistics
        $totalTables = count($db->listTables());
        CLI::write("Remaining tables: $totalTables", 'cyan');
    }
}
