<?php

declare(strict_types=1);

namespace app\service;

use app\model\Logs;
use support\Db;
use Webman\RedisQueue\Redis;

class LogService extends BaseService {
    protected static $logQueue = '';

    public function __construct(
        protected ProjectService $projectService
    ) {
        self::$logQueue = config('app.log_queue');
    }

    public const LOG_TYPE_LIST = [ // 日志类型列表
        1 => 'ERROR',
        2 => 'WARNING',
        3 => 'INFO',
        4 => 'DEBUG',
        5 => 'NOTICE',
    ];

    /**
     * 添加日志
     *
     * @param array $data
     *
     * @author Jarvis
     * @date   2024-05-04 14:06
     */
    public function add($data): void {
        switch (self::$logQueue) {
            case 'redis':
                Redis::send('logs', $data);
                break;
            case 'rabbitmq':
                // TODO 待实现
                break;
            default:
                $this->create($data);
                break;
        }
    }

    /**
     * 创建日志
     *
     * @param  array $data
     * @return int
     *
     * @author Jarvis
     * @date   2024-02-16 23:51
     */
    public function create($data) {
        if (! is_array($data['tags'])) {
            $data['tags'] = [$data['tags']];
        }

        $data['tags'] = json_encode($data['tags'], JSON_UNESCAPED_UNICODE);
        if (! is_array($data['content'])) {
            $data['content'] = ['data' => $data['content']];
        }

        // 将数组转换为字符串
        $data['content_text'] = arrayToString($data['content']);
        // 将内容转换为JSON字符串
        $data['content'] = json_encode($data['content'], JSON_UNESCAPED_UNICODE);

        // 获取项目ID
        $data['project'] = $this->projectService->getIdByName($data['project']);

        try {
            $this->projectService->add($data['project']);
        } catch (\Throwable $th) {
            BE($th->getMessage());
        }

        $id = Logs::insertGetId($data);
        if (empty($id)) {
            BE('添加失败');
        }

        return $id;
    }

    /**
     * 日志详情
     *
     * @param  int   $id 日志ID
     * @return array
     *
     * @author Jarvis
     * @date   2024-02-16 23:50
     */
    public function info($id) {
        $data = Logs::find($id);
        if (empty($data)) {
            BE('日志不存在');
        }

        return $data;
    }

    /**
     * 搜索日志
     *
     * @param  array                                    $where  查询条件
     * @param  int                                      $offset
     * @param  int                                      $limit
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @author Jarvis
     * @date   2024-02-24 21:51
     */
    public function search($where, $offset = 0, $limit = 50) {
        $data = Logs::query()
            ->when(! empty($where['id']), fn ($query) => $query->where('id', '>=', $where['id']))
            ->when(! empty($where['project']), fn ($query) => $query->where('project', ProjectService::getIdByName($where['project'])))
            ->when(! empty($where['time']), fn ($query) => $query->whereBetween('time', [$where['time'].' 00:00:00', $where['time'].' 23:59:59']))
            ->when(! empty($where['type']), fn ($query) => $query->where('type', $where['type']))
            ->when(! empty($where['tags']), function ($query) use ($where): void {
                $tags = explode(' ', trim($where['tags']));
                $tags = array_filter($tags);
                $tags = array_unique($tags);
                array_map(fn ($tag) => trim($tag), $tags);
                $tags = json_encode($tags, JSON_UNESCAPED_UNICODE);
                $query->whereRaw("JSON_CONTAINS(tags, '{$tags}')");
            })
            ->when(! empty($where['content']), fn ($query) => $query->whereRaw('MATCH(content_text) AGAINST(? IN BOOLEAN MODE)', [fulltextKeyword($where['content'])]))
            ->offset($offset)
            ->limit($limit)
            ->orderBy('id', 'DESC')
            // ->dd();
            ->get(['id', 'project', 'type', 'tags', 'time', 'content']);

        return $data;
    }

    /**
     * 分词
     *
     * @param  string $str
     * @return array
     *
     * @author Jarvis
     * @date   2024-02-24 22:50
     */
    public function wordSegmentation($str) {
        $data = Db::select("select * from to_tsquery('jiebacfg', '{$str}')");
        if (empty($data)) {
            return [];
        }

        $tsQuery = array_shift($data)->to_tsquery;
        preg_match_all("/'([^']*)'/", $tsQuery, $matches);
        if (! empty($matches)) {
            return $matches[1];
        }

        return [];
    }
}
