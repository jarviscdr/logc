<?php

namespace app\validate;

class LogValidate extends BaseValidate
{
    // 定义规则
    protected $rule =   [
        'id' => 'require|integer',
        'project|项目' => 'require',
        'type|类型' => 'require|integer|in:1,2,3,4,5', // 1:ERROR, 2:WARNING, 3:INFO, 4:DEBUG 5:NOTICE
        'tags|标签' => 'array',
        'time|时间' => 'require|date',
        'content|内容' => 'require',
    ];

    // 定义信息
    protected $message = [];

    //定义场景
    protected $scene = [
        'create' => ['project', 'type', 'tags', 'time', 'content'],
        'info' => ['id']
    ];
}
