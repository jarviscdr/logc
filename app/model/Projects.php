<?php

namespace app\model;

use support\Model;

/**
 * 日志项目列表
 */
class Projects extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 允许实例化时加载的字段
     *
     * @var array
     */
    protected $fillable = ['name'];

}
