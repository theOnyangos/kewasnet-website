<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = '';
    public string $fromName   = '';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost;

    /**
     * SMTP Username
     */
    public string $SMTPUser;

    /**
     * SMTP Password
     */
    public string $SMTPPass;

    /**
     * SMTP Port
     */
    public int $SMTPPort;

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 5;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption.
     *
     * @var string '', 'tls' or 'ssl'. 'tls' will issue a STARTTLS command
     *             to the server. 'ssl' means implicit SSL. Connection on port
     *             465 should set this to ''.
     */
    public string $SMTPCrypto;

    public function __construct()
    {
        parent::__construct();

        // Check environment: use database in production, env vars in development
        $environment = env('CI_ENVIRONMENT', 'production');

        if ($environment === 'production') {
            // Load email settings from database in production
            $this->loadFromDatabase();
        } else {
            // Load email settings from environment variables in development
            $this->loadFromEnvironment();
        }
    }

    /**
     * Load email settings from database (for production)
     */
    private function loadFromDatabase(): void
    {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('email_settings');
            $settings = $builder->orderBy('id', 'DESC')->get(1)->getRowArray();

            if ($settings) {
                $this->SMTPHost   = $settings['host'] ?? env('EMAIL_HOST', 'sandbox.smtp.mailtrap.io');
                $this->SMTPUser   = $settings['username'] ?? env('EMAIL_USERNAME', '');
                $this->SMTPPass   = $settings['password'] ?? env('EMAIL_PASSWORD', '');
                $this->SMTPPort   = (int) ($settings['port'] ?? env('EMAIL_PORT', 2525));
                $this->SMTPCrypto = $settings['encryption'] ?? env('EMAIL_ENCRYPTION', 'tls');
                $this->fromEmail  = $settings['from_address'] ?? env('EMAIL_FROM_ADDRESS', 'info@kewasnet.co.ke');
                $this->fromName   = $settings['from_name'] ?? env('EMAIL_FROM_NAME', 'KEWASNET');
            } else {
                // Fallback to environment variables if no database settings found
                $this->loadFromEnvironment();
            }
        } catch (\Exception $e) {
            // If database query fails, fallback to environment variables
            log_message('error', 'Email config: Failed to load from database, using environment variables - ' . $e->getMessage());
            $this->loadFromEnvironment();
        }
    }

    /**
     * Load email settings from environment variables (for development)
     */
    private function loadFromEnvironment(): void
    {
        $this->SMTPHost   = env('EMAIL_HOST', 'sandbox.smtp.mailtrap.io');
        $this->SMTPUser   = env('EMAIL_USERNAME', '');
        $this->SMTPPass   = env('EMAIL_PASSWORD', '');
        $this->SMTPPort   = (int) env('EMAIL_PORT', 2525);
        $this->SMTPCrypto = env('EMAIL_ENCRYPTION', 'tls');
        $this->fromEmail  = env('EMAIL_FROM_ADDRESS', 'info@kewasnet.co.ke');
        $this->fromName   = env('EMAIL_FROM_NAME', 'KEWASNET');
    }

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = false;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;
}
