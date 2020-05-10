<?php
namespace app\common\model;

class Cate extends \core\Model
{
    use \traits\Identity;
    protected $table = 'auth_admin';     // 系统管理员表
    //定义属性
    protected $type = [
        'status' => 'integer',
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'update_at' => 'timestamp:Y-m-d H:i:s',
    ];
    protected $fillable = ['name', 'status'];
    protected $filter = ['name', 'status'];  // 搜索项
}