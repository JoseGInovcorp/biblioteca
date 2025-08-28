<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | Mailer usado por defeito quando nenhum outro for especificado.
    | Define no .env: MAIL_MAILER=smtp (Mailtrap) ou MAIL_MAILER=real_smtp (real)
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Configurações para cada mailer disponível.
    |
    */

    'mailers' => [

        // 📧 Mailtrap (teste)
        'smtp' => [
            'transport'    => 'smtp',
            'host'         => env('MAIL_HOST', 'sandbox.smtp.mailtrap.io'),
            'port'         => env('MAIL_PORT', 2525),
            'username'     => env('MAIL_USERNAME'),
            'password'     => env('MAIL_PASSWORD'),
            'encryption'   => env('MAIL_ENCRYPTION', null),
            'timeout'      => null,
            'local_domain' => env(
                'MAIL_EHLO_DOMAIN',
                parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)
            ),
        ],

        // 📧 SMTP Real (produção/teste real)
        'real_smtp' => [
            'transport'    => 'smtp',
            'host'         => env('MAIL_REAL_HOST', 'smtp.gmail.com'),
            'port'         => env('MAIL_REAL_PORT', 587),
            'username'     => env('MAIL_REAL_USERNAME'),
            'password'     => env('MAIL_REAL_PASSWORD'),
            'encryption'   => env('MAIL_REAL_ENCRYPTION', 'tls'),
            'timeout'      => null,
            'local_domain' => env(
                'MAIL_REAL_EHLO_DOMAIN',
                parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)
            ),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path'      => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers'   => ['smtp', 'log'],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers'   => ['ses', 'postmark'],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | Endereço e nome usados por defeito em todos os emails enviados.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name'    => env('MAIL_FROM_NAME', 'Example'),
    ],

];
