<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Book Processing Charge (BPC)
    |--------------------------------------------------------------------------
    |
    | The default processing fee applied to a book the moment it is
    | accepted. Override with EBOOK_PROCESSING_FEE in .env.
    |
    */

    'processing_fee' => env('EBOOK_PROCESSING_FEE', 75.00),

    'currency' => env('EBOOK_CURRENCY', 'ETB'),
];
