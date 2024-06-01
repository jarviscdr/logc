<?php

declare(strict_types=1);

namespace app\validate;

class UserValidate extends BaseValidate {
    // 定义规则
    protected $rule = [
        'id|用户ID'      => 'require|integer',
        'username|用户名' => 'require|length:5,20|alphaDash',
        'nickname|昵称'  => 'require|length:1,20',
        'password|密码'  => 'length:6,20|regex:[A-Za-z0-9@#$%^&*()_+]+',
        'avatar|头像地址' => 'activeUrl',
        'email|邮箱'     => 'email',
        'mobile|手机'    => ['regex' => '/^1[34578]\d{9}$/'],
        'status|状态'    => 'integer|in:1,2',
    ];

    // 定义信息
    protected $message = [];

    //定义场景
    protected $scene = [
        'login'  => ['username', 'password'],
        'update' => ['id', 'nickname', 'password', 'avatar', 'email', 'mobile', 'status'],
    ];
}
