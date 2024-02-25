<?php

namespace process;

use app\service\LogService;
use app\validate\LogValidate;
use Workerman\Connection\TcpConnection;

class Websocket
{
    public function __construct(
        protected LogService $logService
    ) {
    }

    /**
     * 进程入口
     *
     * @return void
     * @author Jarvis
     * @date   2024-02-18 22:32
     */
    public function onWorkerStart(){}

    public function onConnect(TcpConnection $connection){}

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer){}

    public function onClose(TcpConnection $connection){}

    /**
     * 接收消息
     *
     * @param  TcpConnection $connection
     * @param  string        $data
     * @return void
     * @author Jarvis
     * @date   2024-02-25 00:41
     */
    public function onMessage(TcpConnection $connection, $data)
    {
        if ($data == 'ping') {
            return;
        }

        $data = json_decode($data, true);
        if (empty($data)) {
            $this->sendError($connection, '参数不能为空');
            return;
        }

        try {
            validate(LogValidate::class)->scene('create')->check($data);
            $this->logService->create($data);
            $this->sendSuccess($connection);
        } catch (\Throwable $th) {
            $this->sendError($connection, $th->getMessage());
        }
    }

    /**
     * 发送错误消息
     *
     * @param  TcpConnection $connection
     * @param  string        $message
     * @param  array|string  $data
     * @return void
     * @author Jarvis
     * @date   2024-02-25 00:39
     */
    private function sendError(TcpConnection $connection, string $message, $data = null)
    {
        $connection->send(json_encode([
            'code' => 1,
            'msg' => $message,
            'data' => $data ?? new \stdClass
        ]));
    }

    /**
     * 发送成功消息
     *
     * @param  TcpConnection $connection
     * @param  string        $message
     * @param  array|string  $data
     * @return void
     * @author Jarvis
     * @date   2024-02-25 14:25
     */
    private function sendSuccess(TcpConnection $connection, string $message = 'success', $data = null)
    {
        $connection->send(json_encode([
            'code' => 0,
            'msg' => $message,
            'data' => $data ?? new \stdClass
        ]));
    }
}
