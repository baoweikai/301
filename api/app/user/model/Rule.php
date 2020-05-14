<?php
namespace app\admin\model;

use core\Model;

class Rule extends Model
{
    protected $table = 'auth_rule';     // 系统管理员表
    protected $type = [
        'last_at' => 'integer',
    ];
    protected $fillable = ['title', 'href', 'icon', 'serial', 'pid'];  // 可编辑项
    protected $filter = ['title'];  // 搜索项
}