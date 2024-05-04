<?php

declare(strict_types=1);

namespace app\queue\redis;

use app\service\LogService;
use Webman\RedisQueue\Consumer;

class Logs implements Consumer {
    // 要消费的队列名
    public $queue = 'logs';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    public function __construct(private LogService $logService) {
    }

    // 消费
    public function consume($data): void {
        $this->logService->create($data);
    }
}
