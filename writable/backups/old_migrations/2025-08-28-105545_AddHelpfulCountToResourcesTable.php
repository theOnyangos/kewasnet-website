<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHelpfulCountToResourcesTable extends Migration
{
    public function up()
    {
        // Check if columns already exist
        $existingColumns = [];
        if ($this->db->tableExists('resources')) {
            $query = $this->db->query("SHOW COLUMNS FROM `resources`");
            foreach ($query->getResultArray() as $row) {
                $existingColumns[] = $row['Field'];
            }
        }
        
        $fieldsToAdd = [];
        $fields = [
            'helpful_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'unsigned'   => true,
                'after'      => 'download_count',
            ],
            'is_private' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '1=private (employees only), 0=public',
                'after'      => 'helpful_count',
            ],
        ];
        
        // Only add columns that don't already exist
        foreach ($fields as $fieldName => $fieldDef) {
            if (!in_array($fieldName, $existingColumns)) {
                $fieldsToAdd[$fieldName] = $fieldDef;
            }
        }
        
        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('resources', $fieldsToAdd);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('resources', ['helpful_count', 'is_private']);
    }
}
