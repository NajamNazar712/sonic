<?php

$environment = env('APP_ENV', 'production');

if ($environment == 'local') {
    return array(
        'pdf' => array(
            'enabled' => true,
            'binary' => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"',
            'timeout' => false,
            'options' => array(),
            'env'     => array(),
        ),
        'image' => array(
            'enabled' => true,
            'binary' => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltoimage.exe"',
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
            'binary' => '/usr/bin/wkhtmltopdf',
            'timeout' => false,
            'options' => array('enable-local-file-access' => true,),
            'env'     => array(),
        ),
        'image' => array(
            'enabled' => true,
            'binary' => '/usr/bin/wkhtmltoimage',
            'timeout' => false,
            'options' => array('enable-local-file-access' => true,),
            'env'     => array(),
        ),
    );
}