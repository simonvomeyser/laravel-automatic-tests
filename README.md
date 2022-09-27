# This is my package laravel-automatic-tests

[![Latest Version on Packagist](https://img.shields.io/packagist/v/simonvomeyser/laravel-automatic-tests.svg?style=flat-square)](https://packagist.org/packages/simonvomeyser/laravel-automatic-tests)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/simonvomeyser/laravel-automatic-tests/run-tests?label=tests)](https://github.com/simonvomeyser/laravel-automatic-tests/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/simonvomeyser/laravel-automatic-tests/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/simonvomeyser/laravel-automatic-tests/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/simonvomeyser/laravel-automatic-tests.svg?style=flat-square)](https://packagist.org/packages/simonvomeyser/laravel-automatic-tests)

A package for automatically testing all internal links of your application in your Feature-Tests.

In the most simple form, you just need one command to test that all of your linked, internal don't respond with an error.

- Written for peace of mind... and simplicity in mind
- Still quite configurable
- Works with PHPUnit and PEST PHP


```php
    //...
    public function testAllInternalPagesReachableOnFrontpage()
    {
        // Crawls all pages reachable from the root (url('/')) of your application
        // Makes sure, the all return a response code > 399
        StaticPagesTester::create()->run();
    }
    //...
```


## Installation

```bash
composer require simonvomeyser/laravel-automatic-tests
```

## Usage

- [ ] Usage plain
- [ ] working with alternatives
- [ ] base url?

## Testing

```bash
composer test
```

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
