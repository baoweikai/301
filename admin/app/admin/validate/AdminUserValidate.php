<?php
namespace app\admin\validate;

use think\facade\Validate;


class AdminUserValidate extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'group_id' => 'require|gt:0',
        'username' => 'require|length:4,25|unique:AdminUser',
        'nickname' => 'require|unique:AdminUser',
        'password' => 'require|length:6,15',
        'status' => 'require|in:0,1'

    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'group_id.require' => '请选择所属用户组',
        'group_id.gt' => '所属用户组必须大于0',
        'username.require' => '请输入用户名',
        'username.length' => '用户名在4到25个字符之间',
        'username.unique' => '用户名已存在,请重新输入',
        'nickname.require' => '请输入昵称',
        'nickname.require' => '昵称已经存在,请重新输入',
        'password.require' => '请输入密码',
        'password.length' => '密码在6到25个字符之间',
        'status.require' => '请选择状态',
        'status.in' => '状态只是正常与禁用',
    ];

}


