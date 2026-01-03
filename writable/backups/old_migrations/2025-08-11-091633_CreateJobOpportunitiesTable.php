<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobOpportunitiesTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('job_opportunities')) {
            $this->forge->dropTable('job_opportunities', true);
        }
        
        // Opportunities Table
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()'
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true
            ],
            'description' => [
                'type' => 'TEXT'
            ],
            'opportunity_type' => [
                'type' => 'ENUM',
                'constraint' => ['full-time', 'part-time', 'contract', 'internship', 'freelance'],
                'default' => 'full-time'
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'is_remote' => [
                'type' => 'BOOLEAN',
                'default' => false
            ],
            'salary_min' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true
            ],
            'salary_max' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true
            ],
            'salary_currency' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => true
            ],
            'application_deadline' => [
                'type' => 'DATE',
                'null' => true
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'closed'],
                'default' => 'draft'
            ],
            'company' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'benefits' => [
                'type' => 'JSON',
                'null' => true
            ],
            'scope' => [
                'type' => 'JSON',
                'null' => true
            ],
            'document_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'requirements' => [
                'type' => 'JSON',
                'null' => true
            ],
            'contract_duration' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'hours_per_week' => [
                'type' => 'INT',
                'constraint' => 2,
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

        // Primary Key
        $this->forge->addPrimaryKey('id');
        
        // Create the table
        $this->forge->createTable('job_opportunities');
        
        // Add indexes after table creation
        $this->db->query('CREATE INDEX idx_job_opportunities_status ON job_opportunities(status)');
        $this->db->query('CREATE INDEX idx_job_opportunities_opportunity_type ON job_opportunities(opportunity_type)');
        $this->db->query('CREATE INDEX idx_job_opportunities_company ON job_opportunities(company)');
        $this->db->query('CREATE INDEX idx_job_opportunities_created_at ON job_opportunities(created_at)');
        $this->db->query('CREATE INDEX idx_job_opportunities_application_deadline ON job_opportunities(application_deadline)');
        
        // Full-text search index
        $this->db->query('ALTER TABLE job_opportunities ADD FULLTEXT INDEX ft_job_opportunities_search (title, description, company, location)');
    }

    public function down()
    {
        // Drop the job_opportunities table
        $this->forge->dropTable('job_opportunities');
    }
}