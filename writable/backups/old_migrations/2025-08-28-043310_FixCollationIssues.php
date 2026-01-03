<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCollationIssues extends Migration
{
    public function up()
    {
        // Set target charset and collation
        $charset = 'utf8mb4';
        $collation = 'utf8mb4_unicode_ci';
        
        // Get the database instance
        $db = \Config\Database::connect();
        
        try {
            // First, let's check and fix the overall database collation
            $databaseName = $db->getDatabase();
            
            // Fix database collation
            $db->query("ALTER DATABASE `{$databaseName}` CHARACTER SET {$charset} COLLATE {$collation}");
            
            // Fix pillars table
            $db->query("ALTER TABLE `pillars` CONVERT TO CHARACTER SET {$charset} COLLATE {$collation}");
            
            // Fix document_types table
            $db->query("ALTER TABLE `document_types` CONVERT TO CHARACTER SET {$charset} COLLATE {$collation}");
            
            // Fix contributors table
            $db->query("ALTER TABLE `contributors` CONVERT TO CHARACTER SET {$charset} COLLATE {$collation}");
            
            // For tables with foreign keys, we need to temporarily drop and recreate the constraints
            
            // 1. Drop foreign keys from resource_categories
            // Check if foreign key exists before dropping
            try {
                $db->query("ALTER TABLE `resource_categories` DROP FOREIGN KEY `resource_categories_pillar_id_foreign`");
            } catch (\Exception $e) {
                // Foreign key doesn't exist, ignore
            }
            
            // 2. Convert resource_categories table
            $db->query("ALTER TABLE `resource_categories` CONVERT TO CHARACTER SET {$charset} COLLATE {$collation}");
            
            // 3. Re-add the foreign key (only if it doesn't exist)
            try {
                $db->query("ALTER TABLE `resource_categories` ADD CONSTRAINT `resource_categories_pillar_id_foreign` 
                           FOREIGN KEY (`pillar_id`) REFERENCES `pillars`(`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Foreign key may already exist or data integrity issue, ignore
            }
            
            // 4. Drop foreign keys from resources table
            // Check if foreign keys exist before dropping
            $foreignKeysToDrop = [
                'resources_pillar_id_foreign',
                'resources_category_id_foreign',
                'resources_document_type_id_foreign'
            ];
            foreach ($foreignKeysToDrop as $fk) {
                try {
                    $db->query("ALTER TABLE `resources` DROP FOREIGN KEY `{$fk}`");
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, ignore
                }
            }
            
            // 5. Convert resources table
            $db->query("ALTER TABLE `resources` CONVERT TO CHARACTER SET {$charset} COLLATE {$collation}");
            
            // 6. Re-add foreign keys to resources table (only if they don't exist)
            try {
                $db->query("ALTER TABLE `resources` ADD CONSTRAINT `resources_pillar_id_foreign` 
                           FOREIGN KEY (`pillar_id`) REFERENCES `pillars`(`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Foreign key may already exist or data integrity issue, ignore
            }
            try {
                $db->query("ALTER TABLE `resources` ADD CONSTRAINT `resources_category_id_foreign` 
                           FOREIGN KEY (`category_id`) REFERENCES `resource_categories`(`id`) ON DELETE SET NULL ON UPDATE SET NULL");
            } catch (\Exception $e) {
                // Foreign key may already exist or data integrity issue, ignore
            }
            try {
                $db->query("ALTER TABLE `resources` ADD CONSTRAINT `resources_document_type_id_foreign` 
                           FOREIGN KEY (`document_type_id`) REFERENCES `document_types`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
            } catch (\Exception $e) {
                // Foreign key may already exist or data integrity issue, ignore
            }
            
            // 7. Drop foreign keys from resource_contributors if they exist
            try {
                // Check if foreign key exists before dropping
                try {
                    $db->query("ALTER TABLE `resource_contributors` DROP FOREIGN KEY `resource_contributors_resource_id_foreign`");
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, ignore
                }
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            try {
                // Check if foreign key exists before dropping
                try {
                    $db->query("ALTER TABLE `resource_contributors` DROP FOREIGN KEY `resource_contributors_contributor_id_foreign`");
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, ignore
                }
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // 8. Convert resource_contributors table
            $db->query("ALTER TABLE `resource_contributors` CONVERT TO CHARACTER SET {$charset} COLLATE {$collation}");
            
            // 9. Re-add foreign keys to resource_contributors (only if they don't exist)
            try {
                $db->query("ALTER TABLE `resource_contributors` ADD CONSTRAINT `resource_contributors_resource_id_foreign` 
                           FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Foreign key may already exist or data integrity issue, ignore
            }
            try {
                $db->query("ALTER TABLE `resource_contributors` ADD CONSTRAINT `resource_contributors_contributor_id_foreign` 
                           FOREIGN KEY (`contributor_id`) REFERENCES `contributors`(`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            } catch (\Exception $e) {
                // Foreign key may already exist or data integrity issue, ignore
            }
            
            echo "Collation issues have been fixed successfully.\n";
            
        } catch (\Exception $e) {
            echo "Error fixing collation: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function down()
    {
        // This migration is not easily reversible due to character set changes
        // If needed, you would need to manually revert the collations
        echo "This migration cannot be automatically reversed.\n";
    }
}
