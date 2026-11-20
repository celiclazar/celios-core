<?php

namespace Celios\Core\Services;

use Celios\Core\Contracts\ThemeInterface;
use Celios\Core\Themes\JsonTheme;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ThemeInstallerService
{
    public function __construct(
        protected ThemeManager $themeManager
    ) {}

    /**
     * Installs a theme from an uploaded ZIP archive or local path.
     *
     * @param UploadedFile|string $zipFile
     * @return array{success: bool, message: string, theme: ?ThemeInterface}
     */
    public function installFromZip(UploadedFile|string $zipFile): array
    {
        $filePath = $zipFile instanceof UploadedFile ? $zipFile->getRealPath() : $zipFile;

        if (! file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'Uploaded zip file not found.',
                'theme' => null,
            ];
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [
                'success' => false,
                'message' => 'Unable to open ZIP archive. Please ensure it is a valid zip file.',
                'theme' => null,
            ];
        }

        // 1. Security check: prevent zip slip / directory traversal
        $manifestEntry = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            if (str_contains($filename, '..') || str_starts_with($filename, '/') || str_starts_with($filename, '\\')) {
                $zip->close();
                return [
                    'success' => false,
                    'message' => 'Security warning: ZIP contains illegal directory traversal paths.',
                    'theme' => null,
                ];
            }

            if (basename($filename) === 'theme.json') {
                $manifestEntry = $filename;
            }
        }

        if (! $manifestEntry) {
            $zip->close();
            return [
                'success' => false,
                'message' => 'Invalid theme archive: theme.json manifest was not found.',
                'theme' => null,
            ];
        }

        // 2. Read and validate manifest
        $manifestContent = $zip->getFromName($manifestEntry);
        $manifest = json_decode((string) $manifestContent, true);

        if (! is_array($manifest) || empty($manifest['name'])) {
            $zip->close();
            return [
                'success' => false,
                'message' => 'Invalid theme.json: theme must have at least a valid "name".',
                'theme' => null,
            ];
        }

        $themeId = preg_replace('/[^a-z0-9_-]/i', '', strtolower((string) ($manifest['id'] ?? '')));
        if (empty($themeId)) {
            $themeId = \Illuminate\Support\Str::slug($manifest['name'], '_');
        }

        if ($themeId === 'default') {
            $zip->close();
            return [
                'success' => false,
                'message' => 'Theme ID "default" is reserved for the core system theme.',
                'theme' => null,
            ];
        }

        // 3. Extract to themes/{themeId}
        $themesRoot = base_path('themes');
        if (! File::isDirectory($themesRoot)) {
            File::makeDirectory($themesRoot, 0755, true);
        }

        $targetDir = $themesRoot . '/' . $themeId;
        $prefixToRemove = dirname($manifestEntry);
        if ($prefixToRemove === '.' || $prefixToRemove === '') {
            $prefixToRemove = '';
        } else {
            $prefixToRemove = rtrim($prefixToRemove, '/\\') . '/';
        }

        // Extract files individually to handle nested folders cleanly
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            
            // Skip entries not inside the theme root folder in zip
            if ($prefixToRemove !== '' && ! str_starts_with($entryName, $prefixToRemove)) {
                continue;
            }

            $relativeSubPath = $prefixToRemove !== '' ? substr($entryName, strlen($prefixToRemove)) : $entryName;
            if (empty($relativeSubPath)) {
                continue;
            }

            $destPath = $targetDir . '/' . $relativeSubPath;

            if (str_ends_with($entryName, '/')) {
                if (! File::isDirectory($destPath)) {
                    File::makeDirectory($destPath, 0755, true);
                }
            } else {
                $dir = dirname($destPath);
                if (! File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }
                file_put_contents($destPath, $zip->getFromIndex($i));
            }
        }

        $zip->close();

        // 4. Publish public assets to public/themes/{themeId} if exists
        $this->publishThemeAssets($targetDir, $themeId);

        // 5. Instantiate and register
        try {
            $theme = new JsonTheme($targetDir);
            $this->themeManager->register($theme);

            return [
                'success' => true,
                'message' => "Theme '{$theme->getName()}' installed successfully!",
                'theme' => $theme,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => "Theme files extracted, but could not be loaded: {$e->getMessage()}",
                'theme' => null,
            ];
        }
    }

    /**
     * Publishes theme assets and screenshot to public/themes/{themeId}.
     */
    public function publishThemeAssets(string $themeDir, string $themeId): void
    {
        $publicDir = public_path("themes/{$themeId}");

        if (! File::isDirectory($publicDir)) {
            File::makeDirectory($publicDir, 0755, true);
        }

        // Copy assets folder if present
        $sourceAssets = $themeDir . '/assets';
        if (File::isDirectory($sourceAssets)) {
            File::copyDirectory($sourceAssets, $publicDir . '/assets');
        }

        // Copy screenshot.png if present
        if (File::exists($themeDir . '/screenshot.png')) {
            File::copy($themeDir . '/screenshot.png', $publicDir . '/screenshot.png');
        }
    }

    /**
     * Deletes an installed local theme.
     */
    public function deleteTheme(string $themeId): array
    {
        if (empty($themeId) || ! preg_match('/^[a-z0-9_-]+$/i', $themeId)) {
            return [
                'success' => false,
                'message' => 'Invalid theme identifier.',
            ];
        }

        $sanitizedId = strtolower($themeId);

        if ($sanitizedId === 'default') {
            return [
                'success' => false,
                'message' => __('themes.cannot_delete_default_theme'),
            ];
        }

        if ($this->themeManager->getActiveTheme()->getId() === $sanitizedId) {
            return [
                'success' => false,
                'message' => __('themes.cannot_delete_active_theme'),
            ];
        }

        $themePath = base_path("themes/{$sanitizedId}");
        if (File::isDirectory($themePath)) {
            File::deleteDirectory($themePath);
        }

        $publicPath = public_path("themes/{$sanitizedId}");
        if (File::isDirectory($publicPath)) {
            File::deleteDirectory($publicPath);
        }

        $this->themeManager->unregister($sanitizedId);

        return [
            'success' => true,
            'message' => __('themes.theme_deleted', ['theme' => $sanitizedId]),
        ];
    }
}
