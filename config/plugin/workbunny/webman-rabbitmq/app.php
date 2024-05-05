<?php

declare(strict_types=1);

return [
    'enable' => true,

    'host'       => env('RABBITMQ_HOST', 'rabbitmq'),
    'vhost'      => env('RABBITMQ_VHOST', '/'),
    'port'       => env('RABBITMQ_PORT', 5672),
    'username'   => env('RABBITMQ_USERNAME', 'guest'),
    'password'   => env('RABBITMQ_PASSWORD', 'guest'),
    'mechanisms' => 'AMQPLAIN',
    'timeout'    => 10,
    // 重启间隔
    'restart_interval' => 0,
    // 心跳间隔
    'heartbeat' => 50,
    // 心跳回调
    'heartbeat_callback' => function (): void {
    },
    // 错误回调
    'error_callback' => function (Throwable $throwable): void {
    },
    // 复用连接
    'reuse_connection' => false,
    // AMQPS 如需使用AMQPS请取消注释
    //    'ssl'                => [
    //        'cafile'      => 'ca.pem',
    //        'local_cert'  => 'client.cert',
    //        'local_pk'    => 'client.key',
    //    ],
];
