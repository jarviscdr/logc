<?php

declare(strict_types=1);

$redisHost     = env('REDIS_HOST', '127.0.0.1');
$redisPort     = env('REDIS_PORT', 6379);
$redisPassword = env('REDIS_PASSWORD', null);
$redisDatabase = env('REDIS_DATABASE', 0);

return [
    'default' => [
        'host'    => "redis://{$redisHost}:{$redisPort}",
        'options' => [
            'auth'          => $redisPassword, // 密码，字符串类型，可选参数
            'db'            => $redisDatabase, // 数据库
            'prefix'        => '',       // key 前缀
            'max_attempts'  => 5, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
        ],
    ],
];
