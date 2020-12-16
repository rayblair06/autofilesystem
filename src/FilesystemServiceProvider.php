<?php

namespace Rayblair\Filesystem;

use Exception;
use Illuminate\Support\ServiceProvider;
use Rayblair\Filesystem\Commands\MoveToDisk;
use Illuminate\Contracts\Filesystem\Filesystem;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Register Commands
            $this->registerCommands();

            // Publish Vendor Assets
            $this->registerVendorPublish();
        }

        // Workout our Default Disk
        $default_disk = $this->getDefaultDisk();

        // Bind the Storage Facade to the Filesystem Contract
        $this->bindFilesystemContract($default_disk);

        // Extend our Filesystem with our Filesystem Decorator
        $this->extendFilesystem($default_disk);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfig();
    }

    /**
     * Register our Commands
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->commands([
            MoveToDisk::class,
        ]);
    }

    /**
     * Register Vendor Publish
     *
     * @return void
     */
    private function registerVendorPublish()
    {
        $this->publishes([
            __DIR__ . '/../config/rb-filesystem.php' => config_path('rb-filesystem.php'),
        ]);
    }

    /**
     * Merge our Config file
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/rb-filesystem.php',
            'rb-filesystem'
        );
    }

    /**
     * Determine our Default Storage Disk
     *
     * @return string
     */
    private function getDefaultDisk() : string
    {
        // Use Cloud by default
        $disk = 's3';

        // If sandbox, reference our local storage
        if (config('app.env') == 'sandbox') {
            $disk = 'public';
        }

        // If our developer doesn't have s3, use local
        if ((config('app.env') == 'local') && (config('filesystems.disks.s3.secret') == null)) {
            $disk = 'public';
        }

        return $disk;
    }

    /**
     * Bind the Storage Facade to the Laravel Filesystem Contract
     *
     * @param string $disk
     * @return void
     */
    private function bindFilesystemContract(string $disk)
    {
        // Bind our Filesystem to this Storage
        $this->app
            ->bind(Filesystem::class, function ($app) use ($disk) {
                return $app['filesystem']->disk($disk);
            });
    }

    /**
     * Extend our Filesystem Contract with our decorator
     *
     * @param string $disk
     * @return void
     */
    private function extendFilesystem(string $disk)
    {
        $this->app->extend(Filesystem::class, function ($service, $app) use ($disk) {
            $decorator_class = config('rb-filesystem.extend_filesystem_class');

            if (!class_exists($decorator_class)) {
                throw new Exception("Decorator Class: {$decorator_class} doesn't exist.");
            }
            
            return new $decorator_class($service, $disk);
        });
    }
}
