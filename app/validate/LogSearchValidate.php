<?php

declare(strict_types=1);

namespace app\validate;

class LogSearchValidate extends BaseValidate {
    // 定义规则
    protected $rule = [
        'project|项目' => 'min:1',
        'type|类型'    => 'integer|in:0,1,2,3,4,5', // 1:ERROR, 2:WARNING, 3:INFO, 4:DEBUG 5:NOTICE
        'tags|标签'    => 'min:1',
        'time|时间'    => 'date',
        'content|内容' => 'min:1',
    ];

    // 定义信息
    protected $message = [];

    //定义场景
    protected $scene = [];
}
