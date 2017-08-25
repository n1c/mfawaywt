<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Model\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'google' => [
        'analytics' => env('GOOGLE_ANALYTICS', ''),
    ],

    'viglink' =>  [
        'key' => env('VIGLINK_KEY', 'DEV'),
    ],

    'reddit' => [
        'client_id' => env('REDDIT_CLIENT_ID'),
        'secret' => env('REDDIT_SECRET'),
    ],

    'imgur' => [
        'client_id' => env('IMGUR_CLIENT_ID'),
        'secret' => env('IMGUR_SECRET'),
        'low-credits' => 5, // credits
        'low-credits-cooldown' => 5, // minutes
    ],

    'mailchimp' => [
        'newslettersubpost' => env('MAILCHIMP_NEWSLETTERSUBPOST'),
        'newslettersubname' => env('MAILCHIMP_NEWSLETTERSUBNAME'),
    ],

];
