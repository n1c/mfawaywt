<?php

namespace App\Models;

use Auth;
use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Parsedown;
use PDOException;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'post_id',
        'reddit_id',
        'score',
        'body_md',
        'is_enabled',
        'created_at',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public static function findByRedditId($redditId)
    {
        return $redditId ? self::where('reddit_id', $redditId)->first() : null;
    }

    public static function findByRedditIdOrFail($redditId)
    {
        $comment = self::findByRedditId($redditId);
        if ($comment) {
            return $comment;
        } else {
            throw (new ModelNotFoundException)->setModel(self::class);
        }
    }

    public static function filter(Carbon $start, Carbon $end, $limit = 200, $voteLimit = 50)
    {
        $key = sprintf(
            '%s::filter:start%s:end%s:limit%u:voteLimit%u',
            self::class,
            $start->timestamp,
            $end->timestamp,
            $limit,
            $voteLimit
        );

        return Cache::remember($key, 15, function () use ($start, $end, $limit, $voteLimit) {
            return self::full()
                ->where('is_enabled', true)
                ->where('created_at', '>', $start)
                ->where('created_at', '<', $end)
                ->where('score', '>=', $voteLimit)
                ->orderBy('score', 'DESC')
                ->limit($limit)
                ->get()
                ;
        });
    }

    public function getBodyHtmlAttribute()
    {
        $parsedown = new Parsedown;
        return $parsedown->text($this->body_md);
    }

    public function inflate()
    {
        return Cache::remember(sprintf("%s::inflate:%s", self::class, $this->id), 60, function () {
            $this->user;
            $this->post;
            $this->images;
            return $this;
        });
    }

    public function belongsToCurrentUser()
    {
        return $this->user == Auth::user();
    }

    public function enable()
    {
        $this->update([
            'is_enabled' => true,
        ]);

        return $this;
    }

    public function disable()
    {
        $this->update([
            'is_enabled' => false,
        ]);

        return $this;
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeFull($query)
    {
        return $query
            ->with('user')
            ->with('post')
            ->with([ 'images' => function ($q) {
                $q->orderBy('id', 'ASC');
            }])
            ;
    }

    public function firstImage()
    {
        return $this->images->first() ?: new Image();
    }

    public function images()
    {
        return $this->belongsToMany(Image::class)->orderBy('id', 'ASC');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /*
     * Take a comment and try find all it's images.
     */
    /* Should be called: parse for images.
    should return an array of image objects from all the urls
    possibly some unsaved which we must add the comment id and save.
     - first priority is find guids from url, and if they exist in the db already
    */
    public function parseForImages()
    {
        $results = [];
        $urls = links_from_md($this->body_md);
        foreach ($urls as $url) {
            $results = array_merge($results, Image::parseUrl($url));
        }

        // Skip nulls, assoc & save if new!
        $images = [];
        foreach ($results as $image) {
            if (!$image) {
                continue;
            }

            try {
                $this->images()->save($image);
            } catch (PDOException $e) {
                // Already exists.
            }

            $images[] = $image;
        }

        return $images;
    }
}
