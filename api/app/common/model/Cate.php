<?php
namespace app\common\model;

class Cate extends \core\Model
{
    //定义属性
    protected $type = [
        'status' => 'integer',
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'update_at' => 'timestamp:Y-m-d H:i:s',
    ];
    protected $fillable = ['name', 'status'];
    protected $filter = ['name', 'status'];  // 搜索项
}