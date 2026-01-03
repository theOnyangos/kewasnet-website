<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BackupDatabase extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:backup';
    protected $description = 'Creates a backup of the database';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $dbConfig = config('Database')->default;
        
        CLI::write('Database Backup Tool', 'yellow');
        CLI::newLine();
        
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = WRITEPATH . "backups/db_backup_{$timestamp}.sql";
        
        // Create backups directory if it doesn't exist
        if (!is_dir(WRITEPATH . 'backups')) {
            mkdir(WRITEPATH . 'backups', 0755, true);
        }
        
        CLI::write("Creating backup: $backupFile", 'cyan');
        
        // Parse hostname and port
        $hostname = $dbConfig['hostname'];
        $port = 3306;
        if (strpos($hostname, ':') !== false) {
            list($hostname, $port) = explode(':', $hostname);
        }
        
        // Build mysqldump command
        $command = sprintf(
            '/Applications/XAMPP/bin/mysqldump -h %s -P %s -u %s %s %s > %s',
            $hostname,
            $port,
            $dbConfig['username'],
            !empty($dbConfig['password']) ? "-p{$dbConfig['password']}" : '',
            $dbConfig['database'],
            escapeshellarg($backupFile)
        );
        
        // Execute backup
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            $size = filesize($backupFile);
            $sizeInMB = round($size / 1024 / 1024, 2);
            CLI::write("✓ Backup created successfully: {$sizeInMB} MB", 'green');
            CLI::write("Location: $backupFile", 'cyan');
        } else {
            CLI::write("✗ Backup failed", 'red');
        }
        
        CLI::newLine();
        
        // List all backups
        $backups = glob(WRITEPATH . 'backups/db_backup_*.sql');
        if (count($backups) > 0) {
            CLI::write('Available backups:', 'yellow');
            foreach ($backups as $backup) {
                $size = filesize($backup);
                $sizeInMB = round($size / 1024 / 1024, 2);
                $name = basename($backup);
                CLI::write("  - $name ({$sizeInMB} MB)", 'cyan');
            }
        }
    }
}
