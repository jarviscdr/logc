<?php

declare(strict_types=1);

namespace app\controller;

use app\service\LogService;
use app\service\ProjectService;
use support\Request;

class IndexController {
    /**
     * 不需要登录的方法
     *
     * @var string[]
     */
    protected $noNeedLogin = [];

    public function __construct(
        protected ProjectService $projectService
    ) {
    }

    /**
     * 日志视图
     *
     * @return void
     *
     * @author Jarvis
     * @date   2024-02-18 22:37
     */
    public function index(Request $request) {
        return view('log/index', [
            'projects' => $this->projectService->list(),
            'types'    => LogService::LOG_TYPE_LIST,
        ]);
    }
}
