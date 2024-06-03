<?php

declare(strict_types=1);

namespace app\exception;

use support\exception\BusinessException as BaseBusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class BusinessException extends BaseBusinessException {
    protected $code = 1;

    protected $message = '业务异常';

    protected $data = [];

    public function __construct($message = '', $code = 1, $data = []) {
        $this->message = $message;
        $this->code    = $code;
        $this->data    = $data;
    }

    public function render(Request $request): ?Response {
        if ($this->code == 401 && ! $request->expectsJson()) {
            $baseUrl = config('app.base_url');

            return redirect($baseUrl.'/user/login');
        }

        return error($this->message, $this->code, $this->data);
    }
}
