<?php

namespace app\controller;

use taoser\exception\ValidateException;

class BaseController
{
    public function validate($validateClass, $scene = '')
    {
        try {
            $validator = validate($validateClass);
            if (!empty($scene)) {
                $validator->scene($scene);
            }
            $validator->check(request()->all());
            return $validator->getSafeValue();
        } catch (ValidateException $e) {
            BE($e->getError());
        }
    }
}
