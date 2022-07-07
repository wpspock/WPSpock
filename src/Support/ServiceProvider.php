<?php

namespace WPScotty\WPSpock\Support;

abstract class ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    abstract public function register();

    /**
     * Instance of main theme.
     *
     * @var
     */
    protected $theme;

    public function __construct($theme)
    {
        $this->theme = $theme;
    }

    /**
     * Dynamically handle missing method calls.
     *
     * @param string $method
     * @param array  $parameters
     */
    public function __call(string $method, $parameters)
    {
        if ($method == 'boot') {
            return;
        }
    }
}
