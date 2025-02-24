<?php

namespace TapanDerasari\MysqlEncrypt\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class DecryptSelectScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $columns = $builder->getQuery()->columns;

        $encryptable = $model->encryptable();

        if (empty($columns) || $columns === ['*'] || $columns === '*') {
            $columns = Schema::getColumnListing($model->getTable());
        }

        $select = collect($columns)->map(function ($column) use ($encryptable) {
            return (in_array($column, $encryptable)) ? db_decrypt($column) : $column;
        });

        return $builder->select(...$select);
    }
}
