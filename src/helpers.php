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
        return WPSpock::$theme;
    }
}

if (!function_exists('studly_case')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     * @return string
     */
    function studly_case($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }
}

if (!function_exists('the_slug')) {
    /**
     * Display the slug of post/page
     */
    function the_slug()
    {
        spock()->theSlug();
    }
}

if (!function_exists('import')) {
    /**
     * Import a file
     *
     * @param string $file
     * @return mixed
     */
    function import($path)
    {
        return spock()->import($path);
    }
}

if (!function_exists('component')) {
    /**
     * Load a component form /theme/Components/ folder.
     *
     * @param string $name Just the name of the component file.
     */
    function component($name)
    {
        return spock()->component($name);
    }
}

if (!function_exists('view')) {
    /**
     * Load a component form /theme/Components/ folder.
     *
     * @param string $name Just the name of the component file.
     */
    function view($name, $data = [])
    {
        return spock()->view($name, $data);
    }
}

if (!function_exists('cls')) {
    /**
     * Utility function used to merge the props classes with your own.
     *
     * @param array $classes Optional. Your own classes.
     * @param array $props Optional. The props classes.
     *
     * @return string
     */
    function cls($classes = [], $props = [])
    {
        return spock()->cls($classes, $props);
    }
}

if (!function_exists('_t')) {
    function _t($s)
    {
        return __($s, 'wpspock');
    }
}

if (!function_exists('_te')) {
    function _te($s)
    {
        _e($s, 'wpspock');
    }
}
