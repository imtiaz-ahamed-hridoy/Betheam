<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Bokun API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Bokun API credentials and settings. This file
    | provides a convenient location for storing your Bokun access key,
    | secret key, and base URL for easy retrieval throughout your application.
    |
    */

    'access_key' => env('BOKUN_ACCESS_KEY'),

    'secret_key' => env('BOKUN_SECRET_KEY'),

    'api_base_url' => env('BOKUN_API_BASE_URL', 'https://api.bokun.io'),

];