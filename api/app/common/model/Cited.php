<?php
namespace app\common\model;

class Cited extends \core\Model
{
    // protected $table = 'cited';     // 系统管理员表
    //定义属性
    protected $type = [
        'domain_id' => 'integer',
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'update_at' => 'timestamp:Y-m-d H:i:s',
    ];
    protected $fillable = ['name', 'domain_id', 'ip'];
    protected $filter = ['name', 'domain_id'];  // 搜索项
}