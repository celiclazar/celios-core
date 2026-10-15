<?php

namespace Celios\Core\Themes;

class DefaultTheme extends AbstractTheme
{
    public function __construct()
    {
        $this->id = 'default';
        $this->name = 'Celios Core Default';
        $this->description = 'Standard built-in Celios theme with modular blocks, dynamic navigation, and clean typography.';
        $this->version = '1.0.0';
        $this->author = 'Celios Team';
        $this->path = dirname(__DIR__, 2);
        $this->viewPath = $this->path . '/resources/views';
        $this->menuLocations = [
            'primary' => 'Main Navigation (Header)',
            'footer' => 'Footer Navigation',
        ];
    }
}
