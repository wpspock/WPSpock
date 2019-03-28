<?php
/**
 * Here you'll find some useful helpers functions
 */

if (!function_exists('spock')) {

    /**
     * Return the instance of Theme.
     *
     * @return \WPScotty\WPSpock\Foundation\Theme
     */
    function spock()
    {
        return $GLOBALS['WPSpock'];
    }
}