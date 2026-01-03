<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddCourseIdToOrders extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:add-course-id';
    protected $description = 'Adds course_id column to orders table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            // Check if column exists
            $query = $db->query("SHOW COLUMNS FROM orders LIKE 'course_id'");
            $result = $query->getResult();

            if (empty($result)) {
                CLI::write('Adding course_id column to orders table...', 'yellow');
                $db->query("ALTER TABLE orders ADD COLUMN course_id INT(11) UNSIGNED NULL AFTER user_id");
                CLI::write('Column added successfully!', 'green');
            } else {
                CLI::write('Column course_id already exists in orders table.', 'blue');
            }

            // Verify
            $query = $db->query("DESCRIBE orders");
            CLI::write("\nCurrent orders table structure:", 'cyan');
            foreach ($query->getResult() as $column) {
                CLI::write("  - {$column->Field} ({$column->Type})");
            }

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }
}
