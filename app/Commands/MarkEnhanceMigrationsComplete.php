<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MarkEnhanceMigrationsComplete extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'migrate:mark-enhance';
    protected $description = 'Mark enhancement migrations as complete';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        $migrations = [
            '2025-12-24-111848', // EnhanceCourseLecturesTable
            '2025-12-24-111852', // EnhanceCourseSectionsTable
            '2025-12-24-111855', // EnhanceCoursesTable
            '2025-12-27-000000', // ConvertCoursesToUuid
            '2025-12-27-062609', // AddCourseIdToOrdersTable
        ];

        foreach ($migrations as $version) {
            $existing = $db->table('migrations')->where('version', $version)->get()->getFirstRow();
            if (!$existing) {
                $db->table('migrations')->insert([
                    'version' => $version,
                    'class' => 'App',
                    'group' => 'default',
                    'namespace' => 'App\\Database\\Migrations',
                    'time' => time(),
                    'batch' => 77
                ]);
                CLI::write("Marked $version as migrated", 'green');
            } else {
                CLI::write("$version already marked", 'blue');
            }
        }

        CLI::newLine();
        CLI::write("Done marking enhancement migrations!", 'green');
    }
}
