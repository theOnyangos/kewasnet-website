<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserEventModel;
use App\Models\UserSessionModel;
use App\Services\ActivityTrackingService;

class TestEventTracking extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:event-tracking';
    protected $description = 'Test event tracking persistence to database';

    public function run(array $params)
    {
        CLI::write('Testing Event Tracking Persistence', 'yellow');
        CLI::newLine();

        $db = \Config\Database::connect();
        
        // Check if table exists
        CLI::write('1. Checking user_events table structure...', 'cyan');
        try {
            $tableInfo = $db->query("DESCRIBE user_events")->getResultArray();
            CLI::write('   Table exists with columns:', 'green');
            foreach ($tableInfo as $column) {
                CLI::write("   - {$column['Field']} ({$column['Type']}) " . ($column['Null'] === 'NO' ? '[REQUIRED]' : '[NULLABLE]'), 'light_gray');
            }
        } catch (\Exception $e) {
            CLI::write('   ❌ Table does not exist: ' . $e->getMessage(), 'red');
            return EXIT_ERROR;
        }
        CLI::newLine();

        // Check current event count
        CLI::write('2. Checking current event count...', 'cyan');
        try {
            $result = $db->query("SELECT COUNT(*) as total FROM user_events")->getRow();
            $currentCount = $result->total ?? 0;
            CLI::write("   Current events in database: {$currentCount}", 'green');
        } catch (\Exception $e) {
            CLI::write('   ❌ Error: ' . $e->getMessage(), 'red');
            return EXIT_ERROR;
        }
        CLI::newLine();

        // Check for a valid session
        CLI::write('3. Checking for valid tracking session...', 'cyan');
        try {
            $sessionResult = $db->query("SELECT id FROM user_sessions ORDER BY created_at DESC LIMIT 1")->getRow();
            if (!$sessionResult) {
                CLI::write('   ⚠️  No sessions found. Creating a test session...', 'yellow');
                
                // Create a test session
                $sessionModel = new UserSessionModel();
                $testSessionId = \Ramsey\Uuid\Uuid::uuid4()->toString();
                $sessionData = [
                    'session_id' => 'test_' . time(),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Test Agent',
                    'browser' => 'Test',
                    'device' => 'Desktop',
                    'os' => 'Test OS',
                    'analytics_consent' => true,
                    'marketing_consent' => false,
                    'session_start' => date('Y-m-d H:i:s'),
                    'page_views' => 0,
                    'is_bounce' => true
                ];
                $sessionModel->insert($sessionData);
                $insertedSession = $sessionModel->where('session_id', $sessionData['session_id'])->first();
                $testSessionId = $insertedSession->id ?? null;
                
                if ($testSessionId) {
                    CLI::write("   ✅ Test session created: {$testSessionId}", 'green');
                } else {
                    CLI::write('   ❌ Failed to create test session', 'red');
                    return EXIT_ERROR;
                }
            } else {
                $testSessionId = $sessionResult->id;
                CLI::write("   ✅ Found session: {$testSessionId}", 'green');
            }
        } catch (\Exception $e) {
            CLI::write('   ❌ Error: ' . $e->getMessage(), 'red');
            CLI::write('   Stack trace: ' . $e->getTraceAsString(), 'light_gray');
            return EXIT_ERROR;
        }
        CLI::newLine();

        // Test direct model insertion
        CLI::write('4. Testing direct model insertion...', 'cyan');
        try {
            $eventModel = new UserEventModel();
            
            // Check what fields are required
            $requiredFields = [];
            foreach ($tableInfo as $column) {
                if ($column['Null'] === 'NO' && $column['Field'] !== 'id' && $column['Field'] !== 'created_at') {
                    $requiredFields[] = $column['Field'];
                }
            }
            CLI::write('   Required fields: ' . implode(', ', $requiredFields), 'light_gray');
            
            // Prepare test event data
            $testEventData = [
                'session_id' => $testSessionId,
                'event_type' => 'custom',
                'event_action' => 'test',
                'event_label' => 'Test Event',
                'event_category' => 'Testing',
                'occurred_at' => date('Y-m-d H:i:s')
            ];
            
            // Add page_url if required
            if (in_array('page_url', $requiredFields)) {
                $testEventData['page_url'] = '/test-page';
                CLI::write('   ⚠️  Adding page_url field (required by table)', 'yellow');
            }
            
            // Add user_id if it exists in table
            $hasUserId = false;
            foreach ($tableInfo as $column) {
                if ($column['Field'] === 'user_id') {
                    $hasUserId = true;
                    break;
                }
            }
            
            CLI::write('   Testing via trackEvent() method...', 'light_gray');
            
            try {
                $result = $eventModel->trackEvent(
                    $testSessionId,
                    'custom',
                    'test',
                    'Test Event',
                    null,
                    'Testing',
                    null,
                    '/test-page'
                );
            } catch (\Exception $e) {
                CLI::write('   ❌ trackEvent() failed: ' . $e->getMessage(), 'red');
                $lastQuery = $db->getLastQuery();
                if ($lastQuery) {
                    CLI::write('   Last query: ' . $lastQuery->getQuery(), 'light_gray');
                }
                throw $e;
            }
            
            if ($result) {
                CLI::write('   ✅ Event inserted successfully via trackEvent()!', 'green');
                
                // Verify it was saved
                $newCount = $db->query("SELECT COUNT(*) as total FROM user_events")->getRow()->total;
                CLI::write("   New event count: {$newCount}", 'green');
                
                // Get the inserted event
                $insertedEvent = $db->query("SELECT * FROM user_events ORDER BY created_at DESC LIMIT 1")->getRowArray();
                CLI::write('   Inserted event details:', 'light_gray');
                foreach ($insertedEvent as $key => $value) {
                    CLI::write("     {$key}: " . ($value ?? 'NULL'), 'light_gray');
                }
            } else {
                CLI::write('   ❌ Event insertion failed', 'red');
                return EXIT_ERROR;
            }
        } catch (\Exception $e) {
            CLI::write('   ❌ Error: ' . $e->getMessage(), 'red');
            CLI::write('   Stack trace: ' . $e->getTraceAsString(), 'light_gray');
            return EXIT_ERROR;
        }
        CLI::newLine();

        // Test via service
        CLI::write('5. Testing via ActivityTrackingService...', 'cyan');
        try {
            // This would require a proper request context, so we'll skip for now
            CLI::write('   ⚠️  Service test requires HTTP context - skipping', 'yellow');
        } catch (\Exception $e) {
            CLI::write('   ❌ Error: ' . $e->getMessage(), 'red');
        }
        CLI::newLine();

        CLI::write('✅ Event tracking test completed!', 'green');
        return EXIT_SUCCESS;
    }
}
