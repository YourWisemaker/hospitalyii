<?php

/**
 * Bootstrap file for the application
 * This file is included at the beginning of the application lifecycle
 */

// Include the CountHelper class
require_once __DIR__ . '/../helpers/CountHelper.php';

// Fix for count() on non-countable objects in PHP 7.2+
if (!function_exists('_count')) {
    /**
     * Safe count function that works with any variable type
     * 
     * @param mixed $var Variable to count
     * @return int Count result
     */
    function _count($var) {
        return \app\helpers\CountHelper::safeCount($var);
    }
}
