<?php

/**
 * Format file size into human-readable string
 *
 * @param int $bytes
 * @param int $decimals
 * @return string
 */
if (!function_exists('format_file_size')) {
    function format_file_size($bytes, $decimals = 2) {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
