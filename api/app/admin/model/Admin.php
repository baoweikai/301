<?php
namespace app\admin\model;

use core\Model;

class Admin extends Model  
{
    use \traits\Identity;
    protected $table = 'auth_admin';     // 系统管理员表
    protected $type = [
        'login_at' => 'integer',
    ];
    protected $fillable = ['username', 'password', 'role_id', 'status'];
    protected $filter = ['username', 'role_id', 'status'];  // 搜索项
    /*
     * 验证规则
     */
    protected $rule = [
        'username'  => 'require|max:30|chsDash|min:5|unique:auth_admin',
        'password'   => 'alphaDash|min:6|max:20',
        'repasswd'   => 'confirm:password',
        'mobile'   => 'number|length:11|unique:auth_admin',
        'email'   => 'email|max:32|unique:auth_admin',
    ];
    /*
     * 角色
     */
    public function role(){
        return $this->belongsTo(Role::class, 'role_id')->bind(['role_name' => 'title', 'rules' => 'rules']);
	}
	/*
     * 可否删除
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function ableDel()
    {
        if (in_array('10000', explode(',', input('get.id/d')))) {
            $this->error = '系统超级账号禁止删除！';
            return false;
        }
        return true;
    }
}