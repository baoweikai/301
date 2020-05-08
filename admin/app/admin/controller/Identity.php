<?php
namespace app\admin\controller;

use app\admin\model\AuthAdmin;
use thans\jwt\facade\JWTAuth;

class Identity extends \app\admin\Controller{
    private $admin;
    protected function initialize(){
        $this->admin = new AuthAdmin();
    }
    public function login()
    {
		if (request()->isPost()) {
            $post = input('post.');

            $model = new AuthAdmin();
            $user = $model->login($post['username'], $post['password']);
            if($user){ 
                // 获取token
                try{
                    $token = JWTAuth::builder(['id' => $user['id'], 'account' => $user['username']]);
                    cookie('token', $token);
                    $admin = AuthAdmin::with('role')->where('id', $user['id'])->find();

                    cache('AuthRule', explode(',', $admin->rules));  //参数为用户认证的信息，请自行添加
                } catch(\Exception $e){
                    return $this->error($e->getMessage());
                }
            } else {
                return $this->error($model->error);
            }
            return $this->success($user);
        }else{
            if (is_login()) {
                return redirect('/');
            }else{
               return $this->fetch();
            }
        }
    }
    //信息编辑
    public function saveInfo()
    {
        $info = cache("user_auth_" . session('admin'));
        if (request()->isPost()) {
            try{
                $post = input('post.');
                $result = $this->admin->saveInfo($post);
                if(!$result){
                    return $this->error($this->admin->error);
                }
                return $this->success($this->admin->error, $result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }else{
            $this->result['title'] = '信息编辑';
            $this->result['info'] = json_encode($info,true);
            return $this->fetch('saveInfo');
        }
    }

    //修改密码
    public function savePwd()
    {
        $info = cache("user_auth_" . session('admin'));
        if (request()->isPost()) {
            try {
                $post = input('post.');
                $result = $this->admin->saveUserPwd($post);
                if (!$result) {
                    return $this->error($this->admin->error);
                }
                return $this->success($this->admin->error, $result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

        }else{
            $this->result['title'] = '密码修改';
            $this->result['info'] = json_encode($info, true);
            return $this->fetch('pwd');
        }
    }

    public function logout()
    {
        if (is_admin_login()) {
            $this->admin->logout();
			return $this->success('退出成功！', url('/identity/login'));
		} else {
			return $this->error('您还未登陆哟', url('/identity/login'));
		}
    }

    
    
    //超时
    public function check_timeout() {
		if (!is_admin_login()) {
			return $this->error('亲,请重新登陆~');
		} else {
			return $this->success();
		}
	}
}
?>