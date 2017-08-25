<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

class ImgurCache extends Model
{
    protected $fillable = [
        'endpoint',
        'response',
    ];

    protected $casts = [
        'response' => 'object',
    ];

    public static function forEndpoint($endpoint)
    {
        return Cache::remember(sprintf("%s::forEndpoint:%s", self::class, $endpoint), 60, function () use ($endpoint) {
            return self::where('endpoint', $endpoint)->first();
        });
    }

    public static function set($endpoint, $data)
    {
        self::updateOrCreate([
            'endpoint' => $endpoint,
        ], [
            'response' => $data,
        ]);
    }
}
