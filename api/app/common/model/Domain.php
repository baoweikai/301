<?php
namespace app\common\model;

class Domain extends \core\Model
{
    // protected $table = 'domain';     // 系统管理员表
    //定义属性
    protected $type = [
        'user_id' => 'integer',
        'is_use' => 'integer',
        'status' => 'integer',
        'is_param' => 'integer',
        'is_open' => 'integer',
        'start_time' => 'array',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['shield_url', 'jump_url', 'cited_url', 'percent', 'is_param', 'is_open', 'start_time', 'status'];
    protected $filter = ['shield_url', 'jump_url', 'is_param', 'is_open', 'status'];  // 搜索项
}