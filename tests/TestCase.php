<?php

namespace SimonVomEyser\LaravelAutomaticTests\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SimonVomEyser\LaravelAutomaticTests\LaravelAutomaticTestsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelAutomaticTestsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-automatic-tests_table.php.stub';
        $migration->up();
        */
    }
}
