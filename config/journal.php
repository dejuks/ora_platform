<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Article Processing Charge (APC)
    |--------------------------------------------------------------------------
    |
    | The default publication fee applied to a manuscript the moment it
    | is accepted. Override with JOURNAL_PUBLICATION_FEE in .env.
    |
    */

    'publication_fee' => env('JOURNAL_PUBLICATION_FEE', 50.00),

    'currency' => env('JOURNAL_CURRENCY', 'ETB'),
];
