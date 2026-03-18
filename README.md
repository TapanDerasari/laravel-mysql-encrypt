# Laravel MySql AES Encrypt/Decrypt

<a href="https://packagist.org/packages/tapanderasari/laravel-mysql-encrypt"><img src="https://img.shields.io/packagist/dt/tapanderasari/laravel-mysql-encrypt" alt="Total Downloads"></a> <a href="https://img.shields.io/packagist/v/tapanderasari/laravel-mysql-encrypt"><img src="https://img.shields.io/packagist/v/tapanderasari/laravel-mysql-encrypt" alt="Latest Stable Version"></a> <a href="https://github.com/TapanDerasari/laravel-mysql-encrypt/blob/master/LICENSE"><img src="https://img.shields.io/packagist/l/tapanderasari/laravel-mysql-encrypt" alt="License"></a>

Laravel database encryption at database side using native AES_DECRYPT and AES_ENCRYPT functions.
Automatically encrypt and decrypt fields in your Models.

## Version Compatibility

| Package Version | Laravel | PHP  |
|-----------------|---------|------|
| 3.x             | 13.x    | ^8.3 |
| 1.x             | 10–12   | ^8.0 |

## Install

### 1. Composer

```bash
composer require tapanderasari/laravel-mysql-encrypt
```

### 2. Publish config (optional)

```bash
php artisan vendor:publish --provider="TapanDerasari\MysqlEncrypt\Providers\LaravelServiceProvider"
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

## Schema

Encrypted columns must use `VARBINARY` (or `BINARY`/`BLOB`) in MySQL, not `VARCHAR`.

```php
Schema::create('users', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('password');
    $table->binary('first_name', 300);  // VARBINARY(300)
    $table->binary('last_name', 300);
    $table->binary('email', 300);
    $table->binary('telephone', 50);
    $table->rememberToken();
    $table->timestamps();
});
```

## Validators

`unique_encrypted`

```
unique_encrypted:<table>,<field(optional)>,<ignore_id(optional)>
```

`exists_encrypted`

```
exists_encrypted:<table>,<field(optional)>
```

## Scopes

A global scope `DecryptSelectScope` is automatically applied to models using the `Encryptable` trait. It rewrites SELECT queries to wrap encrypted columns with `AES_DECRYPT()`.

The following local scopes are available:

### Exact match

```php
User::whereEncrypted('email', 'john@example.com')->first();
User::whereNotEncrypted('email', 'john@example.com')->get();
```

### OR conditions

```php
User::whereEncrypted('email', 'a@b.com')
    ->orWhereEncrypted('email', 'c@d.com')
    ->get();

User::whereNotEncrypted('email', 'a@b.com')
    ->orWhereNotEncrypted('email', 'c@d.com')
    ->get();
```

### LIKE search

```php
User::whereEncryptedLike('first_name', 'John')->get();
User::orWhereEncryptedLike('first_name', 'Jane')->get();
```

### Ordering

```php
User::orderByEncrypted('first_name', 'asc')->get();
User::orderByEncryptedSort('last_name', 'desc')->get();
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
