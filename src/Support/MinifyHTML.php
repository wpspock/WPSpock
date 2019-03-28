<?php

namespace WPScotty\WPSpock\Support;

if (!defined('ABSPATH')) {
    exit;
}

class MinifyHTML
{

    protected static $instance = null;
    protected $html = '';

    public function __construct($html = '')
    {
        $this->html = $html;
    }

    public static function init()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        add_action('template_redirect', [self::$instance, 'startMinify'], PHP_INT_MAX);
    }

    public function startMinify()
    {
        ob_start([$this, 'endMinify']);
    }

    public function endMinify($html)
    {
        $this->html = $html;

        return $this->minify();
    }

    protected function minify()
    {
        // first of all
        $this->html = str_replace("\r\n", "\n", trim($this->html));

        // deeper
        $pattern     = [
            '/<!--\s.*?-->/',
            // Remove all HTML comments
            '/[\n\r\t\v\e\f]/',
            // Remove all new lines, carriage returns, tabs, vertical whitespaces, esc & form feeds characters
            '/\s{2,}/',
            // Remove all spaces (when there are 2 or more)
        ];
        $replacement = ['', '', ' ',];

        return preg_replace($pattern, $replacement, $this->html);
    }

    public function __toString()
    {
        return $this->minify();
    }
}
