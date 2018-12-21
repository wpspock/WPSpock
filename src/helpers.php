<?php
/**
 * Here you'll find some useful helpers functions
 */

if (!function_exists('studly_case')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param  string $value
     * @return string
     */
    function studly_case($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }
}

if (!function_exists('provider')) {
    /**
     * Return the slug of post/page
     *
     * @param $key string Provider key
     * @return mixed|null
     */
    function provider($key)
    {
        return $GLOBALS["spock_service_provider_{$key}"] ?? null;
    }
}

if (!function_exists('the_slug')) {
    /**
     * Display the slug of post/page
     */
    function the_slug()
    {
        global $post;

        echo $post->post_name ?? "";
    }
}