<?php

namespace app\exception;

use support\exception\BusinessException as BaseBusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class BusinessException extends BaseBusinessException
{
    protected $code = 1;
    protected $message = '业务异常';
    protected $data = [];

    public function __construct($message = '', $code = 1, $data = [])
    {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }

    public function render(Request $request): ?Response
    {
        return error($this->message, $this->code, $this->data);
    }
}
