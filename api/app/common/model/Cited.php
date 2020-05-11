<?php
namespace app\common\model;

class Cited extends \core\Model
{
    // protected $table = 'cited';     // 系统管理员表
    //定义属性
    protected $type = [
        'domain_id' => 'integer',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['name', 'domain_id', 'ip'];
    protected $filter = ['name', 'domain_id'];  // 搜索项
    protected $rule = [
        'domain_id'  => 'require|integer',
        'ip'   => 'require',
    ];
}