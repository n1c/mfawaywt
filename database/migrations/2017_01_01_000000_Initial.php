<?php

use App\Models\Image;
use App\Models\Subreddit;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Initial extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue');
            $table->longText('payload');
            $table->tinyInteger('attempts')->unsigned();
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');

            $table->index([ 'queue', 'reserved_at', ]);
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('imgur_caches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('endpoint')->unique();
            $table->text('response');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable()->default(null);
            $table->boolean('is_admin')->default(false);
            $table->timestamp('token_expires_at')->useCurrent();

            $table->timestamps();
        });

        Schema::create('subreddits', function (Blueprint $table) {
            $table->increments('id');

            $table->string('slug')->unique();
            $table->string('name');
            $table->string('author');
            $table->string('search');

            $table->timestamps();
        });

        collect([
            [
                'slug' => 'malefashionadvice',
                'name' => 'MaleFashionAdvice',
                'author' => 'AutoModerator',
                'search' => 'waywt',
            ],
            [
                'slug' => 'streetwear',
                'name' => 'Streetwear',
                'author' => 'AutoModerator',
                'search' => 'wdywt',
            ],
            [
                'slug' => 'TeenFA',
                'name' => 'Teen Fashion Advice',
                'author' => 'AutoModerator',
                'search' => 'waywt',
            ],
        ])->each(function ($i) {
            Subreddit::create($i);
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('subreddit_id')->unsigned();
            $table->foreign('subreddit_id')->references('id')->on('subreddits');

            $table->string('reddit_id')->unique();
            $table->string('title');
            $table->string('url')->unique();
            $table->integer('score');
            $table->text('body_md');

            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('posts');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('reddit_id')->unique();

            $table->integer('score');
            $table->text('body_md');
            $table->boolean('is_enabled')->default(true);

            $table->timestamps();
        });

        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('comment_id')->unsigned();
            $table->foreign('comment_id')->references('id')->on('comments');

            $table->enum('provider', [ Image::PROVIDER_DRESSEDSO, Image::PROVIDER_IMGUR, Image::PROVIDER_OTHER, ]);
            $table->string('guid')->unique();
            $table->string('src');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('imgur_caches');
        Schema::dropIfExists('images');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('subreddits');
        Schema::dropIfExists('users');
    }
}
