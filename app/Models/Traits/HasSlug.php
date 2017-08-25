<?php

namespace App\Models\Traits;

trait HasSlug
{
    use FindByField;

    public static function findBySlug($slug)
    {
        return self::findByField('slug', $slug);
    }

    public static function findBySlugOrFail($slug)
    {
        return self::findByFieldOrFail('slug', $slug);
    }
}
