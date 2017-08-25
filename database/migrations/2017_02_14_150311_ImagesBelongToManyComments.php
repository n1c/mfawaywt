<?php

use App\Models\Image;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImagesBelongToManyComments extends Migration
{
    public function up()
    {
        Schema::create('comment_image', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('comment_id')->unsigned()->index();
            $table->foreign('comment_id')->references('id')->on('comments');

            $table->integer('image_id')->unsigned()->index();
            $table->foreign('image_id')->references('id')->on('images');

            $table->unique([ 'comment_id', 'image_id', ]);
        });

        // Seed all our existing relationships.
        $hasMore = true;
        $lastID = 0;
        do {
            Log::debug('New Loop lastID: ' . $lastID);
            $images = Image::limit(100)
                ->orderBy('id', 'ASC')
                ->where('id', '>', $lastID)
                ->get()
                ;

            if (!$images->count()) {
                $hasMore = false;
            }

            $images->each(function ($i) use (&$lastID) {
                $lastID = $i->id;
                Log::debug('lastID: ' . $lastID);

                DB::table('comment_image')->insert([
                    'comment_id' => $i->comment_id,
                    'image_id' => $i->id,
                ]);
            });
        } while ($hasMore);

        Log::debug('Do While finished!');

        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign([ 'comment_id' ]);
            $table->dropColumn('comment_id');
        });
    }

    public function down()
    {
        // YOLO
    }
}
