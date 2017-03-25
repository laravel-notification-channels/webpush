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

];
