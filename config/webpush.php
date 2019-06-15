<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VAPID Authentication
    |--------------------------------------------------------------------------
    |
    | You'll need to create a public and private key for your server.
    | These keys must be safely stored and should not change.
    |
    */

    'vapid' => [
        'subject' => env('VAPID_SUBJECT'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'pem_file' => env('VAPID_PEM_FILE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Cloud Messaging
    |--------------------------------------------------------------------------
    |
    | Deprecated and optional. It's here only for compatibility reasons.
    |
    */

    'gcm' => [
        'key' => env('GCM_KEY'),
        'sender_id' => env('GCM_SENDER_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    |
    | If you want to change the name of the subscriptions table
    |
    */

    'db_table' => env('WEBPUSH_TABLE', 'push_subscriptions'),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | If you want to change the connection of the subscriptions table
    |
    */

    'db_connection' => env('WEBPUSH_CONNECTION'),
];
