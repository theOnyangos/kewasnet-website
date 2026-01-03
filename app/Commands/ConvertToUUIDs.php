<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Ramsey\Uuid\Uuid;

class ConvertToUUIDs extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:convert-uuids';
    protected $description = 'Convert all tables to use UUIDs for primary keys';

    public function run(array $params)
    {
        CLI::write('===========================================', 'yellow');
        CLI::write('  UUID Conversion Analysis', 'yellow');
        CLI::write('===========================================', 'yellow');
        CLI::newLine();

        // Get database configuration
        $config = config('Database');
        $db = \Config\Database::connect('default', false);

        CLI::write("Database: {$config->default['database']}", 'green');
        CLI::newLine();

        // Get all tables
        $tables = $db->listTables();
        $uuidTables = [];
        $autoIncrementTables = [];

        foreach ($tables as $table) {
            if ($table === 'migrations') continue;

            $fields = $db->getFieldData($table);
            foreach ($fields as $field) {
                if ($field->primary_key) {
                    if (stripos($field->type, 'char') !== false || stripos($field->type, 'varchar') !== false) {
                        $uuidTables[] = $table;
                    } elseif (stripos($field->type, 'int') !== false || stripos($field->type, 'bigint') !== false) {
                        $autoIncrementTables[] = $table;
                    }
                    break;
                }
            }
        }

        CLI::write("Tables already using UUIDs: " . count($uuidTables), 'green');
        foreach ($uuidTables as $table) {
            CLI::write("  ✓ {$table}", 'green');
        }

        CLI::newLine();
        CLI::write("Tables using auto-increment: " . count($autoIncrementTables), 'yellow');
        foreach ($autoIncrementTables as $table) {
            CLI::write("  • {$table}", 'yellow');
        }

        CLI::newLine();
        CLI::write('===========================================');
        CLI::write("Total: " . count($tables) . " tables");
        CLI::write('===========================================');
    }
}
