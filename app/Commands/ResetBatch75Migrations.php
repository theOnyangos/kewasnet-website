<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetBatch75Migrations extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:reset-batch75';
    protected $description = 'Remove batch 75 migration records to force re-run';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        $migrations = [
            '2025-12-24-111756',
            '2025-12-24-111801',
            '2025-12-24-111805',
            '2025-12-24-111809',
            '2025-12-24-111814',
            '2025-12-24-111819',
            '2025-12-24-111827',
            '2025-12-24-111834',
        ];
        
        $deleted = 0;
        foreach ($migrations as $version) {
            $result = $db->table('migrations')->where('version', $version)->delete();
            $deleted += $result;
        }
        
        CLI::write("Deleted {$deleted} migration record(s) from batch 75", 'green');
    }
}

