<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\AdminUserModel;
use think\Json;

class PublicsController extends Controller{
    private $adminUserModel;
    public function initialize(){
        $this->adminUserModel = new AdminUserModel();
    }
    public function  login()
    {
		if (request()->isPost()) {
            $post_data = input('post.');

            //验证参数
            $validate = $this->validate($post_data, 'Login');
            if (true !== $validate) {
               Json::fail($validate);
            }
            $result =  $this->adminUserModel->login($post_data['username'],$post_data['password']);
            if(!$result) {
                Json::fail($this->adminUserModel->getError());
            }
            Json::success($this->adminUserModel->getError(),$result);
        }else{
            if (is_login()) {
                $this->redirect('/');
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
                    Json::fail($this->adminUserModel->getError());
                }
                Json::success($this->adminUserModel->getError(), $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        }else{
            $this->assign('title', '信息编辑');
            $this->assign('info',json_encode($info,true));
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
                    Json::fail($this->adminUserModel->getError());
                }
                Json::success($this->adminUserModel->getError(), $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }

        }else{
            $this->assign('title', '密码修改');
            $this->assign('info', json_encode($info, true));
            return $this->fetch('pwd');
        }
    }

    public function logout()
    {
        if (is_login()) {
            $this->adminUserModel->logout();
			$this->success('退出成功！', url('/Publics/login'));
		} else {
			$this->error('您还未登陆哟', url('/Publics/login'));
		}
    }

    
    
    //超时
    public function check_timeout() {
		if (!is_login()) {
			Json::fail('亲,请重新登陆~');
		} else {
			Json::success('OK');
		}
	}
}
?>