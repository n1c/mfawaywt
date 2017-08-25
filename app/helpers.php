<?php

if (!function_exists('smix')) {
    function smix($file)
    {
        return config('app.url_static') . mix($file);
    }
}

if (!function_exists('links_from_md')) {
    function links_from_md($md)
    {
        // lol surely this could just be a regex.
        $md = str_replace(PHP_EOL, ' ', $md);
        $md = str_replace('(', ' ', $md);
        $md = str_replace(')', ' ', $md);
        $tokens = explode(' ', $md);

        $links = [];
        foreach ($tokens as $token) {
            if (starts_with($token, 'http')) {
                // Trim possible #'s
                if (str_contains($token, '#')) {
                    $token = substr($token, 0, strpos($token, '#'));
                }

                if (str_contains($token, '&')) {
                    $token = substr($token, 0, strpos($token, '&'));
                }

                if (ends_with($token, '/')) {
                    $token = substr($token, 0, strlen($token) - 1);
                }

                if (ends_with($token, '.')) {
                    $token = substr($token, 0, strlen($token) - 1);
                }

                $token = str_replace('*', '', $token);

                if (!in_array($token, $links)) {
                    $links[] = $token;
                }
            }
        }

        return $links;
    }
}
