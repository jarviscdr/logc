<?php

namespace app\service;

use app\model\Projects;

class ProjectService extends BaseService
{
    /**
     * 当前项目列表
     *
     * @var array
     */
    private $projects = [];

    /**
     * 加载项目列表
     *
     * @return void
     * @author Jarvis
     * @date   2024-02-24 22:12
     */
    private function loadProjects(){
        $data = $this->list();
        $this->projects = $data->toArray();
    }

    /**
     * 判断项目是否存在
     *
     * @param  string $project
     * @return bool
     * @author Jarvis
     * @date   2024-02-24 22:11
     */
    public function exist($project) {
        if(empty($this->projects)) {
            $this->loadProjects();
        }
        return in_array($project, $this->projects);
    }

    /**
     * 添加项目
     *
     * @param  string $name 项目名称
     * @return bool
     * @author Jarvis
     * @date   2024-02-22 00:13
     */
    public function add($name){
        if($this->exist($name)) {
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
     * @author Jarvis
     * @date   2024-02-22 00:14
     */
    public function list(){
        $data =  Projects::pluck('name');
        return $data;
    }
}
