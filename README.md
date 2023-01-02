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

Sebelum menggunakan code dibawah ini anda harus mengkonfigurasi data server dulu di database

```php
$phone = "085231028718";

$w = Whatsapp::device(1)->data([
    'nama' => 'David',
    'nomor' => $phone,
])->copywriting('Halo [nama]');

/*------ Attach File Image, Video, Document ------*/
// $w->file("https://seminar.co.id/gold.jpeg");
// $w->file("http://staffnew.uny.ac.id/upload/131474242/penelitian/LAPORAN+PENELITIAN.pdf");
// $w->file("https://www.w3schools.com/html/mov_bbb.mp4");

/*------ Insert Button Max 3 ------*/
$w->button('Button 1');
$w->button('Button 2');
$w->button('Button 3');

/*------ Insert Select List ------*/
// $w->button_list('Promo Special', 'Lihat Menu', 'tokalink.id');
// $w->list('Makanan', function($list) {
//     $list->add('Nasi Goreng', 'Rp. 10.000');
//     $list->add('Nasi Rawon', 'Rp. 15.000');
//     $list->add('Nasi Ayam', 'Rp. 20.000');
//     $list->add('Nasi Soto', 'Rp. 25.000');
// });
// $w->list('Minuman', function($list) {
//     $list->add('Es Teh', 'Rp. 3.000');
//     $list->add('Dawet', 'Rp. 7.000');
//     $list->add('Cincau', 'Rp. 7.000');
//     $list->add('Kopi', 'Rp. 5.000');
// });

$r = $w->send($phone);
```

## Testing

```bash
composer test
```

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
