<?php

namespace Pollen\Opcache\Test;

use Illuminate\Foundation\Application as LaravelApplication;
use Pollen\Opcache\OpcacheServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param  LaravelApplication  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.url', 'https://ecranlarge.docker.localhost/');
        $app['config']->set('opcache.url', 'https://ecranlarge.docker.localhost/');
        $app['config']->set('app.key', 'base64:NzomrFIcRyc3IcbgaCRG95yL+ihJrNYS8f6Jr/YRSDg=');
    }

    /**
     * @param  LaravelApplication  $app
     */
    protected function getPackageProviders($app): array
    {
        return [OpcacheServiceProvider::class];
    }
}
