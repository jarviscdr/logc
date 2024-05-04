<?php

declare(strict_types=1);

namespace app\service;

use app\model\Projects;

class ProjectService extends BaseService {
    public static $NAME2ID = [];

    public static $ID2NAME = [];

    /**
     * 当前项目列表
     *
     * @var array
     */
    private $projects = [];

    /**
     * 加载项目列表
     *
     *
     * @author Jarvis
     * @date   2024-02-24 22:12
     */
    private function loadProjects(): void {
        $data           = $this->list();
        $this->projects = $data->toArray();
    }

    /**
     * 判断项目是否存在
     *
     * @param  string $project
     * @return bool
     *
     * @author Jarvis
     * @date   2024-02-24 22:11
     */
    public function exist($project) {
        if (empty($this->projects)) {
            $this->loadProjects();
        }

        return in_array($project, $this->projects);
    }

    /**
     * 添加项目
     *
     * @param  string $name 项目名称
     * @return bool
     *
     * @author Jarvis
     * @date   2024-02-22 00:13
     */
    public function add($name) {
        if ($this->exist($name)) {
            return true;
        }
        try {
            $project = new Projects(['name' => $name]);
            $project->save();
            $this->projects[] = $name;
        } catch (\Throwable $th) {
            BE('添加项目失败');
        }

        return true;
    }

    /**
     * 项目列表
     *
     * @return \Illuminate\Support\Collection
     *
     * @author Jarvis
     * @date   2024-02-22 00:14
     */
    public function list() {
        $data = Projects::pluck('name');

        return $data;
    }

    /**
     * 使用项目名称获取ID
     *
     * @param string $name
     *
     * @author Jarvis
     * @date   2024-04-27 18:03
     */
    public static function getIdByName($name): int {
        if (empty(self::$NAME2ID[$name])) {
            $id = Projects::where('name', $name)->value('id');
            if (empty($id)) {
                $project = new Projects(['name' => $name]);
                $project->save();
                $id = $project->id;
            }
            self::$NAME2ID[$name] = $id;
        }

        return self::$NAME2ID[$name];
    }

    /**
     * 使用ID获取项目名称
     *
     * @param int $id
     *
     * @author Jarvis
     * @date   2024-05-01 00:16
     */
    public static function getNameById($id): string {
        if (empty(self::$ID2NAME[$id])) {
            $name = Projects::where('id', $id)->value('name');
            if (empty($name)) {
                return '未知';
            }
            self::$ID2NAME[$id] = $name;
        }

        return self::$ID2NAME[$id];
    }
}
