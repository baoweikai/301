<?php
namespace app\common\validate;

use think\facade\Validate;


class UserValidate extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'account' => 'require|unique:User',
        'password' => 'require|length:6,15',
        'attr_id' => 'require|number|gt:0',
        'status' => 'require|in:0,1',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'account.require' => '请填写账号',
        'account.unique' => '账号重复',
        'password.require' => '请输入密码',
        'password.length' => '密码在6到25个字符之间',
        'attr_id.require' => '请选择属性',
        'attr_id.number' => '属性ID必须为数字',
        'attr_id.gt' => '属性ID必须大于0',
        'status.require' => '请选择状态',
        'status.in' => '状态只能为0或1',
    ];


       /**
     * 验证场景 add-新增  edit-修改  login-登录   
     * 
     */
    protected  $scene = [
        'add' => ['account','nickname','password','status'],
        'edit' => ['attr_id','status'],
        'login' => ['account.require','password','vercode'],
        'setPwd' => ['oldpassword','password','repassword','vercode'],
    ];
}


