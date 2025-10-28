<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ExcludeSoftDeletedScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Apply only if table has deleted_at column
        if (in_array('deleted_at', $model->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($model->getTable()))) {
            $builder->whereNull($model->getTable() . '.deleted_at');
        }
    }
}
