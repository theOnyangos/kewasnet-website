<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEmailSettingsTable extends Migration
{
    public function up()
    {
        // Check which columns already exist and only add missing ones
        $existingColumns = [];
        if ($this->db->tableExists('email_settings')) {
            $query = $this->db->query("SHOW COLUMNS FROM `email_settings`");
            foreach ($query->getResultArray() as $row) {
                $existingColumns[] = $row['Field'];
            }
        }
        
        // Add missing fields to email_settings table
        $fields = [
            'smtp_timeout' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 30,
                'after' => 'port'
            ],
            'reply_to_email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'from_name'
            ],
            'bcc_email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'reply_to_email'
            ],
            'email_header' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'bcc_email'
            ],
            'email_footer' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'email_header'
            ],
            'email_enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'email_footer'
            ],
            'debug_mode' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'email_enabled'
            ],
            'html_emails' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'debug_mode'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'html_emails'
            ]
        ];

        // Only add columns that don't already exist
        $fieldsToAdd = [];
        foreach ($fields as $fieldName => $fieldDef) {
            if (!in_array($fieldName, $existingColumns)) {
                $fieldsToAdd[$fieldName] = $fieldDef;
            }
        }
        
        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('email_settings', $fieldsToAdd);
        }

        // Modify existing fields
        $this->forge->modifyColumn('email_settings', [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false
            ],
            'host' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'encryption' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true
            ],
            'port' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => true
            ],
            'from_address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'from_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        // Remove added fields
        $this->forge->dropColumn('email_settings', [
            'smtp_timeout',
            'reply_to_email', 
            'bcc_email',
            'email_header',
            'email_footer', 
            'email_enabled',
            'debug_mode',
            'html_emails',
            'created_at'
        ]);

        // Revert field modifications (optional since this is destructive)
        $this->forge->modifyColumn('email_settings', [
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ]
        ]);
    }
}
