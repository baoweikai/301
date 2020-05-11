<?php
namespace app\common\model;

class User extends \core\Model
{
    protected $type = [
        'is_use' => 'integer',
        'status' => 'integer',
        'login_at' => 'timestamp:m-d H:i',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['account', 'phone', 'password', 'login_at', 'login_ip', 'status'];
    protected $filter = ['account', 'status', 'login_ip'];  // 搜索项
}