<?php
namespace app\model;

class Group extends \core\Model
{
    //定义属性
    protected $type = [
        'status' => 'integer',
        'is_default'   => 'integer',
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'update_at' => 'timestamp:Y-m-d H:i:s',
    ];
    protected $fillable = ['name', 'status', 'is_default'];
    protected $filter = ['name', 'status', 'is_default'];  // 搜索项
    protected $rule = [
        'name'  => 'require|unique:cate',
        'is_default'   => 'integer',
        'status'   => 'integer',
    ];
}