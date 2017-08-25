<?php

namespace App\Models;

use Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $dates = [
        'created_at',
        'updated_at',
        'token_expires_at',
    ];

    protected $fillable = [
        'name',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $hidden = [
        'remember_token',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    public static function findByName($name)
    {
        return $name ? self::where('name', $name)->first() : null;
    }

    public static function findByNameOrFail($name)
    {
        $user = self::findByName($name);
        if ($user) {
            return $user;
        } else {
            throw (new ModelNotFoundException)->setModel(self::class);
        }
    }

    public static function findFullByNameOrFail($name)
    {
        return Cache::remember(
            sprintf("%s::findFullByNameOrFail:%s", self::class, $name),
            60,
            function () use ($name) {
                $user = self::findByNameOrFail($name);
                $user->load([ 'comments' ]);

                return $user;
            }
        );
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->where('is_enabled', true)
            ->orderBy('created_at', 'DESC')
            ->with('post')->full()
            ;
    }
}
