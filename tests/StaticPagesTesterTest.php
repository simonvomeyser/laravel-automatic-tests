<?php

use PHPUnit\Framework\ExpectationFailedException;
use SimonVomEyser\LaravelAutomaticTests\Classes\StaticPagesTester;
use Illuminate\Support\Facades\Route;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;


beforeEach(function () {
    Route::view('/', 'automatic-tests::tests.index')->name('index');
    Route::view('/page-1', 'automatic-tests::tests.page-1')->name('page-1');
    Route::view('/page-2', 'automatic-tests::tests.page-2')->name('page-2');
    Route::view('/page-3', 'automatic-tests::tests.page-3')->name('page-3');
    Route::view('/page-4', 'automatic-tests::tests.page-4')->name('page-4');
    Route::view('/page-hidden', 'automatic-tests::tests.page-hidden')->name('page-hidden');
    Route::view('/page-also-hidden', 'automatic-tests::tests.page-also-hidden')->name('page-also-hidden');
    Route::view('/page-broken', 'automatic-tests::tests.page-broken')->name('page-broken');

    $this->expectedUris = [
        "http://localhost",
        "http://localhost/page-1",
        "http://localhost/page-2",
        "/page-3",
        "http://localhost/page-4",
        "http://localhost/page-2?search=lorem",
        "http://localhost/page-2#section-link",
        "http://localhost/page-2?search=lorem#section-link",
    ];
});

it('runs in the absolute basic configuration', function () {
    StaticPagesTester::create()->run();
});

it('can handle the test case passed in and created in different ways', function () {
    $staticPagesTesterCreate = StaticPagesTester::create($this);
    $staticPagesTester = new StaticPagesTester($this);

    assertSame($staticPagesTester::class, $staticPagesTesterCreate::class);
});

it('parses and finds the expected amount of pages', function () {
    $staticPagesTester = StaticPagesTester::create()->run();

    assertSame($this->expectedUris, $staticPagesTester->urisHandled);
});

it('can ignore query params', function () {
    $staticPagesTester = StaticPagesTester::create()
        ->ignoreQueryParameters()
        ->run();

    assertCount(6, $staticPagesTester->urisHandled);
});

it('can ignore page anchors', function () {
    $staticPagesTester = StaticPagesTester::create()
        ->ignorePageAnchors()
        ->run();

    assertCount(6, $staticPagesTester->urisHandled);
});

it('can ignore both page anchors and query params', function () {
    $staticPagesTester = StaticPagesTester::create()
        ->ignorePageAnchors()
        ->ignoreQueryParameters()
        ->run();

    assertCount(5, $staticPagesTester->urisHandled);
});

it('can parse starting from another base url', function () {
    $staticPagesTester = StaticPagesTester::create()
        ->startFromUrl('/page-2')
        ->run();

    assert(sort($this->expectedUris), sort($staticPagesTester->urisHandled));
});

it('finds hidden links when starting from hidden page', function () {
    $staticPagesTester = StaticPagesTester::create()
        ->startFromUrl('page-hidden')
        ->run();

    assertCount(2, $staticPagesTester->urisHandled);
});

it('it throws errors on pages that are not reachable', function () {
    $caughtExceptionClass = null;
    try {
        StaticPagesTester::create()
            ->startFromUrl('page-broken')
            ->run();
    } catch (\Exception $e) {
        $caughtExceptionClass = get_class($e);
    }

    assertSame(ExpectationFailedException::class, $caughtExceptionClass);
});

it('can skip the default assertions', function () {
    $staticPagesTester = StaticPagesTester::create()
        ->startFromUrl('page-broken')
        ->skipDefaultAssertion()
        ->run();

    assertCount(2, $staticPagesTester->urisHandled);
});

it('can add custom assertion', function () {
    $customAssertionCalled = 0;
    $staticPagesTester = StaticPagesTester::create()
        ->startFromUrl('page-broken')
        ->skipDefaultAssertion()
        ->addAssertion(function($response)  use (&$customAssertionCalled) {
            $customAssertionCalled++;
            assertTrue($response->status() < 499);
        })
        ->run();

    assertCount(2, $staticPagesTester->urisHandled);
    assertSame(2, $customAssertionCalled);
});

it('can define a maximum crawl depth', function () {
    $staticPagesTester = StaticPagesTester::create()
        ->maxCrawlDepth(1)
        ->run();

    assertCount(6, $staticPagesTester->urisHandled);
});

it('can define a maximum of pages to crawl', function () {
    $staticPagesTester = StaticPagesTester::create()
        ->maxPages(3)
        ->run();

    assertCount(3, $staticPagesTester->urisHandled);
});
