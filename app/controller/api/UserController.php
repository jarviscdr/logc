<?php

declare(strict_types=1);

namespace app\controller\api;

use app\controller\BaseController;
use app\service\UserService;
use app\validate\UserValidate;
use support\Request;
use support\Response;

class UserController extends BaseController {
    /**
     * 不需要登录的方法
     *
     * @var string[]
     */
    protected $noNeedLogin = ['login'];

    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * 登录
     *
     * @author Jarvis
     * @date   2024-06-01 22:31
     */
    public function login(Request $request): Response {
        $data      = $this->validate(UserValidate::class, 'login');
        $tokenInfo = $this->userService->login($data['username'], $data['password']);

        return success($tokenInfo, '登录成功', 0, [], [
            'access_token'  => $tokenInfo['access_token'],
            'refresh_token' => $tokenInfo['refresh_token'],
        ]);
    }

    /**
     * 退出登录
     *
     * @author Jarvis
     * @date   2024-06-01 22:31
     */
    public function logout(Request $request): Response {
        return success([], '退出成功', 0, [], [
            'access_token'  => '',
            'refresh_token' => '',
        ]);
    }
}
