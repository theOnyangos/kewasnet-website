<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckMigrationStatus extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'migrate:check-status';
    protected $description = 'Check migration status in database';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        $version = '2025-12-24-111848';
        
        $existing = $db->table('migrations')
            ->where('version', $version)
            ->get()
            ->getRow();
        
        if ($existing) {
            CLI::write("Migration $version exists in database:", 'green');
            print_r($existing);
        } else {
            CLI::write("Migration $version NOT found in database", 'red');
        }

        // Now insert it properly
        CLI::write("\nInserting migration record...", 'yellow');
        
        try {
            $db->table('migrations')->insert([
                'version' => $version,
                'class' => 'App\\Database\\Migrations\\EnhanceCourseLecturesTable',
                'group' => 'default',
                'namespace' => 'App',
                'time' => time(),
                'batch' => 77
            ]);
            CLI::write("Successfully inserted!", 'green');
        } catch (\Exception $e) {
            CLI::write("Error: " . $e->getMessage(), 'red');
        }
    }
}
