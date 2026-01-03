<?php
// Bootstrap CodeIgniter
echo "Starting test...\n";

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);

$pathsPath = FCPATH . '../app/Config/Paths.php';
$paths = require realpath($pathsPath) ?: $pathsPath;

echo "Paths loaded...\n";

$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

echo "Bootstrap loaded...\n";

$app->initialize();

echo "App initialized...\n";

try {
    // Test database connection
    $db = \Config\Database::connect();
    
    echo "Testing database connection...\n";
    
    // Test simple query
    $result = $db->query("SELECT COUNT(*) as count FROM pillars")->getRow();
    echo "Pillars count: " . $result->count . "\n";
    
    // Test problematic query that was causing collation issues
    $query = $db->table('resources r')
        ->select('r.id, r.title, r.slug')
        ->join('document_types dt', 'dt.id = r.document_type_id', 'left')
        ->join('resource_categories rc', 'rc.id = r.category_id', 'left')
        ->limit(5);
    
    $resources = $query->get()->getResult();
    echo "Resources query successful. Found " . count($resources) . " resources.\n";
    
    // Test character sets
    $charset_result = $db->query("SHOW TABLE STATUS LIKE 'resources'")->getRow();
    echo "Resources table collation: " . $charset_result->Collation . "\n";
    
    $charset_result = $db->query("SHOW TABLE STATUS LIKE 'document_types'")->getRow();
    echo "Document types table collation: " . $charset_result->Collation . "\n";
    
    echo "All tests passed! Collation issues appear to be fixed.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
