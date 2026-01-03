<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserDetailsTable extends Migration
{
    public function up()
    {
        // Create the user details table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'designation' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'occupation' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'county' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'website' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'facebook_link' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'twitter_link' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'linkedin_link' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'instagram_link' => [
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('user_details');
    }

    public function down()
    {
        // Delete the user details table
        $this->forge->dropTable('user_details');
    }
}
