<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateTrackingTables extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'tracking:create-tables';
    protected $description = 'Create activity tracking tables manually';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            CLI::write('Creating activity tracking tables...', 'green');

            // Create user_sessions table
            $sql = "CREATE TABLE IF NOT EXISTS user_sessions (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(128) NOT NULL UNIQUE,
                user_id INT(11) UNSIGNED NULL,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT NOT NULL,
                browser VARCHAR(100) NULL,
                device VARCHAR(100) NULL,
                os VARCHAR(100) NULL,
                country VARCHAR(100) NULL,
                city VARCHAR(100) NULL,
                referrer TEXT NULL,
                analytics_consent BOOLEAN DEFAULT FALSE,
                marketing_consent BOOLEAN DEFAULT FALSE,
                session_start DATETIME NOT NULL,
                session_end DATETIME NULL,
                page_views INT(11) DEFAULT 0,
                total_events INT(11) DEFAULT 0,
                is_bounce BOOLEAN DEFAULT TRUE,
                last_activity DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_session_id (session_id),
                INDEX idx_user_id (user_id),
                INDEX idx_session_start (session_start),
                INDEX idx_session_end (session_end)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            
            $db->query($sql);
            CLI::write('✅ user_sessions table created successfully', 'green');

            // Create page_views table
            $sql = "CREATE TABLE IF NOT EXISTS page_views (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                session_id INT(11) UNSIGNED NOT NULL,
                page_url VARCHAR(500) NOT NULL,
                page_title VARCHAR(300) NULL,
                page_category VARCHAR(100) NULL,
                referrer VARCHAR(500) NULL,
                viewed_at DATETIME NOT NULL,
                time_on_page INT(11) DEFAULT 0,
                scroll_depth INT(3) DEFAULT 0,
                is_exit BOOLEAN DEFAULT FALSE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (session_id) REFERENCES user_sessions(id) ON DELETE CASCADE,
                INDEX idx_session_id (session_id),
                INDEX idx_page_url (page_url(255)),
                INDEX idx_viewed_at (viewed_at),
                INDEX idx_page_category (page_category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            
            $db->query($sql);
            CLI::write('✅ page_views table created successfully', 'green');

            // Create user_events table
            $sql = "CREATE TABLE IF NOT EXISTS user_events (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                session_id INT(11) UNSIGNED NOT NULL,
                page_view_id INT(11) UNSIGNED NULL,
                event_type VARCHAR(100) NOT NULL,
                event_action VARCHAR(100) NOT NULL,
                event_label VARCHAR(300) NULL,
                event_value VARCHAR(300) NULL,
                event_category VARCHAR(100) NULL,
                event_metadata JSON NULL,
                occurred_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (session_id) REFERENCES user_sessions(id) ON DELETE CASCADE,
                FOREIGN KEY (page_view_id) REFERENCES page_views(id) ON DELETE SET NULL,
                INDEX idx_session_id (session_id),
                INDEX idx_page_view_id (page_view_id),
                INDEX idx_event_type (event_type),
                INDEX idx_event_action (event_action),
                INDEX idx_occurred_at (occurred_at),
                INDEX idx_event_category (event_category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            
            $db->query($sql);
            CLI::write('✅ user_events table created successfully', 'green');

            CLI::write('🎉 All activity tracking tables created successfully!', 'green');

        } catch (\Exception $e) {
            CLI::write('❌ Error: ' . $e->getMessage(), 'red');
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }
}
