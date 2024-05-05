<?php

declare(strict_types=1);

namespace app\queue\rabbitmq;

use Bunny\Async\Client as BunnyClient;
use Bunny\Channel as BunnyChannel;
use Bunny\Message as BunnyMessage;
use Workbunny\WebmanRabbitMQ\Builders\QueueBuilder;
use Workbunny\WebmanRabbitMQ\Constants;

class LogsFailBuilder extends QueueBuilder {
    protected array $queueConfig = [
        // 队列名称 ，默认由类名自动生成
        'name' => 'logs_fail',
        // 是否延迟
        'delayed' => false,
        // QOS 数量
        'prefetch_count' => 0,
        // QOS size
        'prefetch_size' => 0,
        // QOS 全局
        'is_global' => false,
        // 路由键
        'routing_key' => 'logs_fail',
    ];

    /** @var string 交换机类型 */
    protected string $exchangeType = Constants::DIRECT;

    /** @var string|null 交换机名称,默认由类名自动生成 */
    protected ?string $exchangeName = 'logs_fail';

    /** {@inheritDoc} */
    public function handler(BunnyMessage $message, BunnyChannel $channel, BunnyClient $client): string {
        VD('入库失败', $message->content);

        return Constants::ACK;
    }
}
