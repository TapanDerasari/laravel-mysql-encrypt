<?php

namespace TapanDerasari\MysqlEncrypt\Traits;

use TapanDerasari\MysqlEncrypt\Scopes\DecryptSelectScope;

trait Encryptable
{
    /**
     * @return void
     */
    public static function bootEncryptable()
    {
        static::addGlobalScope(new DecryptSelectScope);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (is_null($value) || !in_array($key, $this->encryptable)) {
            return parent::setAttribute($key, $value);
        }

        return parent::setAttribute($key, db_encrypt($value));
    }

    /**
     * @return array
     */
    public function encryptable(): array
    {
        return $this->encryptable ?? [];
    }

    /**
     * where for encrypted columns
     *
     * @param $query
     * @param $column
     * @param $value
     *
     * @return mixed
     */
    public function scopeWhereEncrypted($query, $column, $value)
    {
        /** @var Builder $query */
        return $query->whereRaw(db_decrypt_string($column, $value));
    }

    /**
     * where not for encrypted columns
     *
     * @param $query
     * @param $column
     * @param $value
     *
     * @return mixed
     */
    public function scopeWhereNotEncrypted($query, $column, $value)
    {
        /** @var Builder $query */
        return $query->whereRaw(db_decrypt_string($column, $value, 'NOT LIKE'));
    }

    /**
     * orWhere for encrypted columns
     *
     * @param $query
     * @param $column
     * @param $value
     *
     * @return mixed
     */
    public function scopeOrWhereEncrypted($query, $column, $value)
    {
        /** @var Builder $query */
        return $query->orWhereRaw(db_decrypt_string($column, $value));
    }

    /**
     * orWhere not for encrypted columns
     *
     * @param $query
     * @param $column
     * @param $value
     *
     * @return mixed
     */
    public function scopeOrWhereNotEncrypted($query, $column, $value)
    {
        /** @var Builder $query */
        return $query->orWhereRaw(db_decrypt_string($column, $value, 'NOT LIKE'));
    }

    /**
     * orderBy for encrypted columns
     *
     * @param $query
     * @param $column
     * @param $direction
     *
     * @return mixed
     */
    public function scopeOrderByEncrypted($query, $column, $direction)
    {
        /** @var Builder $query */
        return $query->orderByRaw(db_decrypt_string($column, $direction, ''));
    }


    /**
     * where for encrypted columns like
     *
     * @param $query
     * @param $column
     * @param $value
     *
     * @return mixed
     */
    public function scopeWhereEncryptedLike($query, $column, $value)
    {
        /** @var Builder $query */
        return $query->whereRaw(db_decrypt_string_like($column, $value));
    }


    /**
     * orWhere not for encrypted columns like
     *
     * @param $query
     * @param $column
     * @param $value
     *
     * @return mixed
     */
    public function scopeOrWhereEncryptedLike($query, $column, $value)
    {
        /** @var Builder $query */
        return $query->orWhereRaw(db_decrypt_string_like($column, $value));
    }

    /**
     * orderBy for encrypted columns
     *
     * @param $query
     * @param $column
     * @param $direction
     *
     * @return mixed
     */
    public function scopeOrderByEncryptedSort($query, $column, $direction)
    {
        /** @var Builder $query */
        return $query->orderByRaw(db_decrypt_string_sort($column, $direction));
    }
}
