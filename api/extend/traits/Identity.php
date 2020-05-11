<?php

namespace traits;

use thans\jwt\facade\JWTAuth;
use think\exception\ValidateException;
/*
 * 身份服务类(登录,注册)
 * Class Identity
 * @date 2017/03/22 15:32
 */
trait Identity
{
    public $accountField = 'username';
    public $passwordField = 'password';
    public $captchaField = 'captcha';
    public $repeatField = 'repeat';
    public $mobileField = 'mobile';  // 手机号
    public $snsField = 'snscode';  // 短信验证码

    public $resetType = 0;  //重置类型,0 密码, 1 手机 2 邮箱
    public $false_count = 0;  // 登录失败次数
    public $sessionName = 'user';

    public $scene = 'login';  // 场景

    /*
     * 登录
     */
    public function login($account, $password, $captcha){
        $data = [
            $this->accountField => $account,
            $this->passwordField => $password,
            $this->captchaField => $captcha,
        ];
        // 用户信息验证
        try {
            validate(\validate\User::class)->batch(true)->scene('login')->check($data);
            $user = self::where($this->accountField, $account)->find();

            if($user->password !== $password){
                $this->error = '密码错误';
                return false;                  
            }
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            $this->error = $e->getMessage();
            return false;
        }
        
        // 获取token
        try{
            $token = JWTAuth::builder(['id' => $user->id, $this->accountField => $account]);
            cache($token, ['id' => $user->id, 'nickname' => $user->nickname], 7200);
            return ['access_token' => $token];//参数为用户认证的信息，请自行添加
        } catch(\Exception $e){
            $this->error = $e->getMessage();
            return false;
        }
    }
    /*
     * 短信
     */
    public function snslogin($mobile, $sns){
        // 输入数据效验
        $data = [
            $this->mobileField => $mobile,
            $this->snsField => $sns,
        ];
        // 用户信息验证
        $validate = new \validate\User;

        if (!$validate->scene('snslogin')->check($data)) {
            $this->error = $validate->getError();
            return false;
        }

        $user = self::where([$this->mobileField => $mobile])->find();
        if(empty($user['state'])){
            $this->error = '账号已经被禁用，请联系管理!';
            return false;
        }
        /*
        if($user[$this->snsField] !== $sns){
            $this->error = '短信验证码错误，请重新登入!';
            return false;
        }*/

        // 更新登录信息
        $user->save(['login_at' => time()]);

        // self::log('系统管理', '用户登录系统成功');
        return true;
    }

    /*
     * 注册
     */
    public function signup($base, $extend = []){
        // 输入数据效验
        $data = [
            $this->accountField => $base[$this->accountField],
            $this->passwordField => $base['password'],
            $this->repeatField => $base['repeat'],
        ];

        // 用户信息验证
        $validate = new \validate\UserValidate;
        if (!$validate->scene('mobile')->check($data)) {
            $this->error = $validate->getError();
            return false;
        }

        $user = self::create([
            $this->accountField => $base[$this->accountField],
            $this->passwordField => md5($base['password']),
            'login_at' => time()
        ] + $extend);

        $builder = new Builder();
        //设置header和payload，以下的字段都可以自定义
        $builder->setIssuer(request()->host()) //发布者
        ->setAudience(request()->host()) //接收者
        ->setId("wwer209fi3498i09F9KIV0o", true) //对当前token设置的标识
        ->setIssuedAt(time()) //token创建时间
        ->setExpiration(time() + 3600 * 720) //过期时间
        ->setNotBefore(time() + 15) //当前时间在这个时间前，token不能使用
        ->set('User', $user->toArray()); //自定义数据
        //设置签名
        $builder->sign(new Sha256(), config('jwt.key'));
        //获取加密后的token，转为字符串
        session('User', $user->toArray());
        session('access_token', (string)$builder->getToken());

        return $user->getData() + ['access_token' => session('access_token')];
    }
}
