<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;

class Subreddit extends Model
{
    use HasSlug;

    protected $fillable = [
        'slug',
        'name',
        'author',
        'search',
    ];

    public function getSearchString(): string
    {
        return sprintf('subreddit:%s author:%s %s', $this->slug, $this->author, $this->search);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
