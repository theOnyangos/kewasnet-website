<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnquiriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'replied_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'type_of_enquiry' => [
                'type' => 'ENUM',
                'constraint' => ['general', 'complaint', 'feedback'],
                'default' => 'general',
            ],
            'response_type' => [
                'type' => 'ENUM',
                'constraint' => ['email', 'sms', 'call'],
                'default' => 'email',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'replied'],
                'default' => 'pending',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('enquiries');
    }

    public function down()
    {
        $this->forge->dropTable('enquiries');
    }
}
