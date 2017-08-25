<?php

namespace App\Models;

use App\Jobs\ParseComment;
use App\Libs\Reddit\API as RedditAPI;
use Cache;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Log;

class Post extends Model
{
    use DispatchesJobs;

    protected $fillable = [
        'subreddit_id',
        'reddit_id',
        'title',
        'url',
        'score',
        'body_md',
        'created_at',
    ];

    public static function findByRedditId($redditId)
    {
        return $redditId ? self::where('reddit_id', $redditId)->first() : null;
    }

    public static function findByRedditIdOrFail($redditId)
    {
        $post = self::findByRedditId($redditId);
        if ($post) {
            return $post;
        } else {
            throw (new ModelNotFoundException)->setModel(self::class);
        }
    }

    public static function findFullByRedditIdOrFail($redditId)
    {
        return Cache::remember(
            sprintf("%s::findFullByRedditIdOrFail:%s", self::class, $redditId),
            60,
            function () use ($redditId) {
                $post = self::findByRedditIdOrFail($redditId);
                $post->comments; // preload 'em

                return $post;
            }
        );
    }

    public function subreddit()
    {
        return $this->belongsTo(Subreddit::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->full()
            ->enabled()
            ->orderBy('score', 'DESC')
            ;
    }

    public function poll()
    {
        $data = RedditAPI::getPost($this->subreddit->slug, $this->reddit_id);
        if (isset($data->error)) {
            Log::debug(sprintf('{%s} deleting [%s]! Poll returned error: %s', self::class, $this->reddit_id, $data->error));
            $this->delete();
            return;
        }

        try {
            $postData = $data[0]->data->children[0]->data;
            $comments = $data[1]->data->children;
        } catch (Exception $e) {
            Log::debug(sprintf('{%s} getPost results look bad: %s', self::class, $e->getMessage()));
            return;
        }

        $this->update([
            'score' => $postData->score,
            'body_md' => $postData->selftext,
        ]);

        foreach ($comments as $c) {
            $new = false;
            if ($c->kind != RedditAPI::TYPE_COMMENT) {
                continue;
            }

            $c = $c->data;
            $comment = Comment::firstOrNew([
                'reddit_id' => $c->id,
            ]);

            // If new
            if (!$comment->id) {
                $new = true;
                $user = User::firstOrCreate([ 'name' => $c->author ]);
                $comment->user_id = $user->id;
                $comment->post_id = $this->id;
            }

            $comment->score = $c->score;
            $comment->body_md = $c->body;
            $comment->created_at = Carbon::createFromTimestampUTC($c->created_utc);
            $comment->save();

            if ($new) {
                dispatch(new ParseComment($comment));
            }
        }

        return $this;
    }
}
