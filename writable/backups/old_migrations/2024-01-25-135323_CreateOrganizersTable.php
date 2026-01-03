<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'org_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'org_event_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
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
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);

        $this->forge->addKey('org_id', true);
        $this->forge->createTable('event_organizers');
    }

    public function down()
    {
        $this->forge->dropTable('event_organizers');
    }
}
