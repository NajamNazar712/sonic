<?php

namespace App\Services\Marco;

abstract class MarcoBaseService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        if (app()->environment('production')) {

            $this->baseUrl = 'https://marco.trax.pk';
            $this->apiKey  = 'PRODUCTION_API_KEY_HERE';
        } elseif (app()->environment('staging')) {

            $this->baseUrl = 'https://marco-staging.trax.pk';
            $this->apiKey  = 'UjRYTUZBazF5enVCRXZFUE51WlNpSXVtR1BJMUt6RVdEUFZ1MG5KQWZ2RWxGcWNuSnljdzZVNTh2eWpR694509de705c0';
        } else {
            // local
            $this->baseUrl = 'http://marco_v2.test';
            $this->apiKey  = 'TUxOYmhaZDg2WGVZMW90VGlVb3NFSmN4VnNoQ1QxVzkxU0l0ZnRWemRRVFJOanNMT1FTcU5XZW16d1Qw693fcdb099420';
        }
    }

    protected function headers(): array
    {
        return [
            'Accept'    => 'application/json',
            'X-API-KEY' => $this->apiKey,
        ];
    }

    protected function endpoint(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
