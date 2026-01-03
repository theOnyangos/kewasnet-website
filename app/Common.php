<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('formatFileSize')) {
    /**
     * Format file size in human readable format
     *
     * @param int $size File size in bytes
     * @return string Formatted file size
     */
    function formatFileSize($size)
    {
        if ($size == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($size, 1024));
        
        return round($size / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Format time as "time ago" string
     *
     * @param string $datetime DateTime string
     * @return string Formatted time ago string
     */
    function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) {
            return 'just now';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($time < 604800) {
            $days = floor($time / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } elseif ($time < 2419200) {
            $weeks = floor($time / 604800);
            return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
        } elseif ($time < 29030400) {
            $months = floor($time / 2419200);
            return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
        } else {
            $years = floor($time / 29030400);
            return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
        }
    }
}
