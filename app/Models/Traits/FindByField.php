<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait FindByField
{
    public static function findByField($field, $value)
    {
        return self::where($field, $value)->first();
    }

    public static function findByFieldOrFail($field, $value)
    {
        $item = self::findByField($field, $value);
        if ($item) {
            return $item;
        } else {
            throw (new ModelNotFoundException)->setModel(static::class);
        }
    }
}
