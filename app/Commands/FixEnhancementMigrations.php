<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixEnhancementMigrations extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'migrate:fix-enhance';
    protected $description = 'Fix enhancement migration records with correct class names';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        $migrations = [
            ['2025-12-24-111848', 'App\\Database\\Migrations\\EnhanceCourseLecturesTable'],
            ['2025-12-24-111852', 'App\\Database\\Migrations\\EnhanceCourseSectionsTable'],
            ['2025-12-24-111855', 'App\\Database\\Migrations\\EnhanceCoursesTable'],
            ['2025-12-27-000000', 'App\\Database\\Migrations\\ConvertCoursesToUuid'],
            ['2025-12-27-062609', 'App\\Database\\Migrations\\AddCourseIdToOrdersTable'],
        ];

        foreach ($migrations as [$version, $className]) {
            // Delete existing incorrect record
            $db->table('migrations')->where('version', $version)->delete();
            CLI::write("Deleted old record for $version", 'yellow');
            
            // Insert correct record
            $db->table('migrations')->insert([
                'version' => $version,
                'class' => $className,
                'group' => 'default',
                'namespace' => 'App',
                'time' => time(),
                'batch' => 77
            ]);
            CLI::write("Inserted correct record for $version", 'green');
        }

        CLI::newLine();
        CLI::write("Done fixing enhancement migrations!", 'green');
    }
}
