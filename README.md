# Laravel MySql AES Encrypt/Decrypt

<a href="https://packagist.org/packages/tapanderasari/laravel-mysql-encrypt"><img src="https://img.shields.io/packagist/dt/tapanderasari/laravel-mysql-encrypt" alt="Total Downloads"></a> <a href="https://img.shields.io/packagist/v/tapanderasari/laravel-mysql-encrypt"><img src="https://img.shields.io/packagist/v/tapanderasari/laravel-mysql-encrypt" alt="Latest Stable Version"></a> <a href="https://github.com/TapanDerasari/laravel-mysql-encrypt/blob/master/LICENSE"><img src="https://img.shields.io/packagist/l/tapanderasari/laravel-mysql-encrypt" alt="License"></a>

Laravel database encryption at database side using native AES_DECRYPT and AES_ENCRYPT functions.
Automatically encrypt and decrypt fields in your Models.

## Install

### 1. Composer

```bash
composer require tapanderasari/laravel-mysql-encrypt
```

### 2. Publish config (optional)

`Laravel`

```bash
php artisan vendor:publish --provider="TapanDerasari\MysqlEncrypt\Providers\LaravelServiceProvider"
```

`Lumen`

```bash
mkdir -p config
cp vendor/tapanderasari/laravel-mysql-encrypt/config/config.php config/mysql-encrypt.php
```

### 3. Set encryption key in `.env` file

```
APP_AESENCRYPT_KEY=yourencryptionkey
```

## Update Models

```php
<?php

namespace App;

use TapanDerasari\MysqlEncrypt\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Encryptable; // <-- 1. Include trait

    public array $encryptable = [ // <-- 2. Include columns to be encrypted
        'email',
        'first_name',
        'last_name',
        'telephone',
    ];
}
```

## Validators

`unique_encrypted`

```
unique_encrypted:<table>,<field(optional)>
```

`exists_encrypted`

```
exists_encrypted:<table>,<field(optional)>
```

## Scopes

Custom Local scopes available:

`whereEncrypted`
`whereNotEncrypted`
`orWhereEncrypted`
`orWhereNotEncrypted`
`orderByEncrypted`
`whereEncryptedLike`
`scopeOrderByEncryptedSort`

Global scope `DecryptSelectScope` automatically booted in models using `Encryptable` trait.

## Schema columns to support encrypted data

```php
Schema::create('users', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('password');
    $table->binary('first_name',300); // VARBINARY(300) for laravel 11.x and above versions
    $table->rememberToken();
    $table->timestamps();
});

// for laravel 10.x and below version, Once the table has been created, use ALTER TABLE to create VARBINARY
// or BLOB types to store encrypted data.
DB::statement('ALTER TABLE `users` ADD `first_name` VARBINARY(300)');
DB::statement('ALTER TABLE `users` ADD `last_name` VARBINARY(300)');
DB::statement('ALTER TABLE `users` ADD `email` VARBINARY(300)');
DB::statement('ALTER TABLE `users` ADD `telephone` VARBINARY(50)');
```

## Implementing encryption for existing data

For this you can create one command like

```
php artisan make:command EncryptionForExistingData
```

In this command you fetch existing table or model data without global scope `DecryptSelectScope`.

You can refer the example, clicking on below Example button:

<a href="https://github.com/TapanDerasari/laravel-mysql-encrypt/blob/master/existing_data_encryption.md" target="new"><img src="https://img.shields.io/badge/Example-green"></a>

## License

The MIT License (MIT). Please
see [License File](https://github.com/TapanDerasari/laravel-mysql-encrypt/blob/master/LICENSE) for more information.
