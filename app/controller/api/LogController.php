<?php

namespace app\controller\api;

use app\controller\BaseController;
use app\service\LogService;
use app\validate\LogSearchValidate;
use app\validate\LogValidate;
use support\Request;
use support\Response;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\ServerSentEvents;
use Workerman\Timer;

/**
 * 日志控制器
 *
 * @author Jarvis
 * @date   2024-02-18 22:37
 */
class LogController extends BaseController
{
    public function __construct(
        protected LogService $logService
    ) {
    }

    /**
     * 记录日志
     *
     * @return Response
     * @author Jarvis
     * @date   2024-02-16 23:03
     */
    public function record()
    {
        $data = $this->validate(LogValidate::class, 'create');
        $id = $this->logService->create($data);
        return success(['id' => $id]);
    }

    /**
     * 日志详情
     *
     * @return Response
     * @author Jarvis
     * @date   2024-02-17 22:19
     */
    public function info()
    {
        $data = $this->validate(LogValidate::class, 'info');
        $data = $this->logService->info($data['id']);
        return success($data);
    }

    /**
     * 搜索日志
     *
     * @return Response
     * @author Jarvis
     * @date   2024-02-17 22:18
     */
    public function search()
    {
        $where = $this->validate(LogSearchValidate::class);
        $data = $this->logService->search($where);

        $words = [];
        if(!empty($where['content'])) {
            $words = $this->logService->wordSegmentation($where['content']);
        }
        return success(['words' => $words, 'list' => $data]);
    }

    /**
     * 监听日志
     *
     * @param  Request $request
     * @return Response
     * @author Jarvis
     * @date   2024-02-17 22:15
     */
    public function listen(Request $request)
    {
        $where = $this->validate(LogSearchValidate::class);
        if ($request->header('accept') == 'text/event-stream') {
            $connection = $request->connection;
            $connection->send(new Response(200, ['Content-Type' => $request->header('accept')], "\r\n"));

            if(!empty($where['content'])) {
                $words = $this->logService->wordSegmentation($where['content']);
                $connection->send(new ServerSentEvents([
                    'event' => 'message',
                    'data' => json_encode([
                        'type' => 'words',
                        'data' => $words
                    ])
                ]));
            }


            $where['id'] = 0;
            // 定时向客户端推送数据
            $timerId = Timer::add(2, function () use ($connection, &$timerId, &$where, &$lastId) {
                // 连接关闭的时候要将定时器删除，避免定时器不断累积导致内存泄漏
                if ($connection->getStatus() !== TcpConnection::STATUS_ESTABLISHED) {
                    Timer::del($timerId);
                    return;
                }

                $data = $this->logService->search($where);
                if (!$data->isEmpty()) {
                    $where['id'] = $data->first()->id + 1;
                    // 发送message事件，事件携带的数据为日志列表
                    $connection->send(new ServerSentEvents([
                        'event' => 'message',
                        'data' => json_encode([
                            'type' => 'logs',
                            'data' => $data
                        ])
                    ]));
                }
            });
            return;
        }
        return error('请求类型错误');
    }
}
