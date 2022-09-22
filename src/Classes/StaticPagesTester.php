<?php
namespace SimonVomEyser\LaravelAutomaticTests\Classes;

class StaticPagesTester
{
    public $requestClient;

    public function __construct()
    {
        $this->requestClient = 'can make requests';
    }

    public function test()
    {
        echo 'test';
    }

    public function run()
    {
        echo $this->requestClient;
    }

}
