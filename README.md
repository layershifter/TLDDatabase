<!-- Logo -->
<p align="center">
    <img height="128" width="128" src="https://github.com/layershifter/TLDDatabase/raw/master/logo.png">
</p>

<!-- Name -->
<h1 align="center">
  TLDDatabase
</h1>

<!-- Badges -->
<p align="center">
  <a href="https://travis-ci.org/layershifter/TLDDatabase">
    <img alt="Build Status" src="https://travis-ci.org/layershifter/TLDDatabase.svg" />
  </a>
  <a href="https://codeclimate.com/github/layershifter/TLDDatabas">
    <img alt="Code Climate" src="https://codeclimate.com/github/layershifter/TLDDatabase/badges/gpa.svg" />
  </a>
  <a href="https://codecov.io/gh/layershifter/TLDDatabase">
    <img alt="codecov" src="https://codecov.io/gh/layershifter/TLDDatabase/branch/master/graph/badge.svg" />
  </a>
</p>

Abstraction layer for [Public Suffix List](https://publicsuffix.org/) in PHP.
Used by [TLDExtract](https://github.com/layershifter/TLDExtract).

---

Main idea of library provide easy and fast access to actual database of Public Suffix List. Library always supplied with
actual database. 

This package is compliant with [PSR-1][], [PSR-2][], [PSR-4][]. If you notice compliance oversights, please send a patch
via pull request.

## Requirements

The following versions of PHP are supported:

* PHP 5.5
* PHP 5.6
* PHP 7.0
* PHP 7.1
* HHVM

## Basic usage

First of all you need load Store class.
```php
$store = new \LayerShifter\TLDDatabase\Store();
```

For check existence of entry in database you need use `isExists` method:
```php
$store->isExists(string $suffix) : bool;

$store->isExists('com'); // true
$store->isExists('comcom'); // false
``` 

_Note: suffix must be without leading dot._

For get type of suffix you need use `getType` method:

For check existence of entry in database you need use `isExists` method:
```php
$store->isExists(string $suffix): int;

$store->getType('com'); // \LayerShifter\TLDDatabase\Store::TYPE_ICANN = 1
$store->getType('s3.amazonaws.com'); // \LayerShifter\TLDDatabase\Store::TYPE_PRIVATE = 2
```

If entry doesn't exists method will throw exception, else it will return one of integer constants:
- ```\LayerShifter\TLDDatabase\Store::TYPE_ICCAN```;
- ```\LayerShifter\TLDDatabase\Store::TYPE_PRIVATE```;

For direct check of type you can use `isICCAN` or `isPrivate` method.
```php
$store->isICCAN(string $suffix) : bool;

$store->isICCAN('com'); // true
$store->isICCAN('s3.amazonaws.com'); // false

$store->isPrivate(string $suffix) : bool;

$store->isPrivate('com'); // false
$store->isPrivate('s3.amazonaws.com'); // true
```

## Advanced usage

There are some cool features for developers.

#### Custom database

If you need operate with custom (non-packaged) database you simply need to add argument to `Store` constructor.
```php
$store = new \LayerShifter\TLDDatabase\Store(string $filename);
$store = new \LayerShifter\TLDDatabase\Store(__DIR__ . '/cache/datatabase.php');
```

#### Update

If you use custom database you need update it :wink: So, you can can use `Update` class.
```php
$update = new \LayerShifter\TLDDatabase\Update(string $filename);
$update = new \LayerShifter\TLDDatabase\Update(__DIR__ . '/cache/datatabase.php');

$update->run();
```

#### HTTP-adapter

Basically library uses cURL adapter for updates, but you can use custom adapter.

```php
class customHttp implements \LayerShifter\TLDDatabase\Http\AdapterInterface {
    public function get() {} 
}

$update = new \LayerShifter\TLDDatabase\Update(__DIR__ . '/cache/datatabase.php', 'customHttp');
$update->run();
```

## Install

Via Composer

``` bash
$ composer require layershifter/tld-database
```

## Testing
``` bash
$ composer test
```

## Versioning

Library uses [SemVer](http://semver.org/) versioning. Where:
 - major makes incompatible API changes;
 - minor adds functionality, fully backwards-compatible;
 - patch is update of database from Public Suffix List.
 
Database has every week update cycle.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## License

This library is released under the Apache 2.0 license. Please see [License File](LICENSE) for more information.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md