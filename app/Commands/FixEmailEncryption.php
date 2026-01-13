<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Fix Email Encryption Setting
 * 
 * Updates email encryption to empty string (no encryption) for SMTP servers
 * that don't support STARTTLS.
 * 
 * Usage:
 *   php spark email:fix-encryption
 */
class FixEmailEncryption extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'email:fix-encryption';
    protected $description = 'Fix email encryption setting to work with SMTP server';

    public function run(array $params)
    {
        CLI::write('========================================', 'cyan');
        CLI::write('  Fix Email Encryption Setting', 'cyan');
        CLI::write('========================================', 'cyan');
        CLI::newLine();

        $db = \Config\Database::connect();
        $builder = $db->table('email_settings');
        
        // Get current settings
        $current = $builder->orderBy('id', 'DESC')->get(1)->getRowArray();
        
        if (!$current) {
            CLI::error('No email settings found in database!');
            return;
        }

        CLI::write('Current encryption: ' . ($current['encryption'] ?: 'empty'), 'yellow');
        CLI::newLine();

        // Update to empty string (no encryption)
        $result = $builder->where('id', $current['id'])->update(['encryption' => '']);

        if ($result) {
            CLI::write('✓ Encryption setting updated to empty string (no encryption)', 'green');
            CLI::write('  This should fix the "503 STARTTLS command used when not advertised" error', 'cyan');
            CLI::newLine();
            CLI::write('Test with: php spark email:test-production your-email@example.com', 'yellow');
        } else {
            CLI::error('Failed to update encryption setting');
        }
    }
}
