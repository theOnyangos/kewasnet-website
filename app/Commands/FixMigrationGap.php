<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixMigrationGap extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:fix-gap';
    protected $description = 'Remove orphaned migration record';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('migrations')
            ->where('version', '2025-12-24-114648')
            ->delete();
        
        CLI::write("Deleted {$result} orphaned migration record(s)", 'green');
    }
}

