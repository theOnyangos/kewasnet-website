<?php

require_once 'vendor/autoload.php';

$config = new \Config\Database();
$config->default['database'] = 'kewasnet_test';
$db = \Config\Database::connect('default', false);

echo "System Users Table Structure:\n";
echo str_repeat('=', 80) . "\n";

$fields = $db->getFieldData('system_users');
foreach ($fields as $field) {
    $pk = $field->primary_key ? ' [PRIMARY KEY]' : '';
    $null = $field->nullable ? 'NULL' : 'NOT NULL';
    $default = $field->default !== null ? " DEFAULT: " . var_export($field->default, true) : '';
    
    echo sprintf("%-30s %-15s %-10s%s%s\n", 
        $field->name, 
        $field->type, 
        $null,
        $default,
        $pk
    );
}

echo "\n\nChecking for empty string in primary key:\n";
$result = $db->query("SELECT id FROM system_users WHERE id = ''")->getResultArray();
echo "Records with empty id: " . count($result) . "\n";

if (count($result) > 0) {
    echo "Found records with empty id!\n";
    print_r($result);
}
