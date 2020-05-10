<?php
namespace app\common\model;

class Domain extends \core\Model
{
    // protected $table = 'auth_admin';     // 系统管理员表
    //定义属性
    protected $type = [
        'is_use' => 'integer',
        'status' => 'integer',
        'start_time' => 'array',
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'update_at' => 'timestamp:Y-m-d H:i:s',
    ];
    protected $fillable = ['shield_url', 'jump_url', 'cited_url', 'percent', 'is_param', 'is_open', 'start_time', 'status'];
    protected $filter = ['shield_url', 'jump_url', 'is_param', 'is_open', 'status'];  // 搜索项
}