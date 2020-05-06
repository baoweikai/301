<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\AdminUser;
use think\facade\Json;

class Identity extends Controller{
    private $adminUserModel;
    public function initialize(){
        $this->adminUserModel = new AdminUser();
    }
    public function login()
    {
		if (request()->isPost()) {
            $post_data = input('post.');

            //验证参数
            $validate = $this->validate($post_data, 'Login');
            if (true !== $validate) {
               return $this->error($validate);
            }
            $result =  $this->adminUserModel->login($post_data['username'],$post_data['password']);
            if(!$result) {
                return $this->error($this->adminUserModel->getError());
            }
            return $this->success($this->adminUserModel->getError(),$result);
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
                $post_data = input('post.');
                $result = $this->adminUserModel->saveInfo($post_data);
                if(!$result){
                    return $this->error($this->adminUserModel->getError());
                }
                return $this->success($this->adminUserModel->getError(), $result);
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
                $post_data = input('post.');
                $result = $this->adminUserModel->saveUserPwd($post_data);
                if (!$result) {
                    return $this->error($this->adminUserModel->getError());
                }
                return $this->success($this->adminUserModel->getError(), $result);
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
            $this->adminUserModel->logout();
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