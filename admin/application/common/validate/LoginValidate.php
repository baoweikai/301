<?php
namespace app\common\validate;

use think\Validate;

class  LoginValidate extends  Validate{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'account' => 'require|max:15',
        'password' => 'require|max:20',
        'vercode'  => 'require|captcha',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected $message = [
        'account.require' => '登录名必须',
        'account.max'     => '登录名最多不能超过15个字符',
        'password.require' => '密码必须',
        'password.max'     => '密码最多不能超过20个字符',
        'vercode.require'  => '验证码能为空',
        'vercode.captcha'  => '验证码不正确，请重新输入',
    ];
}


