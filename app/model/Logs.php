<?php

declare(strict_types=1);

namespace app\model;

use app\service\ProjectService;

/**
 * 日志记录
 */
class Logs extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'project',
        'type',
        'tags',
        'content',
        'content_text',
        'time',
    ];

    // 将tags在展示时转换由json字符串转换为数组
    public function getTagsAttribute($value) {
        return json_decode($value, true);
    }

    // 将content在展示时转换由json字符串转换为数组
    public function getContentAttribute($value) {
        return json_decode($value, true);
    }

    // 将project在展示时替换成项目名称
    public function getProjectAttribute($value) {
        return ProjectService::getNameById($value);
    }
}
