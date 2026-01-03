<?php

if (!function_exists('hex2rgba')) {
    function hex2rgba($color, $opacity = 1) {
        // Remove '#' if present
        $color = ltrim($color, '#');
        
        // Handle shorthand hex color (e.g. #03F)
        if (strlen($color) == 3) {
            $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
        }
        
        // Convert hex to rgb
        $rgb = [
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2))
        ];
        
        return sprintf('rgba(%d, %d, %d, %.2f)', $rgb['r'], $rgb['g'], $rgb['b'], $opacity);
    }
}