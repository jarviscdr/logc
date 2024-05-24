<?php

declare(strict_types=1);
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 *
 * @copyright walkor<walkor@workerman.net>
 *
 * @link      http://www.workerman.net/
 *
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use support\Request;

return [
    'app_name'              => env('APP_NAME', 'Logc'), // APP名称
    'base_url'              => env('BASE_URL', 'http://127.0.0.1:10001'), // 基础 URL
    'debug'                 => env('DEBUG', false), // 是否开启调试模式
    'error_reporting'       => E_ALL,
    'default_timezone'      => 'Asia/Shanghai',
    'request_class'         => Request::class,
    'public_path'           => base_path().DIRECTORY_SEPARATOR.'public',
    'runtime_path'          => base_path(false).DIRECTORY_SEPARATOR.'runtime',
    'controller_suffix'     => 'Controller',
    'controller_reuse'      => false,
    'log_queue'             => env('LOG_QUEUQ', ''), // 是否使用日志队列:空|redis|rabbitmq (为空直接入库，不使用队列)
    'jwt_access_secret_key' => env('JWT_ACCESS_SECRET_KEY'), // JWT 访问密钥
];
