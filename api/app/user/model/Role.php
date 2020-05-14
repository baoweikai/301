<?php
namespace app\admin\model;

use core\Model;

class Role extends Model
  {
    protected $table = 'auth_role';     // 系统管理员表
    protected $fillable = ['title', 'memo', 'status'];
    protected $filter = ['title', 'status'];  // 搜索项

    protected $type = [
        // 'rules'      =>  'array',
    ];
}
