<?php

namespace SimonVomEyser\LaravelAutomaticTests\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SimonVomEyser\LaravelAutomaticTests\LaravelAutomaticTests
 */
class LaravelAutomaticTests extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \SimonVomEyser\LaravelAutomaticTests\LaravelAutomaticTests::class;
    }
}
