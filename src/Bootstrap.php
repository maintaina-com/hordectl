<?php

declare(strict_types=1);

namespace Horde\Hordectl;

use Composer\InstalledVersions;

/**
 * Bootstrapping utilities for a console application.
 *
 * This should probably be part of a library.
 */
class Bootstrap
{
    private static string $rootDir;

    public static function run(): void
    {
        self::setupAutoloading();
        self::$rootDir = self::detectRootDir();
    }

    public static function setupAutoloading(): void
    {
        // Either communicated through vendor proxy or from canonical location
        $autoloaderVendorPath = $_composer_bin_dir ?? __DIR__ . '/../../../vendor/autoload.php';
        // If this is the root package
        $autoloaderRootPath = __DIR__ . '/../vendor/autoload.php';
        if (class_exists(InstalledVersions::class)) {
            // Externally setup autoloader
        } elseif (file_exists($autoloaderRootPath)) {
            require_once $autoloaderRootPath;
        } elseif (file_exists($autoloaderVendorPath)) {
            require_once $autoloaderVendorPath;
        } else {
            die('Autoloading not set up. Run "composer autoload-dump" or plant another autoloader into vendor/autoload.php');
        }
    }

    public static function detectRootDir()
    {
        // Composer case
        if (class_exists(InstalledVersions::class)) {
            return InstalledVersions::getRootPackage()['install_path'];
        }
        die('No non-composer method to detect root dir yet');
    }
}
