<?php

namespace App\Libs\Reddit;

use Cache;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Log;

class API extends Base
{
    public static function get($endpoint)
    {
        Log::debug(sprintf('{%s} GET %s', self::class, $endpoint));
        try {
            $http = new GuzzleClient();
            $responseRaw = $http->request('GET', 'http://reddit.com' . $endpoint, [
                'headers' => [
                    'User-Agent' => config('app.name'), // app.url?
                ],
                'timeout' => 20,
            ]);
        } catch (Exception $e) {
            Log::error(sprintf(
                '{%s::Requests_Exception} failed to get [%s]: %s',
                self::class,
                $endpoint,
                $e->getMessage()
            ));
            return;
        }

        $response = json_decode((string) $responseRaw->getBody());
        if (!$response) {
            throw new Exception(sprintf('%s failed to get', self::class, $endpoint));
        }

        return $response;
    }

    // https://www.reddit.com/r/malefashionadvice/comments/3c06d0.json
    public static function getPost(string $subreddit, string $redditId)
    {
        $key = sprintf("%s::getPost:%s", self::class, $redditId);
        return Cache::remember($key, 0.8, function () use ($subreddit, $redditId) {
            return self::get(sprintf('/r/%s/comments/%s.json', $subreddit, $redditId));
        });
    }
}
