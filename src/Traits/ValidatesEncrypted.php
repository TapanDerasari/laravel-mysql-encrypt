<?php

namespace TapanDerasari\MysqlEncrypt\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use PDOException;


trait ValidatesEncrypted
{
    /**
     * Validators.
     *
     * @return void
     */
    public function addValidators()
    {
        Validator::extend('unique_encrypted', function ($attribute, $value, array $parameters) {

            $this->requireParameterCount(1, $parameters, 'unique_encrypted');

            $this->requireTableExists($parameters[0]);

            $field = isset($parameters[1]) ? $parameters[1] : $attribute;
            $ignore = isset($parameters[2]) ? $parameters[2] : null;

            $key = DB::getPdo()->quote(config('mysql-encrypt.key'));
            $bindings = [$value];
            $sql = "SELECT count(*) as aggregate FROM `{$parameters[0]}` WHERE AES_DECRYPT(`{$field}`, {$key}) = ? COLLATE utf8mb4_general_ci";

            if ($ignore) {
                $sql .= " AND id != ?";
                $bindings[] = $ignore;
            }

            $items = DB::select($sql, $bindings);

            return $items[0]->aggregate == 0;
        });

        Validator::extend('exists_encrypted', function ($attribute, $value, array $parameters) {

            $this->requireParameterCount(1, $parameters, 'exists_encrypted');

            $this->requireTableExists($parameters[0]);

            $field = isset($parameters[1]) ? $parameters[1] : $attribute;

            $key = DB::getPdo()->quote(config('mysql-encrypt.key'));
            $items = DB::select(
                "SELECT count(*) as aggregate FROM `{$parameters[0]}` WHERE AES_DECRYPT(`{$field}`, {$key}) = ? COLLATE utf8mb4_general_ci",
                [$value]
            );

            return $items[0]->aggregate > 0;
        });
    }

    /**
     * Require a certain number of parameters to be present.
     *
     * @param int $count
     * @param array $parameters
     * @param string $rule
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function requireParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }

    /**
     * The table must exist.
     *
     * @param string $table
     * @return void
     *
     * @throws PDOException
     */
    public function requireTableExists($table)
    {
        if (!Schema::hasTable($table)) {
            throw new PDOException("Table $table not found.");
        }
    }
}
