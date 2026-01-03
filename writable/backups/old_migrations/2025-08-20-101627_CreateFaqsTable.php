<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFaqsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('faqs')) {
            $this->forge->dropTable('faqs', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'primary'    => true,
                'default'    => 'UUID()',
                'null'       => false,
            ],
            'question' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
            'answer' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('category');
        $this->forge->createTable('faqs');
    }

    public function down()
    {
        $this->forge->dropTable('faqs');
    }
}