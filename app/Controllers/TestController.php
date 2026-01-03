<?php

namespace App\Controllers;

use App\Services\PillarArticlesService;

class TestController extends BaseController
{
    public function testAttachments()
    {
        $resourceId = 'ef445714-e0f0-4fd4-9276-f8de5a51d34c';
        
        $pillarService = new PillarArticlesService();
        
        echo "<h1>Testing Resource Attachment Retrieval</h1>";
        echo "<p>Resource ID: $resourceId</p>";
        
        try {
            // Direct test of attachment retrieval
            $fileAttachmentModel = new \App\Models\FileAttachment();
            $attachmentData = $fileAttachmentModel->getAttachmentsForResource($resourceId);
            
            echo "<h2>Direct FileAttachment Model Result:</h2>";
            echo "<pre>";
            var_dump($attachmentData);
            echo "</pre>";
            
            // Test with getResourceBySlug
            $resource = $pillarService->getResourceBySlug('test-title-pillar-resource');
            
            echo "<h2>getResourceBySlug Result:</h2>";
            if ($resource) {
                echo "<pre>";
                var_dump([
                    'id' => $resource['id'],
                    'title' => $resource['title'],
                    'slug' => $resource['slug'],
                    'attachments_count' => count($resource['attachments'] ?? []),
                    'total_downloads' => $resource['total_downloads'] ?? 0
                ]);
                echo "</pre>";
            } else {
                echo "<p>Resource not found</p>";
            }
            
        } catch (\Exception $e) {
            echo "<h2>Error:</h2>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
}
