<?php

namespace SimonVomEyser\LaravelAutomaticTests;

use Illuminate\Support\Facades\Facade;

class StaticPagesTester extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \SimonVomEyser\LaravelAutomaticTests\Classes\StaticPagesTester::class;
    }
}
