<?php

namespace validate;

use core\Validate;

/*
 * 基础数据服务
 * Class DataService
 * @package service
 * @date 2017/03/22 15:32
 */
class User extends Validate
{
    protected $table = 'auth_admin';
    /*
     * 验证规则
     */
    protected $rule = [
        'account|账号'  => "require|max:50|min:2",
        'username|用户名'  => "require|max:50",
        'mobile|手机号'   => 'require|number|length:11',
        'password|密码'   => 'require|min:6|max:36',
        'snscode|短信验证码' => 'require|min:4|max:6',
        'repeat|确认密码'   => 'require|confirm:password',
        'captcha|验证码'=>'captcha|min:4|max:6',
        'email|电子邮箱'=>'email',
        'oldpass|旧密码' => 'require|min:8|max:16',
    ];
    /*
     * 验证场景
     */
    protected $scene = [
        'reset'  =>  ['access_token','repeat'],       // 重置密码
        'snsreset'  =>  ['username','password'],  // 短信重置密码
        'emailreset'  =>  ['username','password'],  // 邮箱重置密码
        'mobilereset'  =>  ['username','password'],  // 短信重置密码
        'modify'      => ['oldpass', 'password', 'repeat'],  // 修改密码
    ];

    // 短信登录 验证场景补充
    public function sceneSnslogin()
    {
        return $this->only(['mobile','snscode'])->append('mobile', "exist:{$this->table},mobile");
    }
    // 登录验证
    public function sceneLogin()
    {
        return $this->only(['username', 'password', 'captcha'])->append('username', "exist:{$this->table},username");
    }
    // 手机注册验证
    public function sceneMobile()
    {
        return $this->only(['mobile','password'])->remove('username', 'exist');
    }
    // 注册验证
    public function sceneSignup()
    {
        return $this->only(['username','password', 'repeat'])->append('mobile', "exist:{$this->table},mobile");
    }
    public function table($table){
        $this->table = $table;
        return $this;
    }
}
