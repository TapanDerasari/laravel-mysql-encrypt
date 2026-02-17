<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('db_encrypt')) {
    /**
     * Encrypt value.
     *
     * @param mixed $value
     * @return \Illuminate\Database\Query\Expression
     */
    function db_encrypt($value)
    {
        $key = config('mysql-encrypt.key');
        $quotedKey = DB::getPdo()->quote($key);

        if (is_null($value)) {
            return DB::raw("AES_ENCRYPT(NULL, {$quotedKey})");
        }

        $quotedValue = DB::getPdo()->quote($value);

        return DB::raw("AES_ENCRYPT({$quotedValue}, {$quotedKey})");
    }
}


if (!function_exists('db_decrypt')) {
    /**
     * Decrpyt value.
     *
     * @param mixed $column
     * @return \Illuminate\Database\Query\Expression
     */
    function db_decrypt($column)
    {
        $key = DB::getPdo()->quote(config('mysql-encrypt.key'));

        return DB::raw("AES_DECRYPT({$column}, {$key}) AS `{$column}`");
    }
}


if (!function_exists('db_decrypt_string')) {
    /**
     * Decrpyt value for WHERE clauses with parameterized binding.
     *
     * @param string $column
     * @param string $value
     * @param string $operator
     * @return array [$sql, $bindings]
     */
    function db_decrypt_string($column, $value, $operator = '=')
    {
        $key = DB::getPdo()->quote(config('mysql-encrypt.key'));
        $sql = "AES_DECRYPT({$column}, {$key}) {$operator} ? COLLATE utf8mb4_general_ci";
        return [$sql, [$value]];
    }
}


if (!function_exists('db_decrypt_string_like')) {
    /**
     * Decrpyt value for LIKE searches with parameterized binding.
     *
     * @param string $column
     * @param string $value
     * @param string $operator
     * @return array [$sql, $bindings]
     */
    function db_decrypt_string_like($column, $value, $operator = 'LIKE')
    {
        $key = DB::getPdo()->quote(config('mysql-encrypt.key'));
        $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $value);
        $sql = "AES_DECRYPT({$column}, {$key}) {$operator} ? COLLATE utf8mb4_general_ci";
        return [$sql, ["%{$escaped}%"]];
    }
}

if (!function_exists('db_decrypt_string_sort')) {
    /**
     * Decrpyt value for ORDER BY clauses.
     *
     * @param string $column
     * @param string $direction
     * @return string
     */
    function db_decrypt_string_sort($column, $direction)
    {
        $key = DB::getPdo()->quote(config('mysql-encrypt.key'));
        return "AES_DECRYPT({$column}, {$key}) {$direction}";
    }
}
