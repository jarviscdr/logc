<?php

declare(strict_types=1);

namespace app\validate;

use taoser\Validate;

/**
 * 基础验证器
 *
 * ThinkPHP验证器文档：https://static.kancloud.cn/manual/thinkphp6_0/1037629
 *
 * @author Jarvis
 * @date   2024-05-18 05:55
 */
class BaseValidate extends Validate {
    // 定义规则
    protected $rule = [
        // 'field|字段名'  => 'require'
    ];

    // 定义信息
    protected $message = [
        // 'field.require' => '字段不能为空',
    ];

    //定义场景
    protected $scene = [
        // '场景key'  =>  ['改场景需验证的字段'],
    ];

    /**
     * 获取安全值
     *
     * @return array
     *
     * @author Jarvis
     * @date   2024-02-16 22:24
     */
    public function getSafeValue() {
        $request = request();
        $fields  = [];

        if (! empty($this->currentScene) && isset($this->scene[$this->currentScene])) {
            $fields = $this->scene[$this->currentScene];
        } else {
            foreach ($this->rule as $field => $rule) {
                $field    = explode('|', $field)[0];
                $fields[] = $field;
            }
        }

        if (empty($fields)) {
            return [];
        }

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $request->input($field);
        }

        return $data;
    }
}
