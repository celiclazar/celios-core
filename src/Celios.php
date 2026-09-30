<?php

namespace Celios\Core;

use Composer\InstalledVersions;

class Celios
{
    /**
     * Get the current Celios CMS version dynamically from Composer / Git tags.
     */
    public static function version(): string
    {
        try {
            if (InstalledVersions::isInstalled('celios/core')) {
                $ver = InstalledVersions::getPrettyVersion('celios/core');
                if ($ver) {
                    return $ver;
                }
            }

            $root = InstalledVersions::getRootPackage();
            if (($root['name'] ?? null) === 'celios/celios') {
                $ver = $root['pretty_version'] ?? $root['version'] ?? null;
                if ($ver) {
                    return $ver;
                }
            }
        } catch (\Throwable $e) {
            // Fallback for test or custom environments where Composer runtime is unavailable
        }

        return '1.0.0';
    }
}
