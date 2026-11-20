<?php

namespace Celios\Core\Themes;

class JsonTheme extends AbstractTheme
{
    public function __construct(string $directoryPath)
    {
        $this->path = rtrim($directoryPath, '/\\');
        $manifestPath = $this->path . '/theme.json';

        if (! file_exists($manifestPath)) {
            throw new \InvalidArgumentException("theme.json not found in directory: {$this->path}");
        }

        $json = json_decode(file_get_contents($manifestPath), true);
        if (! is_array($json)) {
            throw new \InvalidArgumentException("Invalid JSON in theme.json: {$manifestPath}");
        }

        $this->id = (string) ($json['id'] ?? basename($this->path));
        $this->name = (string) ($json['name'] ?? ucfirst($this->id));
        $this->description = (string) ($json['description'] ?? '');
        $this->version = (string) ($json['version'] ?? '1.0.0');
        $this->author = (string) ($json['author'] ?? 'Unknown');
        $this->viewPath = isset($json['view_path']) 
            ? $this->path . '/' . ltrim($json['view_path'], '/') 
            : $this->path . '/views';
        $this->menuLocations = (array) ($json['menu_locations'] ?? []);

        if (file_exists($this->path . '/screenshot.png')) {
            $publicDir = public_path('themes/' . $this->id);
            $publicFile = $publicDir . '/screenshot.png';

            if (! file_exists($publicFile)) {
                if (! is_dir($publicDir)) {
                    @mkdir($publicDir, 0755, true);
                }
                @copy($this->path . '/screenshot.png', $publicFile);
            }

            $this->screenshotUrl = asset('themes/' . $this->id . '/screenshot.png');
        } elseif (isset($json['screenshot'])) {
            $this->screenshotUrl = (string) $json['screenshot'];
        }
    }
}
