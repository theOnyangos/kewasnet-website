<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RunBatch76Migrations extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:run-batch76';
    protected $description = 'Run batch 76 migrations manually';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        try {
            // Run CreateVimeoVideosTable
            if (!$db->tableExists('vimeo_videos')) {
                require_once APPPATH . 'Database/Migrations/2025-12-24-111840_CreateVimeoVideosTable.php';
                $migration1 = new \App\Database\Migrations\CreateVimeoVideosTable();
                $migration1->up();
                CLI::write('Created vimeo_videos table', 'green');
                
                $db->table('migrations')->insert([
                    'version' => '2025-12-24-111840',
                    'class' => 'App\\Database\\Migrations\\CreateVimeoVideosTable',
                    'group' => 'default',
                    'namespace' => 'App',
                    'time' => time(),
                    'batch' => 76
                ]);
            } else {
                CLI::write('vimeo_videos table already exists', 'yellow');
            }
            
            // Run CreateUserProgressTable
            if (!$db->tableExists('user_progress')) {
                require_once APPPATH . 'Database/Migrations/2025-12-24-111845_CreateUserProgressTable.php';
                $migration2 = new \App\Database\Migrations\CreateUserProgressTable();
                $migration2->up();
                CLI::write('Created user_progress table', 'green');
                
                $db->table('migrations')->insert([
                    'version' => '2025-12-24-111845',
                    'class' => 'App\\Database\\Migrations\\CreateUserProgressTable',
                    'group' => 'default',
                    'namespace' => 'App',
                    'time' => time(),
                    'batch' => 76
                ]);
            } else {
                CLI::write('user_progress table already exists', 'yellow');
            }
            
            CLI::write('Batch 76 migrations completed!', 'green');
        } catch (\Exception $e) {
            CLI::write('Error: ' . $e->getMessage(), 'red');
        }
    }
}

