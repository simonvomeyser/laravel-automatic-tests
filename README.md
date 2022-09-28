# Automatic Tests for Static Pages

[![Latest Version on Packagist](https://img.shields.io/packagist/v/simonvomeyser/laravel-automatic-tests.svg?style=flat-square)](https://packagist.org/packages/simonvomeyser/laravel-automatic-tests)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/simonvomeyser/laravel-automatic-tests/run-tests?label=tests)](https://github.com/simonvomeyser/laravel-automatic-tests/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/simonvomeyser/laravel-automatic-tests/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/simonvomeyser/laravel-automatic-tests/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/simonvomeyser/laravel-automatic-tests.svg?style=flat-square)](https://packagist.org/packages/simonvomeyser/laravel-automatic-tests)

A package to quickly and automatically test all static, internal links in your Laravel app. Working with PHPUnit oder PEST. 🎉

There are a many [options](https://github.com/simonvomeyser/laravel-automatic-tests#configuration), but the most simple test is:

```php
    //...
    public function testAllStaticPages()
    {
        // Crawls all pages reachable from the root of your application
        // Makes sure, the all links return a response code < 400
        StaticPagesTester::create()->run();
    }
    //...
```

## Installation

```bash
composer require simonvomeyser/laravel-automatic-tests
```

There are no configs to be published since all configuration can be made via fluid methods.

## Usage

Without any configuration, you can use this package...

with the [PEST framework](https://pestphp.com/)
```php
<?php
// tests/StaticPagesTest.php
use SimonVomEyser\LaravelAutomaticTests\StaticPagesTester;

it('tests all pages reachable from the frontpage', function () {
    StaticPagesTester::create()->run();
});
```

with the [PHPUnit](https://phpunit.de/)
```php
<?php
// tests/feature/StaticPagesTest.php

namespace Tests\Feature;

use SimonVomEyser\LaravelAutomaticTests\StaticPagesTester;
use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    public function testAllInternalPagesReachableOnFrontpage()
    {
        StaticPagesTester::create()->run();
    }
}
```

## Configuration

There is quite some ways to configure the default behaviour, all fluid methods can also be used together.

### Ignoring query params and anchor links

Since some websites use a lot of query links (`example.com/search?q=lorem`) or a lot of links to pages with page anchors (`example.com/search#section-2`) it is possible to ignore these variations and check the path (in this example `example.com/search`) only once.

```php
    // ...
    StaticPagesTester::create()
        ->ignorePageAnchors()
        ->ignoreQueryParameters()
        ->run();
    // ...
```

### Starting from a different page

You can of course start the crawling from a different page, the default is `/`, what might be the frontpage on most websites.

```php
    // ...
    StaticPagesTester::create()
        ->startFromUrl('/home')
        ->run();
    // ...
```

### Skipping the default assertion, access found uris + responses
 
To keep the api as lean as possible, the default assertion checks, that no `4xx` or `5xx` errors are returned from any given url.

To disable this behavior, you can skip the assertion and handle all uris and responses as you like.

```php
    // ...
    $spt = StaticPagesTester::create()
        ->skipDefaultAssertion()
        ->run();
    
    // access all uris that were found
    // ['/', '/about', ...]
    dump($spt->urisHandled)
    
    // access all uris with their testresponses
    // ['/' => $response, '/about' => $response, ...]
    dump($spt->respones)
    
    // Make own assertions
    $spt->responses['/admin']->assertRedirect();
    
    // ...
```

### Adding custom assertions

While doing all custom assertions after finding all URIs is possible, you may skip the fuzz and add your custom assertions via `addAssertion` right away.

```php
    // ...
    StaticPagesTester::create()
            ->addAssertion(function($response) {
                // Example: allow anything except for 5xx errors for all uris
                assertTrue($response->status() < 500);
            })
            ->skipDefaultAssertion()
            ->addAssertion(function($response, $uri) {
                // Example: check for redirects only when accessing admin area
                if(str_contains($uri, '/admin')) {
                    $response->assertRedirect()
                }
            })
            ->run();
    // ...
```

### Defining a maximum of pages and crawls 

Since automatic crawling can munch a lot of memory, you may define a maximum of pages/uris or a maximum crawl depth (`1` meaning only the url you start from and the pages found there will be handled)

```php
    // ...
    StaticPagesTester::create()
            ->maxPages(3)
            ->maxCrawlDepth(1)
            ->run();
    // ...
```


### A note on passing the testcase

This package needs the current testcase to start it's crawling - you actually would need to pass the Test itself to new up the `StaticPagesTester` like so: 

```php
// This is NOT necessary, but it's happening under the hood
$staticPagesTester = new StaticPagesTester($this);
```

Since this is no pretty API, the package tries some magic with the `create()` method to find the calling testcase.

Keep that in mind if you want to use the `StaticPagesTester` elsewhere, not from a test.

## Roadmap, ideas, features not implemented yet

I have a lot of ideas for that package, but we all have little time, and I wrote this mostly for myself with only basic features. If somebody is interested let me know, we could discuss a few ideas like

- [ ] Making this work efficiently in `Laravel Dusk` to render JavaScript 
- [ ] Making this work with `DataProviders` to have a much better output (one test per found uri)
- [ ] Add a way to also check for more things like broken external links, thus making this package deserving the title "LaravelAutomaticTests" 😀

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Simon vom Eyser](https://github.com/simonvomeyser)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Testing

To test this testing package, run the following test command (#testception)

```bash
composer test
```
