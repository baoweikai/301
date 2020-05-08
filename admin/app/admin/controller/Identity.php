<?php
namespace app\admin\controller;

use app\admin\model\AuthAdmin;
// use thans\jwt\facade\JWTAuth;

class Identity extends \app\admin\Controller{
    private $admin;
    protected function initialize(){
        $this->admin = new AuthAdmin();
    }
    public function login()
    {
		if (request()->isPost()) {
            $post = input('post.');

            //验证参数
            /*
            $validate = $this->validate($post, 'Login');
            if (true !== $validate) {
               return $this->error($validate);
            }
            */
            $model = new AuthAdmin();
            $result = $model->login($post['username'], $post['password']);
            if(!$result) {
                return $this->error($model->error);
            }
            return $this->success($model->error, $result);
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
        if (is_login()) {
            $this->admin->logout();
			return $this->success('退出成功！', url('/Publics/login'));
		} else {
			return $this->error('您还未登陆哟', url('/Publics/login'));
		}
    }

    
    
    //超时
    public function check_timeout() {
		if (!is_login()) {
			return $this->error('亲,请重新登陆~');
		} else {
			return $this->success();
		}
	}
}
?>