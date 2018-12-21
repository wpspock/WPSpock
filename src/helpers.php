<?php
/**
 * Here you'll find some useful helpers functions
 */

if (!function_exists('spock')) {
    function spock()
    {
        return $GLOBALS[ 'WPSpock' ];
    }
}