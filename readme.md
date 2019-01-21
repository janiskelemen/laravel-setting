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
composer require janiskelemen/laravel-setting
```

## Publish config and migration

```bash
php artisan vendor:publish --tag=setting
php artisan migrate
```

## Setup your settings config

After publishing the setting files you will find a new configuration file: config/setting.php
In this config you can define your basic settings like below.

```php
    return [
        'app_name' => 'My Application',
        'user_limit' => 10,
    ];
```

```php
    Setting::get('app_name');
    //retruns 'My Application'
```

### You can also use multi level arrays

```php
    return [
        'priorities' => [
            'low' => 1,
            'medium' => 2,
            'hight' => 3
        ],
    ];
```

```php
    Setting::get('priorities.medium');
    //retruns 2
```

### Defining optional config values

If you want to store additional data for a particular setting you can do so using an array and name one of the parameters
'default_value' which will be the default for the setting and is what gets returned by Settings::get('app_name') in this case.

```php
    return [
        'app_name' => [
            'type' => 'text', /* Optional config values */
            'max' => 255, /* Optional config values */
            'default_value' => 'My Application' /* <- This value will be returned by Setting::get('app_name') if key is not found in DB */
        ],
        'user_limit' => 10,
    ];
```

```php
    Setting::get('app_name');
    //retruns 'My Application'

    // You can still access the optional parameters
    Setting::get('app_name.max')
    //retruns 255
```

### Scoped settings

You might want to save some settings only for a certain users. You can do this using a multi array.
Those setting naturally wont life your config file but can be saved during runtime into the Database.

Set save the new setting on runtime:

```php
    // Save a new setting under user1.dark_mode
    Setting::set('user' . $user->id . '.dark_mode', true);
```

Now you can get the value:

```php
    Setting::get('user' . $user->id . '.dark_mode');
```

The above will return null if the setting does not exist for this user.
In order to return something else you can set a default as the second parameter:

```php
    Setting::get('user' . $user->id . '.dark_mode', false);
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

Please see [contributing.md](contributing.md) for details.

## Security

If you discover any security related issues, please send me a DM on Twitter [@janiskelemen](https://twitter.com/janiskelemen) instead of using the issue tracker.

## Credits

This package is mostly a fork of [UniSharp/laravel-settings](https://github.com/UniSharp/laravel-settings)

-   [Janis Kelemen](https://twitter.com/janiskelemen)
-   [All Contributors][link-contributors]

## License

MIT. Please see the [license file](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/janiskelemen/laravel-setting.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/janiskelemen/laravel-setting.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/janiskelemen/laravel-setting/master.svg?style=flat-square
[ico-styleci]: https://github.styleci.io/repos/166064246/shield?branch=master
[link-packagist]: https://packagist.org/packages/janiskelemen/laravel-setting
[link-downloads]: https://packagist.org/packages/janiskelemen/laravel-setting
[link-travis]: https://travis-ci.org/janiskelemen/laravel-setting
[link-styleci]: https://github.styleci.io/repos/166064246
[link-author]: https://github.com/janiskelemen

[link-contributors]: ../../contributors]
