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

if (!function_exists('spock_html')) {
    /**
     * Return the HTML markup of a component.
     *
     * @param function $component
     */
    function spock_html($component): string
    {
        ob_start();

        $component();

        $html = ob_get_clean();

        $html = spock()->html($html);

        return $html;
    }
}

if (!function_exists('spock_ob_html')) {
    /**
     * Return the HTML markup of a component.
     *
     * @param function $component
     */
    function spock_ob_html(callable $callable)
    {
        return spock()->ob_html($callable);
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

if (!function_exists('spock_css')) {
    /**
     * Return a minified version of a inline css.
     *
     * @param string $css The css to minify.
     * @param bool $style_tag Optional. If true, return the css wrapped in a style tag.
     * @return string
     */
    function spock_(string $css, $style_tag = true): string
    {
        return spock()->css($css, $style_tag);
    }
}

if (!function_exists('spock_ob_css')) {
    /**
     * Return a minified version of a inline js.
     * This is the "ob" buffered version of css method.
     *
     * @param callable $callable The callable to be minified.
     * @param bool $script_tag Optional. If true, return the css wrapped in a style tag.
     * @return string
     */
    function spock_ob_css(callable $callable, $style_tag = false)
    {
        return spock()->ob_css($callable, $style_tag);
    }
}

if (!function_exists('spock_admin_style')) {
    /**
     * Add an inline style to the admin head.
     *
     * @param callable $callable The callable to be minified.
     * @param bool $script_tag Optional. If true, return the css wrapped in a style tag.
     *
     * @uses add_action('admin_head');
     */
    function spock_admin_style(callable $callable, $style_tag = false)
    {
        return spock()->admin_style($callable, $style_tag);
    }
}

if (!function_exists('spock_js')) {
    function spock_js(string $js, $options = [])
    {
        return spock()->js($js, $options);
    }
}

if (!function_exists('spock_ob_js')) {
    function spock_ob_js(callable $callable, $options = [])
    {
        return spock()->ob_js($callable, $options);
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
