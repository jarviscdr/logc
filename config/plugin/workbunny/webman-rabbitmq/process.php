<?php

declare(strict_types=1);

return [
    'logs' => [
        'handler' => \app\queue\rabbitmq\LogsBuilder::class,
        'count'   => 2,
        'mode'    => 'queue',
    ],
    'logs_fail' => [
        'handler' => \app\queue\rabbitmq\LogsFailBuilder::class,
        'count'   => 1,
        'mode'    => 'queue',
    ],
];
