# Laravel/Lumen MySql AES Encrypt/Decrypt

Laravel/Lumen database encryption at database side using native AES_DECRYPT and AES_ENCRYPT functions.
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

use DanielPardamean\MysqlEncrypt\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Encryptable; // <-- 1. Include trait

    protected $encryptable = [ // <-- 2. Include columns to be encrypted
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
    $table->rememberToken();
    $table->timestamps();
});

// Once the table has been created, use ALTER TABLE to create VARBINARY
// or BLOB types to store encrypted data.
DB::statement('ALTER TABLE `users` ADD `first_name` VARBINARY(300)');
DB::statement('ALTER TABLE `users` ADD `last_name` VARBINARY(300)');
DB::statement('ALTER TABLE `users` ADD `email` VARBINARY(300)');
DB::statement('ALTER TABLE `users` ADD `telephone` VARBINARY(50)');
```

## License

The MIT License (MIT). Please
see [License File](https://github.com/TapanDerasari/laravel-mysql-encrypt/blob/master/LICENSE) for more information.
