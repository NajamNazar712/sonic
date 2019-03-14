<?php

$environment = env('APP_ENV', 'production');

if ($environment == 'local') {
    return array(
        'pdf' => array(
            'enabled' => true,
            'binary' => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"',
            'timeout' => false,
            'options' => array(),
            'env'     => array(),
        ),
        'image' => array(
            'enabled' => true,
            'binary' => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltoimage"',
            'timeout' => false,
            'options' => array(),
            'env'     => array(),
        ),
    );
}
else {
    return array(
        'pdf' => array(
            'enabled' => true,
            'binary' => base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),
            'timeout' => false,
            'options' => array(),
            'env'     => array(),
        ),
        'image' => array(
            'enabled' => true,
            'binary' => base_path('vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64'),
            'timeout' => false,
            'options' => array(),
            'env'     => array(),
        ),
    );
}