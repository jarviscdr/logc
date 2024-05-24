<?php

declare(strict_types=1);

namespace app\model;

use support\Model;

/**
 * 用户表
 *
 * @author Jarvis
 * @date   2024-05-18 00:52
 */
class User extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

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
    protected $fillable = ['username', 'nickname', 'password', 'avatar', 'email', 'mobile', 'status'];
}
