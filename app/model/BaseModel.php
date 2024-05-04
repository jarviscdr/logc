<?php

declare(strict_types=1);

namespace app\model;

use support\Model;

/**
 * 日志项目列表
 */
class BaseModel extends Model {
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = '';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

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
    protected $fillable = [];

    // 将created_at在展示时格式化成Y-m-d H:i:s
    public function getCreatedAtAttribute($value) {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    // 将updated_at在展示时格式化成Y-m-d H:i:s
    public function getUpdatedAtAttribute($value) {
        return date('Y-m-d H:i:s', strtotime($value));
    }
}
