# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [v1.0.9] - 2026-02-17

### Fixed
- `$model->value` now returns a plain string immediately after `create()` or `save()` without requiring a DB re-fetch. Previously the in-memory model held a `DB::raw()` `Expression` object instead of the decrypted string value.

### Added
- `getAttribute()` override in `Encryptable` trait to intercept `Expression` objects and return the original plain-text value
- `$plainEncryptable` property on the trait to track original values during `setAttribute()`
- Encrypt/decrypt round-trip tests for all special character scenarios (single quote, double quote, backslash, wildcards, emoji, caret, diacritics)
- Regression test ensuring encrypted attributes are always strings after `create()`

---

## [v1.0.8] - 2026-02-17

### Fixed
- Values containing special characters (`'`, `"`, `\`, `%`, `_`, multibyte/emoji) no longer break queries or cause SQL errors
- Eliminated SQL injection risks in all helper functions and query scopes by replacing string interpolation with PDO parameterized bindings (`DB::getPdo()->quote()` and `?` placeholders)
- `db_decrypt()` column alias now uses backticks instead of single quotes
- `scopeOrderByEncrypted` was misusing `db_decrypt_string()` for ORDER BY; now correctly uses `db_decrypt_string_sort()`
- `scopeWhereNotEncrypted` and `scopeOrWhereNotEncrypted` changed from `NOT LIKE` to `!=` for correct exact-match semantics
- Validators (`unique_encrypted`, `exists_encrypted`) switched from `LIKE` to `=` for exact matching

### Changed
- `db_decrypt_string()` default operator changed from `LIKE` to `=`
- `db_decrypt_string()` now returns `[$sql, $bindings]` array instead of a raw SQL string
- `db_decrypt_string_like()` now returns `[$sql, $bindings]` array instead of a raw SQL string; escapes `%` and `_` in search terms to prevent wildcard leakage
- Removed `addslashes()` from `db_encrypt()`

### Added
- Test cases for single quote, double quote, backslash, LIKE wildcard, emoji/multibyte, caret, and diacritic values

---

## [v1.0.7] - 2025-02-27

### Added
- Laravel 12.x support

---

## [v1.0.6] - 2025-01-21

### Added
- Support for `select(*)` queries
- Updated Laravel dependency constraints to include latest versions

---

## [v1.0.5] - 2024-05-29

### Fixed
- Wrong encryption key being used in `unique_encrypted` and `exists_encrypted` validators

---

## [v1.0.4] - 2024-03-08

### Added
- Laravel 11.x support (`VARBINARY` column handling)
- Updated test cases

---

## [v1.0.3] - 2024-01-08

### Fixed
- Restricted wildcard column fetching to prevent unintended columns being selected in JOIN queries

---

## [v1.0.2] - 2023-12-27

### Added
- `scopeOrderByEncryptedSort` scope for sorting by encrypted columns

---

## [v1.0.1] - 2023-12-13

### Added
- `whereEncryptedLike` and `orWhereEncryptedLike` scopes for partial LIKE searches on encrypted columns
- Search and sort filter functionality

---

## [1.0.0] - 2023-12-06

### Added
- Initial release
- AES encryption/decryption via MySQL `AES_ENCRYPT` / `AES_DECRYPT`
- `Encryptable` trait with `setAttribute` override and `DecryptSelectScope`
- `whereEncrypted`, `whereNotEncrypted`, `orWhereEncrypted`, `orWhereNotEncrypted`, `orderByEncrypted` scopes
- `ValidatesEncrypted` trait with `unique_encrypted` and `exists_encrypted` validators
- Laravel 8, 9, 10 support
- Nullable value support in `db_encrypt()`
