<?php

namespace App\Models;

use App\Libs\Imgur\API as ImgurAPI;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    const PROVIDER_IMGUR = 'imgur';
    const PROVIDER_DRESSEDSO = 'dressedso';
    const PROVIDER_OTHER = 'other';

    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_NORMAL = 'normal';

    protected $fillable = [ 'provider', 'guid', 'src', ];
    protected $appends = [ 'srcSmall', 'srcMedium', 'srcNormal', ];

    public static function urlIsDressedso($url)
    {
        return str_contains($url, 'dressed.so');
    }

    public static function urlIsImgur($url)
    {
        return str_contains($url, 'imgur.com');
    }

    public static function parseDressedsoUrl($url)
    {
        if (starts_with($url, 'http://cdn.dressed.so')) {
            return self::parseDressedsoCDNUrl($url);
        } elseif (starts_with($url, 'http://dressed.so/post/view/')) {
            return self::parseDressedSoLink($url);
        } else {
            return [];
        }
    }

    public static function parseDressedsoCDNUrl($url)
    {
        $guid = substr($url, 24, -5);
        return [
            Image::updateOrCreate([
                'guid' => $guid,
            ], [
                'provider' => Image::PROVIDER_DRESSEDSO,
                'src' => $url,
            ])
        ];
    }

    public static function parseDressedSoLink($url)
    {
        $guid = substr($url, 28);
        return [
            Image::updateOrCreate([
                'guid' => $guid,
            ], [
                'provider' => Image::PROVIDER_DRESSEDSO,
                'src' => 'http://cdn.dressed.so/i/' . $guid . 'l.jpg',
            ])
        ];
    }

    public static function parseImgurUrl($url)
    {
        $images = [];
        $url = substr($url, strpos($url, '.com') + 5);
        if (!$url) {
            return [];
        } elseif (starts_with($url, 'a/')) {
            $guid = substr($url, 2);
            $album = ImgurAPI::getAlbum($guid);
            if ($album && isset($album->images) && count($album->images)) {
                foreach ($album->images as $ai) {
                    $images[] = self::imgurDataFactory($ai);
                }
            }
        } elseif (starts_with($url, 'gallery/')) {
            $guid = substr($url, 8);

            if (str_contains($guid, '/')) {
                $guid = substr($guid, 0, strpos($guid, '/'));
            }

            $gallery = ImgurAPI::getGallery($guid);
            if ($gallery && isset($gallery->images) && count($gallery->images)) {
                foreach ($gallery->images as $gi) {
                    $images[] = self::imgurDataFactory($gi);
                }
            }
        } else {
            $guids = explode(',', $url);
            foreach ($guids as $guid) {
                if (str_contains($guid, '.')) {
                    $guid = substr($guid, 0, strpos($guid, '.'));
                }

                $image = self::where([ 'provider' => Image::PROVIDER_IMGUR, 'guid' => $guid ])->first();
                $images[] = $image ?: self::imgurDataFactory(ImgurAPI::getImage($guid));
            }
        }

        return $images;
    }

    public static function imgurDataFactory($data)
    {
        if (!isset($data->id) || !isset($data->link)) {
            return null;
        }

        return Image::updateOrCreate([
            'guid' => $data->id,
        ], [
            'provider' => Image::PROVIDER_IMGUR,
            'src' => $data->link,
        ]);
    }

    public static function parseOther($url)
    {
        $images = [];
        if (ends_with($url, [ '.jpg', '.png', ])) {
            $images[] = Image::updateOrCreate([
                'guid' => md5($url),
            ], [
                'provider' => Image::PROVIDER_OTHER,
                'src' => $url,
            ]);
        }

        return $images;
    }

    /*
     * @return array of Images (saved or unsaved)
     */
    public static function parseUrl($url)
    {
        $images = [];
        if (self::urlIsDressedso($url)) {
            $images = self::parseDressedsoUrl($url);
        } elseif (self::urlIsImgur($url)) {
            $images = self::parseImgurUrl($url);
        } else {
            $images = self::parseOther($url);
        }

        return $images;
    }

    public function getSrcSmallAttribute()
    {
        return $this->getSrc(Image::SIZE_SMALL);
    }

    public function getSrcMediumAttribute()
    {
        return $this->getSrc(Image::SIZE_MEDIUM);
    }

    public function getSrcNormalAttribute()
    {
        return $this->getSrc(Image::SIZE_NORMAL);
    }

    public function getSrc($size = null)
    {
        if (!$size) {
            return $this->src;
        }

        if (!in_array($size, [ Image::SIZE_SMALL, Image::SIZE_MEDIUM, Image::SIZE_NORMAL, ])) {
            throw new Exception('Invalid getSrc size param!');
        }

        $sizeModifiers = [
            Image::PROVIDER_DRESSEDSO => [
                Image::SIZE_SMALL => 's', // 50w
                Image::SIZE_MEDIUM => 'c', // 220w
                Image::SIZE_NORMAL => 'm', // 400w

            ],
            Image::PROVIDER_IMGUR => [
               Image::SIZE_SMALL => 't', // 160w
               Image::SIZE_MEDIUM => 'm', // 320w
               Image::SIZE_NORMAL => 'l', // 640w
            ],
        ];

        switch ($this->provider) {
            case Image::PROVIDER_DRESSEDSO:
                return '//cdn.dressed.so/i/' . $this->guid . $sizeModifiers[Image::PROVIDER_DRESSEDSO][$size] . '.jpg';
            case Image::PROVIDER_IMGUR:
                return '//i.imgur.com/' . $this->guid . $sizeModifiers[Image::PROVIDER_IMGUR][$size] . '.jpg';
            default:
                return $this->src;
        }
    }

    public function comments()
    {
        return $this->belongsToMany(Comment::class);
    }
}
