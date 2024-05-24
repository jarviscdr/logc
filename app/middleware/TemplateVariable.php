<?php

declare(strict_types=1);

namespace app\middleware;

use support\View;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class TemplateVariable implements MiddlewareInterface {
    public function process(Request $request, callable $next): Response {
        View::assign('config', [
            'app_name' => config('app.app_name'),
            'base_url' => config('app.base_url'),
        ]);

        return $next($request);
    }
}
