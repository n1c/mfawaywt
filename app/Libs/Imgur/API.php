<?php

namespace App\Libs\Imgur;

use App\Models\ImgurCache;
use Cache;
use GuzzleHttp\Exception\ClientException;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Log;

class API
{
    private static $rlkey = self::class . '::x-ratelimit-clientremaining';
    private static $ulkey = self::class . '::x-ratelimit-userremaining';
    private static $rlimit = null;
    private static $ulimit = null;

    public static function get(string $endpoint, bool $skipCache = false, bool $checkLimits = true)
    {
        if (!$skipCache) {
            $cached = ImgurCache::forEndpoint($endpoint);
            if ($cached) {
                return $cached->response->data;
            }
        }

        if ($checkLimits) {
            self::checkLimits();
        }

        Log::debug(sprintf('{%s} GET %s [ul:%s,rl:%s]', self::class, $endpoint, self::$ulimit, self::$rlimit));

        try {
            $http = new GuzzleClient();
            $responseRaw = $http->request('GET', 'https://api.imgur.com/3' . $endpoint, [
                'headers' => [
                    'User-Agent' => config('app.name'),
                    'Accept' => 'application/json',
                    'Authorization' => 'Client-ID ' . config('services.imgur.client_id'),
                ],
            ]);
        } catch (ClientException $e) {
            $responseError = $e->getResponse();
            // Handle 403
            if ($responseError->getStatusCode() == 404) {
                throw new ImgurNotFoundException($endpoint, 404);
            }

            Log::error(sprintf(
                '{%s::Exception} failed to get [%s]: %s',
                self::class,
                $endpoint,
                $e->getMessage()
            ));
            return;
        }

        if (is_int($responseRaw->getHeader('X-RateLimit-ClientRemaining'))
            && is_int($responseRaw->getHeader('X-RateLimit-UserRemaining'))
        ) {
            self::setLimits(
                $responseRaw->getHeader('X-RateLimit-ClientRemaining'),
                $responseRaw->getHeader('X-RateLimit-UserRemaining')
            );
        }

        $data = json_decode((string) $responseRaw->getBody());
        if (!$data) {
            throw new Exception(sprintf('%s failed to get', self::class, $endpoint));
        }

        ImgurCache::set($endpoint, $data);
        return $data->data;
    }

    public static function checkLimits()
    {
        if (rand(0, 25) == 0) {
            self::getCredits();
        }

        self::$rlimit = Cache::get(self::$rlkey);
        self::$ulimit = Cache::get(self::$ulkey);

        // @TODO: Consider doing a checkCredits lookup here on miss?

        if (is_int(self::$rlimit) && self::$rlimit <= config('services.imgur.low-credits')) {
            throw new Exception(sprintf('{%s} client ratelimit is low! %s', self::class, self::$rlimit));
        }

        if (is_int(self::$ulimit) && self::$ulimit <= config('services.imgur.low-credits')) {
            throw new Exception(sprintf('{%s} user ratelimit is low! %s', self::class, self::$ulimit));
        }
    }

    public static function setLimits(int $client, int $user)
    {
        Cache::put(self::$rlkey, $client, config('services.imgur.low-credits-cooldown'));
        Cache::put(self::$ulkey, $user, config('services.imgur.low-credits-cooldown'));
    }

    public static function getCredits()
    {
        $credits = self::get('/credits', true, false);
        if ($credits) {
            self::setLimits((int) $credits->ClientRemaining, (int) $credits->UserRemaining);
        }
        return $credits;
    }

    // https://api.imgur.com/endpoints/album
    public static function getAlbum($id)
    {
        return Cache::remember(sprintf("%s::getAlbum:%s", self::class, $id), 60, function () use ($id) {
            return self::get(sprintf('/album/%s', $id));
        });
    }

    // http://api.imgur.com/endpoints/gallery
    public static function getGallery($id)
    {
        return Cache::remember(sprintf("%s::getGallery:%s", self::class, $id), 60, function () use ($id) {
            return self::get(sprintf('/gallery/%s', $id));
        });
    }

    // https://api.imgur.com/endpoints/image
    public static function getImage($id)
    {
        return Cache::remember(sprintf("%s::getImage:%s", self::class, $id), 60, function () use ($id) {
            return self::get(sprintf('/image/%s', $id));
        });
    }
}
