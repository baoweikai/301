<?php

namespace app\admin\controller;

use core\Controller;
use thans\jwt\facade\JWTAuth;
use think\facade\Request;
use app\admin\model\Admin as Model;
/*
 * 身份验证管理
 *
 * @icon 
 * @remark 一个管理员可以有多个角色组,左侧的菜单根据管理员所拥有的权限进行生成
 */
class Identity extends Controller
{
    /*
     * 用户登录
     * @return string
     */
    public function login()
    {
        if (request()->isGet()) {
            $this->result['meta']['title'] = '用户登录';
            $this->result['captcha']['img'] = captcha_src();
            return $this->success($this->result);
        }

        $user = new Model;
        //渲染配置信息
        // $this->result['is_captcha'] = config("site.fastadmin.login_captcha");

        $res = $user->login(input('post.username/s'), input('post.password/s'), input('post.captcha/s'));

        if($res){
            return $this->success($this->result + $res, '登录成功');
        }else{
            return $this->error($user->error, 241);
        }
    }
    /*
     *  短信登录
     * @return string
     */
    public function snslogin()
    {
        if (request()->isGet()) {
            $this->result['meta']['title'] = '用户登录';
            return $this->success($this->result);
        }

        $user = new Model;
        $user->accountField = 'mobile';

        $res = $user->snslogin(input('post.mobile/s'), input('post.snscode/s'));
        if($res){
            $this->success($this->result + $res, '登录成功');
        }else{
            $this->error($user->getError(), 241);
        }
    }

    /*
     * 用户注册
     * @return string
     * @throws \think\Exception
    */
    public function signup()
    {
        if ($this->request->isGet()) {
            $this->result['meta']['title'] = '用户注册';
            return $this->success($this->result);
        }

        $user = new Model;
        $user->accountField = 'mobile';

        $res = $user->signup([$user->accountField => input('post.' . $user->accountField . '/s'), 'password' => input('post.password/s'), 'repeat' => input('post.repeat/s')]);
        if($res){
            $this->success($this->result + $res, '注册成功');
        }else{
            $this->error($user->getError(), 241);
        }
    }
    /*
     * 退出登录
     */
    public function logout()
    {
        $token = Request::header('Authorization');
        JWTAuth::invalidate($token);
        $this->success(['status' => 1]);
    }
}
