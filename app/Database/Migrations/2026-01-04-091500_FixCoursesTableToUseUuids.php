<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCoursesTableToUseUuids extends Migration
{
    public function up()
    {
        // Modify user_id to VARCHAR(36) for UUID
        $fields = [
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('courses', $fields);

        // Modify category_id to VARCHAR(36) for UUID
        $fields = [
            'category_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('courses', $fields);

        // Modify sub_category_id to VARCHAR(36) for UUID
        $fields = [
            'sub_category_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('courses', $fields);
    }

    public function down()
    {
        // Revert user_id to INT
        $fields = [
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
        ];
        $this->forge->modifyColumn('courses', $fields);

        // Revert category_id to INT
        $fields = [
            'category_id' => [
                'type' => 'INT',
                'constraint' => 5,
            ],
        ];
        $this->forge->modifyColumn('courses', $fields);

        // Revert sub_category_id to INT
        $fields = [
            'sub_category_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('courses', $fields);
    }
}
