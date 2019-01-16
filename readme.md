# Advanced Settings Manager for Laravel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

-   Simple key-value storage
-   Optional config file for default settings supported
-   Support multi-level array (dot delimited keys) structure
-   Localization supported

## Installation

Via Composer

```bash
$ composer require janiskelemen/laravel-setting
```

## Publish config and migration

```bash
$ php artisan vendor:publish --tag=setting
$ php artisan migrate
```

## Usage

```php
Setting::get('name');
// get setting value with key 'name'
// If this key is not found in DB then it will return the value defined from the config file or null if the key is also not defined in the config file.

Setting::get('name', 'Joe');
// get setting value with key 'name'
// return 'Joe' if the key does not exists. This will overwrite the default coming from the config file.

Setting::all();
// get all settings.
// This will merge the setting.php config file with the values (only where lang is null) found in the database and returns a collection.

Setting::lang('zh-TW')->get('name', 'Joe');
// get setting value with key and language

Setting::set('name', 'Joe');
// set setting value by key

Setting::lang('zh-TW')->set('name', 'Joe');
// set setting value by key and language

Setting::has('name');
// check the key exists in database, return boolean

Setting::lang('zh-TW')->has('name');
// check the key exists by language in database, return boolean

Setting::forget('name');
// delete the setting from database by key

Setting::lang('zh-TW')->forget('name');
// delete the setting from database by key and language
```

## Dealing with locale

By default language parameter are being resets every set or get calls. You could disable that and set your own long term language parameter forever using any route service provider or other method.

```php
Setting::lang(App::getLocale())->langResetting(false);
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

```bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email jak@spacemates.io instead of using the issue tracker.

## Credits

-   [Janis Kelemen][link-author]
-   [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/janiskelemen/laravel-setting.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/janiskelemen/laravel-setting.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/janiskelemen/laravel-setting/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield
[link-packagist]: https://packagist.org/packages/janiskelemen/laravel-setting
[link-downloads]: https://packagist.org/packages/janiskelemen/laravel-setting
[link-travis]: https://travis-ci.org/janiskelemen/laravel-setting
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/janiskelemen

[link-contributors]: ../../contributors]
