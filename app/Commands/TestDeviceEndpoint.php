<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\ActivityTrackingService;
use App\Models\UserSessionModel;

class TestDeviceEndpoint extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'tracking:test-device-endpoint';
    protected $description = 'Test the device data endpoint and display results';

    public function run(array $params)
    {
        CLI::write('Testing Device Data Endpoint...', 'green');
        CLI::newLine();
        
        try {
            $trackingService = new ActivityTrackingService();
            
            // Test with different date ranges
            $ranges = [
                'today' => [
                    date('Y-m-d 00:00:00'),
                    date('Y-m-d 23:59:59')
                ],
                'week' => [
                    date('Y-m-d 00:00:00', strtotime('-7 days')),
                    date('Y-m-d 23:59:59')
                ],
                'month' => [
                    date('Y-m-d 00:00:00', strtotime('-30 days')),
                    date('Y-m-d 23:59:59')
                ]
            ];
            
            foreach ($ranges as $rangeName => $dates) {
                CLI::write("Testing range: {$rangeName}", 'yellow');
                CLI::write("  Date range: {$dates[0]} to {$dates[1]}", 'cyan');
                
                // Get dashboard data
                $reflection = new \ReflectionClass($trackingService);
                $method = $reflection->getMethod('getDashboardData');
                $method->setAccessible(true);
                
                CLI::write("  Calling getDashboardData with: '{$dates[0]}' and '{$dates[1]}'", 'white');
                $data = $method->invoke($trackingService, $dates[0], $dates[1]);
                
                // Also test the device breakdown directly
                $sessionModel = new UserSessionModel();
                $directDeviceBreakdown = $sessionModel->getSessionsByDevice($dates[0], $dates[1]);
                CLI::write('  Direct getSessionsByDevice result:', 'white');
                if (empty($directDeviceBreakdown)) {
                    CLI::write('    Empty result', 'red');
                } else {
                    foreach ($directDeviceBreakdown as $device) {
                        $deviceType = is_object($device) ? ($device->device ?? 'Unknown') : ($device['device'] ?? 'Unknown');
                        $count = is_object($device) ? ($device->count ?? 0) : ($device['count'] ?? 0);
                        CLI::write("    - {$deviceType}: {$count} sessions", 'white');
                    }
                }
                
                // Display device stats
                if (isset($data['device_stats'])) {
                    $deviceStats = $data['device_stats'];
                    CLI::write('  Device Stats:', 'green');
                    CLI::write('    Labels: ' . json_encode($deviceStats['labels'] ?? []), 'white');
                    CLI::write('    Data: ' . json_encode($deviceStats['data'] ?? []), 'white');
                    
                    // Show breakdown
                    if (isset($deviceStats['labels']) && isset($deviceStats['data'])) {
                        for ($i = 0; $i < count($deviceStats['labels']); $i++) {
                            $label = $deviceStats['labels'][$i] ?? 'Unknown';
                            $count = $deviceStats['data'][$i] ?? 0;
                            CLI::write("    - {$label}: {$count} sessions", 'cyan');
                        }
                    }
                } else {
                    CLI::write('  ❌ Device stats not found in response', 'red');
                }
                
                // Also show raw device breakdown
                if (isset($data['device_breakdown'])) {
                    CLI::write('  Raw Device Breakdown:', 'green');
                    foreach ($data['device_breakdown'] as $device) {
                        $deviceType = is_object($device) ? ($device->device ?? 'Unknown') : ($device['device'] ?? 'Unknown');
                        $count = is_object($device) ? ($device->count ?? 0) : ($device['count'] ?? 0);
                        CLI::write("    - {$deviceType}: {$count} sessions", 'cyan');
                    }
                }
                
                CLI::newLine();
            }
            
            // Test direct model query (no date filter)
            CLI::write('Testing Direct Model Query (No Date Filter):', 'yellow');
            $sessionModel = new UserSessionModel();
            $deviceBreakdown = $sessionModel->getSessionsByDevice();
            
            CLI::write('  Direct Query Results:', 'green');
            if (empty($deviceBreakdown)) {
                CLI::write('    No device data found', 'red');
            } else {
                foreach ($deviceBreakdown as $device) {
                    $deviceType = is_object($device) ? ($device->device ?? 'Unknown') : ($device['device'] ?? 'Unknown');
                    $count = is_object($device) ? ($device->count ?? 0) : ($device['count'] ?? 0);
                    CLI::write("    - {$deviceType}: {$count} sessions", 'cyan');
                }
            }
            
            // Test with month range explicitly
            CLI::newLine();
            CLI::write('Testing with explicit month range:', 'yellow');
            $monthStart = date('Y-m-d 00:00:00', strtotime('-30 days'));
            $monthEnd = date('Y-m-d 23:59:59');
            $deviceBreakdownWithDate = $sessionModel->getSessionsByDevice($monthStart, $monthEnd);
            
            CLI::write("  Date range: {$monthStart} to {$monthEnd}", 'cyan');
            CLI::write('  Results with date filter:', 'green');
            if (empty($deviceBreakdownWithDate)) {
                CLI::write('    No device data found in date range', 'red');
            } else {
                foreach ($deviceBreakdownWithDate as $device) {
                    $deviceType = is_object($device) ? ($device->device ?? 'Unknown') : ($device['device'] ?? 'Unknown');
                    $count = is_object($device) ? ($device->count ?? 0) : ($device['count'] ?? 0);
                    CLI::write("    - {$deviceType}: {$count} sessions", 'cyan');
                }
            }
            
            // Check actual session dates
            CLI::newLine();
            CLI::write('Checking actual session dates in database:', 'yellow');
            $db = \Config\Database::connect();
            $sessions = $db->table('user_sessions')
                ->select('device, session_start, COUNT(*) as count')
                ->groupBy('device, DATE(session_start)')
                ->orderBy('session_start', 'DESC')
                ->limit(10)
                ->get()
                ->getResult();
            
            foreach ($sessions as $session) {
                $deviceType = $session->device ?? 'Unknown';
                $sessionStart = $session->session_start ?? 'Unknown';
                $count = $session->count ?? 0;
                CLI::write("    - {$deviceType}: {$count} sessions on {$sessionStart}", 'cyan');
            }
            
            CLI::newLine();
            CLI::write('✅ Device endpoint test completed!', 'green');
            
        } catch (\Exception $e) {
            CLI::write('❌ Error testing device endpoint: ' . $e->getMessage(), 'red');
            CLI::write('Stack trace: ' . $e->getTraceAsString(), 'red');
        }
    }
}
