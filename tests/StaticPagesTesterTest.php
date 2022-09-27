<?php

use SimonVomEyser\LaravelAutomaticTests\Classes\StaticPagesTester;
use Illuminate\Support\Facades\Route;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;

beforeEach(function () {
    Route::view('/', 'automatic-tests::tests.index')->name('index');
    Route::view('/page-1', 'automatic-tests::tests.page-1')->name('page-1');
    Route::view('/page-2', 'automatic-tests::tests.page-2')->name('page-2');
    Route::view('/page-3', 'automatic-tests::tests.page-3')->name('page-3');
    Route::view('/page-4', 'automatic-tests::tests.page-4')->name('page-4');
    Route::view('/page-hidden', 'automatic-tests::tests.page-hidden')->name('page-hidden');
});

it('runs in the absolute basic configuration', function () {
    StaticPagesTester::create()->run();
});

it('can handle the test case passed in and created in different ways', function () {
    $staticPagesTesterCreate = StaticPagesTester::create($this);
    $staticPagesTester = new StaticPagesTester($this);

    assertSame($staticPagesTester::class , $staticPagesTesterCreate::class);
});

it('parses and finds the expected amount of pages', function () {
    $staticPagesTester = StaticPagesTester::create()->run();

    // 6 = Base page + 4 reachable pages + 1 page with query param
    assertCount(6, $staticPagesTester->urisHandled);
});

it('can ignore query params', function () {
    $staticPagesTester = StaticPagesTester::create()
        ->ignoreQueryParameters()
        ->run();

    // 5 = Base page + 4 reachable pages
    assertCount(5, $staticPagesTester->urisHandled);
});
