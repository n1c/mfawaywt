<?php

namespace Tests\Unit;

use App\Models\Image;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CommentParserTest extends TestCase
{
    use DatabaseMigrations;

    public function testBasicImgur()
    {
        $results = Image::parseUrl('http://i.imgur.com/fpvAapE.jpg');
        $this->assertEquals(count($results), 1);
        $this->isInstanceOf($results[0], Image::class);
        $this->assertEquals($results[0]->src, 'http://i.imgur.com/fpvAapE.jpg');
    }

    public function testImgurLink()
    {
        $results = Image::parseUrl('http://imgur.com/u3LZfIN');
        $this->assertEquals(count($results), 1);
        $this->isInstanceOf($results[0], Image::class);
        $this->assertEquals($results[0]->src, 'http://i.imgur.com/u3LZfIN.jpg');
    }

    public function testImgurNotFound()
    {
        $this->setExpectedException('App\Libs\Imgur\ImgurNotFoundException');
        $results = Image::parseUrl('http://i.imgur.com/BCvPe');
        $this->assertEquals(count($results), 0);
    }

    public function testDsoCDN()
    {
        $results = Image::parseUrl('http://cdn.dressed.so/i/5597317cb87ddl.jpg');
        $this->assertEquals(count($results), 1);
        $this->isInstanceOf($results[0], Image::class);
        $this->assertEquals($results[0]->src, 'http://cdn.dressed.so/i/5597317cb87ddl.jpg');
    }

    public function testDsoLink()
    {
        $results = Image::parseUrl('http://dressed.so/post/view/5165a3a68f1eb');
        $this->assertEquals(count($results), 1);
        $this->isInstanceOf($results[0], Image::class);
        $this->assertEquals($results[0]->src, 'http://cdn.dressed.so/i/5165a3a68f1ebl.jpg');
    }

    /* @TODO
    'http://www.imgur.com/gallery/n3SlXJq'
    'http://imgur.com/gallery/tnpfo/new'
    'http://imgur.com/a/4847m'
    'http://imgur.com/7IqfH9N,jv8T4vC,RINN1QJ,bxkzfOZ#3'
    'http://imgur.com/y9QB34A&amp;viUEq2C'

    'http://casualism.pl/wp-content/uploads/2015/06/17.jpg'
    'Remember guys, upvote this thread!! We need to get this to /r/all, for research purposes!'
    */
}
