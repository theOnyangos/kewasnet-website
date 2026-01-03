<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseSubCategoryTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('course_sub_categories')) {
            $this->forge->dropTable('course_sub_categories', true);
        }
        
        // Create the sub course categories table
        $this->forge->addField([
            'csc_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'csc_category_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'csc_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'csc_slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => false,
            ],
            'csc_description' => [
                'type' => 'TEXT',
            ],
            'csc_status' => [
                'type' => 'INT',
                'constraint' => 1,
                'default' => 1,
            ],
            'csc_created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'csc_updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'csc_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('csc_id');
       // $this->forge->addForeignKey('csc_category_id', 'categories', 'cc_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('course_sub_categories');
    }

    public function down()
    {
        // Drop the sub course categories table
        $this->forge->dropTable('course_sub_categories');
    }
}
