<?php
namespace app\common\model;

class Cate extends \core\Model
{
    //定义属性
    protected $type = [
        'status' => 'integer',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['name', 'status'];
    protected $filter = ['name', 'status'];  // 搜索项
    protected $rule = [
        'name'  => 'require|unique:cate',
        'status'   => 'integer',
    ];
}