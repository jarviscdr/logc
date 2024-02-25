<?php

namespace app\controller;

use app\service\LogService;
use app\service\ProjectService;
use support\Db;
use support\Request;

class IndexController
{
    public function __construct(
        protected ProjectService $projectService
    ) {
    }

    /**
     * 日志视图
     *
     * @param  Request $request
     * @return void
     * @author Jarvis
     * @date   2024-02-18 22:37
     */
    public function index(Request $request)
    {
        return view('log/index', [
            'projects' => $this->projectService->list(),
            'types' => LogService::LOG_TYPE_LIST
        ]);
    }
}
