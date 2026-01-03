<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobApplicantsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('job_applicants')) {
            $this->forge->dropTable('job_applicants', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36, // UUID length
                'primary' => true,
                'default' => 'UUID()'
            ],
            'opportunity_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'resume_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'cover_letter_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'linkedin_profile' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'portfolio_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'current_job_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'current_company' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'years_of_experience' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true
            ],
            'education_level' => [
                'type' => 'ENUM',
                'constraint' => ['high_school','associate','bachelor','master','phd','other'],
                'null' => true
            ],
            'skills' => [
                'type' => 'JSON',
                'null' => true
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending','reviewed','interviewed','rejected','hired'],
                'default' => 'pending'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'custom_fields' => [
                'type' => 'JSON',
                'null' => true
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        // Note: Foreign key to job_opportunities added after both tables exist
        $this->forge->addKey('email');
        $this->forge->addKey('status');
        $this->forge->createTable('job_applicants');

        // Create application status history table
        $this->createStatusHistoryTable();
        
        // Add foreign keys after tables are created
        // Note: This may fail if job_opportunities doesn't exist, but that's expected
        try {
            $this->db->query("ALTER TABLE `job_applicants` ADD CONSTRAINT `fk_job_applicants_opportunity` FOREIGN KEY (`opportunity_id`) REFERENCES `job_opportunities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        } catch (\Exception $e) {
            // Foreign key may already exist or table may not exist - ignore
        }
    }

    protected function createStatusHistoryTable()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('applicant_status_history')) {
            $this->forge->dropTable('applicant_status_history', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()'
            ],
            'applicant_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending','reviewed','interviewed','rejected','hired'],
                'null' => false
            ],
            'changed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        // Note: Foreign keys added after table creation
        $this->forge->createTable('applicant_status_history');
        
        // Add foreign keys after table is created
        try {
            $this->db->query("ALTER TABLE `applicant_status_history` ADD CONSTRAINT `fk_applicant_status_history_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `job_applicants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
        
        try {
            $this->db->query("ALTER TABLE `applicant_status_history` ADD CONSTRAINT `fk_applicant_status_history_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL");
        } catch (\Exception $e) {
            // Foreign key may already exist or users table may not exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('applicant_status_history', true);
        $this->forge->dropTable('job_applicants', true);
    }
}