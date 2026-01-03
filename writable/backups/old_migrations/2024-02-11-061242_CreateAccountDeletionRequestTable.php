<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountDeletionRequestTable extends Migration
{
    public function up()
    {
        // Create the account deletion request table
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
            ],
            'reason' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
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

        $this->forge->addKey('id');
        $this->forge->createTable('account_deletion_requests');
    }

    public function down()
    {
        // Drop table
        $this->forge->dropTable('account_deletion_requests');
    }
}
