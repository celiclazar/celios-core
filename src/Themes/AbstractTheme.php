<?php

namespace Celios\Core\Themes;

use Celios\Core\Contracts\ThemeInterface;

abstract class AbstractTheme implements ThemeInterface
{
    protected string $id;
    protected string $name;
    protected string $description = '';
    protected string $version = '1.0.0';
    protected string $author = 'Celios';
    protected string $path = '';
    protected string $viewPath = '';
    protected ?string $screenshotUrl = null;
    protected array $menuLocations = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getViewPath(): string
    {
        return $this->viewPath ?: $this->path . '/views';
    }

    public function getScreenshotUrl(): ?string
    {
        return $this->screenshotUrl;
    }

    public function getMenuLocations(): array
    {
        return $this->menuLocations ?: [
            'primary' => 'Main Navigation (Header)',
            'footer' => 'Footer Navigation',
        ];
    }

    public function boot(): void
    {
        // Custom theme initialization hook (e.g. Blade directives, component aliases)
    }
}
