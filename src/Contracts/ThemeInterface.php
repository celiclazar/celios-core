<?php

namespace Celios\Core\Contracts;

interface ThemeInterface
{
    /**
     * Unique identifier for the theme (e.g. 'default', 'agency', 'minimal').
     */
    public function getId(): string;

    /**
     * Human-readable display name.
     */
    public function getName(): string;

    /**
     * Description of the theme design and purpose.
     */
    public function getDescription(): string;

    /**
     * Semantic version string (e.g. '1.0.0').
     */
    public function getVersion(): string;

    /**
     * Theme author or agency name.
     */
    public function getAuthor(): string;

    /**
     * Absolute filesystem path to the theme root directory.
     */
    public function getPath(): string;

    /**
     * Absolute filesystem path to the theme's Blade views directory.
     */
    public function getViewPath(): string;

    /**
     * URL or relative path to a preview screenshot image.
     */
    public function getScreenshotUrl(): ?string;

    /**
     * Declared theme menu locations/slots.
     * e.g. ['primary' => 'Main Navigation (Header)', 'footer' => 'Footer Navigation']
     *
     * @return array<string, string>
     */
    public function getMenuLocations(): array;

    /**
     * Bootstrap the theme (registers view overrides, assets, hooks).
     */
    public function boot(): void;
}
