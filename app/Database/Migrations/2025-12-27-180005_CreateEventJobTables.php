<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventJobTables extends Migration
{
    public function up()
    {
        // Events table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'admin_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['Webinar', 'Seminar', 'Conference', 'Workshop', 'Training', 'Meeting', 'Other'],
                'default' => 'Webinar',
            ],
            'capacity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'summary' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'is_paid' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'start_time' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'end_time' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'start_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'event_cover_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => true,
            ],
            'latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => true,
            ],
            'notification_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'archived', 'deleted'],
                'default' => 'draft',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'archived_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('events');

        // Event organizers table
        $this->forge->addField([
            'org_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'org_event_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'org_organizer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_company' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_role' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_website' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_fb_link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_insta_link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_twitter_link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_organizer_linkedin_link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('org_id', true);
        $this->forge->createTable('event_organizers');

        // Event registrations table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'attendee_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'event_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'job_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_paid' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'payment_reference_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'get_notified' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('event_registrations');

        // Event tickets table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'event_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'event_reg_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'ticket_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'ticket_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('event_tickets');

        // User events table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'session_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'event_type' => [
                'type' => 'ENUM',
                'constraint' => ['click', 'form_submit', 'download', 'search', 'registration', 'login', 'logout', 'contact', 'newsletter', 'custom'],
            ],
            'event_category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'event_action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'event_label' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'event_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'page_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'occurred_at' => [
                'type' => 'DATETIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('session_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('event_type');
        $this->forge->addKey('event_category');
        $this->forge->addKey('occurred_at');
        $this->forge->addForeignKey('session_id', 'user_sessions', 'id', 'CASCADE', 'CASCADE', 'fk_user_events_session');
        $this->forge->createTable('user_events');

        // Job opportunities table
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'opportunity_type' => [
                'type' => 'ENUM',
                'constraint' => ['full-time', 'part-time', 'contract', 'internship', 'freelance'],
                'default' => 'full-time',
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_remote' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'salary_min' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'salary_max' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'salary_currency' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => true,
            ],
            'application_deadline' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'closed'],
                'default' => 'draft',
            ],
            'company' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'benefits' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'scope' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'document_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'requirements' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'contract_duration' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'hours_per_week' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('status');
        $this->forge->addKey('opportunity_type');
        $this->forge->addKey('company');
        $this->forge->addKey('created_at');
        $this->forge->addKey('application_deadline');
        $this->forge->createTable('job_opportunities');

        // Job applicants table
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'opportunity_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'resume_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'cover_letter_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'linkedin_profile' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'portfolio_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'current_job_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'current_company' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'years_of_experience' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
            ],
            'education_level' => [
                'type' => 'ENUM',
                'constraint' => ['high_school', 'associate', 'bachelor', 'master', 'phd', 'other'],
                'null' => true,
            ],
            'skills' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'reviewed', 'interviewed', 'rejected', 'hired'],
                'default' => 'pending',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'custom_fields' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email');
        $this->forge->addKey('status');
        $this->forge->addKey('opportunity_id');
        $this->forge->addForeignKey('opportunity_id', 'job_opportunities', 'id', 'CASCADE', 'CASCADE', 'fk_job_applicants_opportunity');
        $this->forge->createTable('job_applicants');

        // Applicant status history table
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'applicant_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'reviewed', 'interviewed', 'rejected', 'hired'],
            ],
            'changed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('applicant_id');
        $this->forge->addForeignKey('applicant_id', 'job_applicants', 'id', 'CASCADE', 'CASCADE', 'fk_applicant_status_history_applicant');
        $this->forge->createTable('applicant_status_history');
    }

    public function down()
    {
        $this->forge->dropTable('applicant_status_history', true);
        $this->forge->dropTable('job_applicants', true);
        $this->forge->dropTable('job_opportunities', true);
        $this->forge->dropTable('user_events', true);
        $this->forge->dropTable('event_tickets', true);
        $this->forge->dropTable('event_registrations', true);
        $this->forge->dropTable('event_organizers', true);
        $this->forge->dropTable('events', true);
    }
}
