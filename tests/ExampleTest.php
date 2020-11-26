<?php

namespace Rayblair\Filesystem\Tests;

use Orchestra\Testbench\TestCase;
use Rayblair\Filesystem\FilesystemServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [FilesystemServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
