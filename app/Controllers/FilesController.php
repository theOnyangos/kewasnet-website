<?php

namespace App\Controllers;

use CodeIgniter\Files\File;
use App\Models\FileAttachment;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class FilesController extends BaseController
{
    protected $fileAttachmentModel;

    public function __construct()
    {
        $this->fileAttachmentModel = new FileAttachment();
    }

    public function downloadAttachment()
    {
        // Get the entire URI path after the route
        $uri = service('uri');
        $path = $uri->getSegment(4); // This gets the segment after 'download-attachment/'

        // For longer paths, you need to combine all remaining segments
        $segmentCount = $uri->getTotalSegments();
        $pathParts = [];
        
        for ($i = 4; $i <= $segmentCount; $i++) {
            $pathParts[] = $uri->getSegment($i);
        }
        
        $filePath = implode('/', $pathParts);
        
        log_message('debug', 'Downloading file: ' . $filePath);
        
        $fullPath = WRITEPATH . 'uploads/' . $filePath;
        log_message('debug', 'Full file path: ' . $fullPath);

        if (!file_exists($fullPath)) {
            log_message('error', 'File not found: ' . $fullPath);
            throw new PageNotFoundException('File not found');
        }

        // Find attachment record to get original name
        $attachment = $this->fileAttachmentModel->where('file_path', $filePath)->first();
        $originalName = $attachment ? $attachment->original_name : basename($filePath);

        // Increment download count
        if ($attachment) {
            $this->fileAttachmentModel->incrementDownloadCount($attachment->id);
        }

        return $this->response->download($fullPath, null)->setFileName($originalName);
    }

    public function viewAttachment()
    {
        // Get the entire URI path after the route
        $uri = service('uri');
        $segmentCount = $uri->getTotalSegments();
        $pathParts = [];
        
        // Start from segment 4 to get: replies/2025-08-31/1756662713_609d647453953d85c09f.pdf
        for ($i = 4; $i <= $segmentCount; $i++) {
            $pathParts[] = $uri->getSegment($i);
        }
        
        $filePath = implode('/', $pathParts);
        
        log_message('debug', 'Viewing file: ' . $filePath);
        
        $fullPath = WRITEPATH . 'uploads/' . $filePath;
        log_message('debug', 'Full file path: ' . $fullPath);

        if (!file_exists($fullPath)) {
            log_message('error', 'File not found: ' . $fullPath);
            throw new PageNotFoundException('File not found');
        }

        $file = new File($fullPath);
        $mime = $file->getMimeType();

        return $this->response->setContentType($mime)
                            ->setBody(file_get_contents($fullPath));
    }
}
