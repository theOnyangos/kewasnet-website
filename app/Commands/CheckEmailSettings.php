<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\EmailSettings;

/**
 * Check Email Settings Command
 * 
 * Displays and verifies email settings from database (production) and environment (development).
 * 
 * Usage:
 *   php spark email:settings              - Show current settings
 *   php spark email:settings --env=production  - Force production mode
 *   php spark email:settings --env=development - Force development mode
 */
class CheckEmailSettings extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'email:settings';
    protected $description = 'Check and verify email settings from database and environment';

    public function run(array $params)
    {
        $envOverride = CLI::getOptionString('env');
        $currentEnv = env('CI_ENVIRONMENT', 'production');
        
        if ($envOverride) {
            $currentEnv = $envOverride;
        }

        CLI::write('========================================', 'cyan');
        CLI::write('  Email Settings Check', 'cyan');
        CLI::write('========================================', 'cyan');
        CLI::newLine();

        CLI::write("Current Environment: {$currentEnv}", 'yellow');
        CLI::newLine();

        // Check database settings (production)
        CLI::write('Database Settings (Production):', 'cyan');
        CLI::write('--------------------------------', 'cyan');
        $this->checkDatabaseSettings();
        CLI::newLine();

        // Check environment variables (development)
        CLI::write('Environment Variables (Development):', 'cyan');
        CLI::write('------------------------------------', 'cyan');
        $this->checkEnvironmentSettings();
        CLI::newLine();

        // Show what's actually being used
        CLI::write('Active Configuration (What CodeIgniter is using):', 'cyan');
        CLI::write('------------------------------------------------', 'cyan');
        $this->showActiveConfiguration($currentEnv);
        CLI::newLine();

        // Recommendations
        $this->showRecommendations($currentEnv);
    }

    /**
     * Check database settings
     */
    private function checkDatabaseSettings()
    {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('email_settings');
            $settings = $builder->orderBy('id', 'DESC')->get(1)->getRowArray();

            if (!$settings) {
                CLI::write('  ⚠ No email settings found in database', 'yellow');
                CLI::write('  → System will fallback to environment variables');
                return;
            }

            CLI::write('  ✓ Database settings found', 'green');
            CLI::write('  ID: ' . ($settings['id'] ?? 'N/A'), 'white');
            CLI::write('  Host: ' . ($settings['host'] ?? 'not set'), 'white');
            CLI::write('  Port: ' . ($settings['port'] ?? 'not set'), 'white');
            CLI::write('  Username: ' . ($this->maskValue($settings['username'] ?? '')), 'white');
            CLI::write('  Password: ' . ($settings['password'] ? '***' : 'not set'), 'white');
            CLI::write('  Encryption: ' . ($settings['encryption'] ?? 'not set'), 'white');
            CLI::write('  From Address: ' . ($settings['from_address'] ?? 'not set'), 'white');
            CLI::write('  From Name: ' . ($settings['from_name'] ?? 'not set'), 'white');
            CLI::write('  Enabled: ' . (($settings['email_enabled'] ?? 1) ? 'Yes' : 'No'), 'white');
            CLI::write('  Created: ' . ($settings['created_at'] ?? 'N/A'), 'white');
            CLI::write('  Updated: ' . ($settings['updated_at'] ?? 'N/A'), 'white');

            // Validate settings
            $this->validateSettings($settings, 'database');

        } catch (\Exception $e) {
            CLI::write('  ✗ Error accessing database: ' . $e->getMessage(), 'red');
            CLI::write('  → ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    /**
     * Check environment settings
     */
    private function checkEnvironmentSettings()
    {
        $envVars = [
            'EMAIL_HOST'         => env('EMAIL_HOST'),
            'EMAIL_USERNAME'     => env('EMAIL_USERNAME'),
            'EMAIL_PASSWORD'     => env('EMAIL_PASSWORD'),
            'EMAIL_PORT'         => env('EMAIL_PORT'),
            'EMAIL_ENCRYPTION'   => env('EMAIL_ENCRYPTION'),
            'EMAIL_FROM_ADDRESS' => env('EMAIL_FROM_ADDRESS'),
            'EMAIL_FROM_NAME'    => env('EMAIL_FROM_NAME'),
        ];

        $allSet = true;
        $missing = [];

        foreach ($envVars as $key => $value) {
            if (empty($value)) {
                $allSet = false;
                $missing[] = $key;
                CLI::write("  ⚠ {$key}: not set", 'yellow');
            } else {
                if ($key === 'EMAIL_PASSWORD') {
                    CLI::write("  ✓ {$key}: ***", 'green');
                } else {
                    CLI::write("  ✓ {$key}: " . $this->maskValue($value), 'green');
                }
            }
        }

        if ($allSet) {
            CLI::write('  ✓ All environment variables are set', 'green');
        } else {
            CLI::write('  ⚠ Missing variables: ' . implode(', ', $missing), 'yellow');
        }

        // Validate settings
        $this->validateSettings($envVars, 'environment');
    }

    /**
     * Show active configuration
     */
    private function showActiveConfiguration($environment)
    {
        $emailConfig = config('Email');

        CLI::write('  Environment: ' . $environment, 'white');
        CLI::write('  Protocol: ' . ($emailConfig->protocol ?? 'not set'), 'white');
        CLI::write('  SMTP Host: ' . ($emailConfig->SMTPHost ?? 'not set'), 'white');
        CLI::write('  SMTP Port: ' . ($emailConfig->SMTPPort ?? 'not set'), 'white');
        CLI::write('  SMTP User: ' . $this->maskValue($emailConfig->SMTPUser ?? ''), 'white');
        CLI::write('  SMTP Pass: ' . ($emailConfig->SMTPPass ? '***' : 'not set'), 'white');
        CLI::write('  SMTP Crypto: ' . ($emailConfig->SMTPCrypto ?? 'not set'), 'white');
        CLI::write('  From Email: ' . ($emailConfig->fromEmail ?? 'not set'), 'white');
        CLI::write('  From Name: ' . ($emailConfig->fromName ?? 'not set'), 'white');

        // Determine source
        if ($environment === 'production') {
            try {
                $db = \Config\Database::connect();
                $builder = $db->table('email_settings');
                $settings = $builder->orderBy('id', 'DESC')->get(1)->getRowArray();
                
                if ($settings) {
                    CLI::write('  Source: Database (email_settings table)', 'green');
                } else {
                    CLI::write('  Source: Environment variables (fallback)', 'yellow');
                }
            } catch (\Exception $e) {
                CLI::write('  Source: Environment variables (fallback - DB error)', 'yellow');
            }
        } else {
            CLI::write('  Source: Environment variables (.env file)', 'green');
        }
    }

    /**
     * Validate settings
     */
    private function validateSettings($settings, $source)
    {
        $issues = [];

        // Check required fields
        if (empty($settings['host'] ?? $settings['EMAIL_HOST'] ?? null)) {
            $issues[] = 'SMTP Host is missing';
        }

        if (empty($settings['port'] ?? $settings['EMAIL_PORT'] ?? null)) {
            $issues[] = 'SMTP Port is missing';
        }

        if (empty($settings['username'] ?? $settings['EMAIL_USERNAME'] ?? null)) {
            $issues[] = 'SMTP Username is missing';
        }

        if (empty($settings['password'] ?? $settings['EMAIL_PASSWORD'] ?? null)) {
            $issues[] = 'SMTP Password is missing';
        }

        if (empty($settings['from_address'] ?? $settings['EMAIL_FROM_ADDRESS'] ?? null)) {
            $issues[] = 'From Email Address is missing';
        }

        // Validate email format
        $fromEmail = $settings['from_address'] ?? $settings['EMAIL_FROM_ADDRESS'] ?? null;
        if ($fromEmail && !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            $issues[] = 'From Email Address is invalid: ' . $fromEmail;
        }

        // Validate port
        $port = $settings['port'] ?? $settings['EMAIL_PORT'] ?? null;
        if ($port && (!is_numeric($port) || $port < 1 || $port > 65535)) {
            $issues[] = 'SMTP Port is invalid: ' . $port;
        }

        // Validate encryption
        $encryption = $settings['encryption'] ?? $settings['EMAIL_ENCRYPTION'] ?? null;
        if ($encryption && !in_array(strtolower($encryption), ['', 'tls', 'ssl'])) {
            $issues[] = 'SMTP Encryption is invalid: ' . $encryption . ' (should be tls, ssl, or empty)';
        }

        if (!empty($issues)) {
            CLI::write('  ⚠ Validation Issues:', 'yellow');
            foreach ($issues as $issue) {
                CLI::write('    - ' . $issue, 'yellow');
            }
        } else {
            CLI::write('  ✓ All settings are valid', 'green');
        }
    }

    /**
     * Show recommendations
     */
    private function showRecommendations($environment)
    {
        CLI::write('Recommendations:', 'yellow');

        if ($environment === 'production') {
            CLI::write('  1. Verify database settings are correct', 'cyan');
            CLI::write('  2. Test with: php spark email:test your-email@example.com --env=production --direct', 'cyan');
            CLI::write('  3. Check email_settings table in database', 'cyan');
            CLI::write('  4. Ensure email_enabled = 1 in database', 'cyan');
        } else {
            CLI::write('  1. Verify .env file has all required EMAIL_* variables', 'cyan');
            CLI::write('  2. Test with: php spark email:test your-email@example.com --env=development --direct', 'cyan');
            CLI::write('  3. Check .env file in project root', 'cyan');
        }

        CLI::newLine();
    }

    /**
     * Mask sensitive values
     */
    private function maskValue($value)
    {
        if (empty($value)) {
            return 'not set';
        }

        if (strlen($value) <= 3) {
            return '***';
        }

        return substr($value, 0, 3) . '***';
    }
}
