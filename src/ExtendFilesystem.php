<?php

namespace Rayblair\Filesystem;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * Decorator for Laravel's Filesystem
 *
 */
class ExtendFilesystem
{
    use ForwardsCalls;

    /**
     *  Our Filesystem
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    public $filesystem;

    /**
     * Our Disk Name binded to our Filesystem Adapter
     *
     * @var string
     */
    public $disk;

    public function __construct(FilesystemAdapter $filesystem, String $disk = null)
    {
        $this->filesystem    = $filesystem;
        $this->disk          = $disk;
    }

    /**
     * Return our Disk name that we are using for the FilesystemAdapter
     *
     * @return void
     */
    public function getDiskName()
    {
        return $this->disk;
    }

    /**
     * Handle dynamic method calls to the filesystem.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->filesystem, $method, $parameters);
    }
}
