<?php

declare(strict_types=1);

namespace app\queue\rabbitmq;

use app\service\LogService;
use Bunny\Async\Client as BunnyClient;
use Bunny\Channel as BunnyChannel;
use Bunny\Message as BunnyMessage;
use support\Container;
use Workbunny\WebmanRabbitMQ\Builders\QueueBuilder;
use Workbunny\WebmanRabbitMQ\Constants;

class LogsBuilder extends QueueBuilder {
    /**
     * @var array = [
     *            'name'           => 'example',
     *            'delayed'        => false,
     *            'prefetch_count' => 1,
     *            'prefetch_size'  => 0,
     *            'is_global'      => false,
     *            'routing_key'    => '',
     *            ]
     */
    protected array $queueConfig = [
        // 队列名称 ，默认由类名自动生成
        'name' => 'logs',
        // 是否延迟
        'delayed' => false,
        // QOS 数量
        'prefetch_count' => 0,
        // QOS size
        'prefetch_size' => 0,
        // QOS 全局
        'is_global' => false,
        // 路由键
        'routing_key' => 'logs',
    ];

    /** @var string 交换机类型 */
    protected string $exchangeType = Constants::DIRECT;

    /** @var string|null 交换机名称,默认由类名自动生成 */
    protected ?string $exchangeName = 'logs';

    // 日志服务
    protected LogService $logService;

    public function __construct() {
        parent::__construct();
        $this->getBuilderConfig()->setArguments([
            'x-dead-letter-exchange'    => 'logs_fail',
            'x-dead-letter-routing-key' => 'logs_fail',
        ]);
        $this->logService = Container::get(LogService::class);
    }

    /** {@inheritDoc} */
    public function handler(BunnyMessage $message, BunnyChannel $channel, BunnyClient $client): string {
        try {
            $data = json_decode($message->content, true);
            // 处理日志数据
            $this->logService->create($data);

            return Constants::ACK;
        } catch (\Throwable $th) {
            VD('日志入库失败:', $th->getMessage());
        }

        return Constants::NRQ_NACK;
    }
}
