# Laravel 13 Support — Design Document

## Goal

Release a new major version (v3.0) of `tapanderasari/laravel-mysql-encrypt` that supports Laravel 13. The existing version continues to support Laravel 10/11/12.

## Approach: Minimal Bump (Approach A)

Laravel 13 introduces minimal breaking changes. The core APIs this package uses (`DB::raw`, `whereRaw`, `Expression`, global scopes, `setAttribute`/`getAttribute`, `Schema::getColumnListing`, `Validator::extend`) are all unchanged. Only dependency versions and dead code need updating.

## Changes

### 1. `composer.json`
- `php`: `^8.0` -> `^8.3` (Laravel 13 requires PHP 8.3+)
- `illuminate/database`: `^10.0|^11.0|^12.0` -> `^13.0`
- `illuminate/support`: `^10.0|^11.0|^12.0` -> `^13.0`
- `orchestra/testbench`: `^8.0|^9.0|^10.0` -> `^11.0`
- `pestphp/pest`: `^2.0|^3.0` -> `^4.0`
- `pestphp/pest-plugin-laravel`: `^2.0|^3.0` -> `^3.0|^4.0`

### 2. Delete `src/Providers/LumenServiceProvider.php`
Lumen has been EOL since Laravel 10. No longer relevant for a Laravel 13-only package.

### 3. Update `phpunit.xml`
Update XSD schema reference to PHPUnit 12.x.

### 4. Update `CHANGELOG.md`
Add v3.0 entry.

## No Changes Required
- `src/Traits/Encryptable.php`
- `src/helpers.php`
- `src/Scopes/DecryptSelectScope.php`
- `src/Traits/ValidatesEncrypted.php`
- `src/Providers/LaravelServiceProvider.php`
- Test logic (only test infrastructure versions change)
