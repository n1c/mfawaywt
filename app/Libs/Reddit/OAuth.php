<?php

namespace App\Libs\Reddit;

use App\Models\User;
use Exception;
use GuzzleHttp\Client as GuzzleClient;

class OAuth
{
    private $accessToken;

    public static function getAuthUrl()
    {
        $params = [
            'client_id' => config('services.reddit.client_id'),
            'state' => uniqid(),
            'response_type' => 'code',
            'redirect_uri' => route('connect'),
            'duration' => 'permanent',
            'scope' => 'identity',
        ];

        return 'https://www.reddit.com/api/v1/authorize?' . http_build_query($params);
    }

    public static function authWithCode($code)
    {
        $http = new GuzzleClient();
        $responseRaw = $http->post('https://www.reddit.com/api/v1/access_token', [
            'headers' => [
                'User-Agent' => config('app.name'),
            ],
            'form_params' => [
                'client_id' => config('services.reddit.client_id'),
                'redirect_uri' => route('connect'),
                'code' => $code,
                'grant_type' => 'authorization_code',
            ],
            'auth' => [
                config('services.reddit.client_id'),
                config('services.reddit.secret')
            ],
        ]);

        $response = json_decode((string) $responseRaw->getBody());

        if (!$response) {
            throw new Exception('Failed to fetch access token from Reddit!');
        }

        if (isset($response->error)) {
            throw new Exception('Failed Reddit auth with ' . $response->error);
        }

        $r = new self($response->access_token);
        $me = $r->get('/api/v1/me/');

        $user = User::firstOrNew([
            'name' => $me->name,
        ]);

        $user->access_token = $response->access_token;
        $user->refresh_token = $response->refresh_token;
        $user->token_expires_at = time() + $response->expires_in;
        $user->save();

        return $user;
    }

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function get($endpoint)
    {
        $http = new \GuzzleHttp\Client();
        $responseRaw = $http->request('GET', 'https://oauth.reddit.com' . $endpoint, [
            'headers' => [
                'User-Agent' => config('app.name'),
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
        ]);

        $response = json_decode((string) $responseRaw->getBody());
        if (!$response) {
            throw new Exception(sprintf('%s failed to get', self::class, $endpoint));
        }

        return $response;
    }
}
