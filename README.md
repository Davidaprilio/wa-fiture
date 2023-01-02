# PackStarter untuk Fitur WA MSD

![Latest Version on wa-fiture](https://img.shields.io/badge/version-1.0.0-red)
![GitHub Tests Action Status](https://img.shields.io/badge/package-wa_fiture-red)

This a Starter Pack, Blade Component, and Helpers to Fiture CRM WA msd by DavidAprilio.

## Installation

You can install the package via composer:

```bash
composer require davidarl/wa-fiture
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="wa-fiture-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="wa-fiture-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="wa-fiture-views"
```

## Usage

```php
$waFiture = new Quods\Whatsapp();
echo $waFiture->echoPhrase('Hello, DavidArl!');
```

## Testing

```bash
composer test
```

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
