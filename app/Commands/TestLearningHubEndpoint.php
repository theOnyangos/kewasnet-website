<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\CourseModel;

class TestLearningHubEndpoint extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:learning-hub';
    protected $description = 'Test Learning Hub endpoint and course loading';

    public function run(array $params)
    {
        $courseModel = new CourseModel();
        
        CLI::write("=== Testing Learning Hub Endpoint ===\n", 'yellow');
        
        // Test 1: Check total courses
        $allCourses = $courseModel->withDeleted()->findAll();
        CLI::write("Total courses (including deleted): " . count($allCourses), 'white');
        
        // Test 2: Check courses with status = 1 (without soft delete filter)
        $db = \Config\Database::connect();
        $activeCoursesRaw = $db->table('courses')
            ->where('status', 1)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();
        CLI::write("Courses with status=1 and not deleted (raw query): " . count($activeCoursesRaw), 'white');
        
        // Test 3: Check what CourseModel returns (with soft deletes)
        $activeCourses = $courseModel->where('status', 1)->findAll();
        CLI::write("Courses returned by CourseModel (with soft delete): " . count($activeCourses), 'white');
        
        // Test 4: Simulate controller query
        $builder = $courseModel->where('status', 1);
        $courses = $builder->orderBy('created_at', 'DESC')->findAll();
        CLI::write("Courses returned by controller query: " . count($courses), 'white');
        
        if (!empty($courses)) {
            CLI::write("\nSample courses:", 'green');
            foreach (array_slice($courses, 0, 5) as $course) {
                CLI::write(sprintf(
                    "- ID: %s, Title: %s, Status: %s, Price: %s, Is Paid: %s",
                    $course['id'],
                    substr($course['title'] ?? 'N/A', 0, 50),
                    $course['status'] ?? 'N/A',
                    $course['price'] ?? '0',
                    $course['is_paid'] ?? '0'
                ), 'cyan');
            }
        } else {
            CLI::write("\nNo courses found! Debugging...", 'red');
            
            // Check what's in the database
            $all = $db->table('courses')->get()->getResultArray();
            CLI::write("All courses in DB: " . count($all), 'yellow');
            
            if (!empty($all)) {
                CLI::write("\nFirst course details:", 'yellow');
                $first = $all[0];
                CLI::write("ID: " . ($first['id'] ?? 'N/A'), 'white');
                CLI::write("Title: " . ($first['title'] ?? 'N/A'), 'white');
                CLI::write("Status: " . ($first['status'] ?? 'N/A'), 'white');
                CLI::write("Deleted_at: " . ($first['deleted_at'] ?? 'null'), 'white');
            }
        }
        
        CLI::write("\n=== Test Complete ===", 'yellow');
    }
}

