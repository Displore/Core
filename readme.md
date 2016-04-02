# Displore Core

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Quality Score][ico-code-quality]][link-code-quality]

This is the core package of Displore, it helps you with installing any other (Displore) packages. It also comes with a general notifications, tags and logbook system.

## Install

### Via Composer

``` bash
$ composer require displore/core
```
Afterwards, add the service provider and dump Composer's autoloads.
`Displore\Core\CoreServiceProvider::class,`

### Configuration

```bash
$ php artisan vendor:publish --tag=displore.core.config
```

If you use the tags functionality, you'll need the migrations.
```bash
$ php artisan vendor:publish --tag=displore.core.migrations
```

## Usage

### Installing packages

For example, if you want to install the backend package called Biotope.
``` bash
$ php artisan displore:install Biotope
```
Everything is done for you, from composer to the laravel service provider.

Optional flags:
`--dev` (use composer require --dev)
`--config` (treat the given name as a path to a config file)

The `--config` flag is used the following way:
``` bash
$ php artisan displore:install app/myconfig.json --config
```
The installer will then cycle through the json file and install the package, or packages that are listed. This includes setting Laravel specific logic such as providers and aliases. See the documentation, or any of the Displore packages for an example of ta config file.

### Notifications

This package provides a notifications system out of the box. The Notifier class is resolved by Laravel.
Some examples:
```php
Notifier::notify("You have been tagged in a photo");
Notifier::flash("You Only Read This Once");
Notifier::get()->allForUser(Auth::user());
Notifier::get()->markAsRead($nId);
Notifier::flushOldAndReadFor($timestamp, 'user', Auth::user());
```

### Tags

Tags can be added to every model. They optionally have a description and a category. The Tagger class is resolved by Laravel.
In order to make it work, your model(s) should use the trait `Displore\Core\Tags\Taggable`.
Some examples:
```php
$task = Task::find(1);
Tagger::tag($task, 'Important');
// or...
$task->tag('Important');
Tagger::create('Important', 'Optional category', 'optional description');
Tagger::tagOrCreate($task, 'Very Important');
Tagger::untag($task, 'Important');
// or...
$task->untag('Important');
Tagger::syncTags($task, ['Important', 'Very Important']);
// or...
$task->syncTags(['Important', 'Very Important']);
$tagsForTask = $task->tags;
```

### Logbook

To get a simple overview of the contents of you logs, you can use the Logbook class to retrieve and parse all .log files.
The Biotope backend package has an example implementation of this functionality.
```php
$files = Logbook::getLogFiles(storage_path('logs'));
$logs = Logbook::compile($files);
$JsonLogs = Logbook::compile($files)->toJson();
```

### Menus
This package comes with a very basic implementation of a menu builder. By binding the `Displore\Core\Contracts\MenuBuilder` interface to another class you can easily swap it, for example with the excellent menu package created by [Spatie](https://github.com/spatie/laravel-menu).
Run `vendor:publish --tag=displore.core.menu` to publish the config file in which you can set the different menus. You can for example pass them along with your views in a controller:
```php
$menu = Menu::from(Config::get('displore.menu'))->build('main');
return view('index')->with($menu);
```

## Further documentation

To be made.

## Change log

Please see [changelog](changelog.md) for more information what has changed recently.

## Testing

In a Laravel application, with [Laravel Packager](https://github.com/Jeroen-G/laravel-packager):
``` bash
$ php artisan packager:git *Displore Github url*
$ php artisan packager:tests
$ phpunit
```

## Contributing

Please see [contributing](contributing.md) for details.

## Credits

- [JeroenG][link-author]
- [All Contributors][link-contributors]

## License

The EUPL License. Please see the [License File](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/displore/core.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/displore/core.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/displore/core
[link-code-quality]: https://scrutinizer-ci.com/g/displore/core
[link-author]: https://github.com/Jeroen-G
[link-contributors]: ../../contributors
