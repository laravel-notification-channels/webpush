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
    | Customize Subscriber Model
    |--------------------------------------------------------------------------
    |
    | Here you can customize the subscriber model.
    |
    */
    'subscriber_table' => 'users',
    'subscriber_model' => App\User::class,
    'subscriber_foreing_key' => 'user_id'
];
