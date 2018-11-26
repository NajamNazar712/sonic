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
        'http://sonic.pk/admin/cargo/create/shipment_details',
        'http://sonic.pk/admin/cargo/create/consignment_details',
        'http://sonic.pk/admin/cargo/create/store',
        'http://sonic.pk/admin/cargo/receive/shipment_details',
        'http://sonic.pk/admin/cargo/receive/short_received',
        'http://sonic.pk/admin/cargo/receive/store',
        'http://sonic.pk/admin/delivery/note/shipment/info',
        'http://sonic.pk/admin/delivery/note/create',
        'http://sonic.pk/admin/return/create/shipment_details',
        'http://sonic.pk/admin/return/create/note/submit',
        'http://sonic.pk/admin/finance/make_payments/verify',
        'http://sonic.pk/admin/finance/make_payments/store'
    ];
}
