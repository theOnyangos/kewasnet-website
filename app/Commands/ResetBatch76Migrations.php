<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetBatch76Migrations extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:reset-batch76';
    protected $description = 'Remove batch 76 migration records to force re-run';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        $migrations = [
            '2025-12-24-111840',
            '2025-12-24-111845',
        ];
        
        $deleted = 0;
        foreach ($migrations as $version) {
            $result = $db->table('migrations')->where('version', $version)->delete();
            $deleted += $result;
        }
        
        CLI::write("Deleted {$deleted} migration record(s) from batch 76", 'green');
    }
}

