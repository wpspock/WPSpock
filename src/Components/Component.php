<?php

namespace WPScotty\WPSpock\Component;

if (!defined('ABSPATH')) {
    exit;
}

abstract class Component
{
    protected $name;

    public function __construct($name = "")
    {
        $this->name = $name;

        $this->init();
    }

    protected function init()
    {
    }

    abstract public function render();

    public function __toString()
    {
        return $this->render();
    }
}
