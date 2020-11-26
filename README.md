# Auto Filesystem

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rayblair/Filesystem.svg?style=flat-square)](https://packagist.org/packages/rayblair/Filesystem)
[![Build Status](https://img.shields.io/travis/rayblair/Filesystem/master.svg?style=flat-square)](https://travis-ci.org/rayblair/Filesystem)
[![Quality Score](https://img.shields.io/scrutinizer/g/rayblair/Filesystem.svg?style=flat-square)](https://scrutinizer-ci.com/g/rayblair/Filesystem)
[![Total Downloads](https://img.shields.io/packagist/dt/rayblair/Filesystem.svg?style=flat-square)](https://packagist.org/packages/rayblair/Filesystem)

A collection of handy functions to extend Laravel's filesystem.

### Detect and resolve filesystem from service container

Automatically define the filesystem disk, and keep the filesystem consistent through the application based on environment settings.

Default to s3, unless environment is sandbox, or environment is local and doesn't have s3.

### Extend the filesystem's functionality

Extend Laravel's Storage facade with your own functionality.

### MoveToDisk command

Easily move files from one disk to another.

## Installation

<!-- You can install the package via composer: -->

<!-- ```bash
composer require rayblair/filesystem
``` -->

Update `composer.json` with the following:

```
"require": {
        ...
        "rayblair/filesystem": "*"
    },
    "repositories": [
        ...
        {
            "type": "vcs",
            "url": "https://github.com/rayblair06/filesystem.git"
        },
        ...
    ],
```

## Usage

### Resolve from Service Container

Makesure to resolve all Storage uses from the service container.

```
use Illuminate\Contracts\Filesystem\Filesystem;

// Normal Use
Storage::get($filename);

// Our Use
app(Filesystem::class)->get($filename);
```

### Extend the filesystem's functionality

Adding new methods to `ExtendFilesystem.php` will allow you use them while resolving from the service container

```
class ExtendFilesystem
{
    ...
    public function foo()
    {
        return 'bar';
    }
    ...
}

use Illuminate\Contracts\Filesystem\Filesystem;

// Returns the string 'bar'
app(Filesystem::class)->foo();
```

### MoveToDisk command

Command `php artisan move:to-disk {from_disk} {to_disk}` will allow you to easily move files from one disk to another.

**Note: This will make use of the queuing system to move files.**

```
php artisan move:to-disk local s3
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email rayblair06@hotmail.com instead of using the issue tracker.

## Credits

-   [Ray Blair](https://github.com/rayblair)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
