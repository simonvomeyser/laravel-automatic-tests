<?php

namespace SimonVomEyser\LaravelAutomaticTests;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SimonVomEyser\LaravelAutomaticTests\Commands\LaravelAutomaticTestsCommand;

class LaravelAutomaticTestsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-automatic-tests')
            ->hasConfigFile()
            ->hasViews();
    }
}
