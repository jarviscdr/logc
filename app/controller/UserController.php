<?php

declare(strict_types=1);

namespace app\controller;

use support\Request;

class UserController {
    /**
     * 不需要登录的方法
     *
     * @var string[]
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request) {
        return view('user/login');
    }
}
