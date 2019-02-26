<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'admin/cargo/create/*',
        'admin/cargo/receive/*',
        'admin/delivery/note/shipment/info',
        'admin/delivery/note/create',
        'admin/return/create/*',
        'admin/finance/make_payments/*'
    ];
}
