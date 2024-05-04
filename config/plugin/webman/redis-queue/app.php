<?php

declare(strict_types=1);

return [
    'enable' => env('LOG_QUEUQ', '') == 'redis' ? true : false,
];
