<?php
namespace app\common\model;

class User extends \core\Model
{
    protected $autoWriteTimestamp = true;    // 自动时间戳
    protected $type = [
        'is_use' => 'integer',
        'status' => 'integer',
        'login_at' => 'timestamp:m-d H:i',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['account', 'phone', 'password', 'login_at', 'login_ip', 'status'];
    protected $filter = ['account', 'status', 'login_ip'];  // 搜索项
    // 状态搜索
    public function searchAccountAttr($query, $value, $data)
    {
        $value !== null && $value !== '' && $query->where('account', 'like', $value . '%');
    }
    /*
     * 验证规则
     */
    protected $rule = [
        'account'  => 'require|max:30|chsDash|min:5|unique:user',
        'password'   => 'alphaDash|min:6|max:20',
        'phone'   => 'number|length:11|unique:auth_admin'
    ];
}