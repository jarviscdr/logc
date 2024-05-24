<?php

declare(strict_types=1);

namespace app\controller;

use taoser\exception\ValidateException;

class BaseController {
    /**
     * 不需要登录的方法
     *
     * @var string[]
     */
    protected $noNeedLogin = [];

    public function __construct() {

    }

    public function validate($validateClass, $scene = '') {
        try {
            $validator = validate($validateClass);
            if (! empty($scene)) {
                $validator->scene($scene);
            }
            $validator->check(request()->all());

            return $validator->getSafeValue();
        } catch (ValidateException $e) {
            BE($e->getError());
        }
    }
}
