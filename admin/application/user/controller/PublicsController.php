<?php
namespace app\user\controller;

use app\common\model\UserModel;
use think\Controller;
use think\Json;

class PublicsController extends Controller
{
    private $userModel;
    public function initialize(){
        $this->userModel = new UserModel();
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
           $result =  $this->userModel->login($post_data['account'],$post_data['password']);
           if(!$result) {
               Json::fail($this->userModel->getError());
           }
           
         Json::success($this->userModel->getError(),$result);
        }else{
            if (is_user_login()) {
                $this->redirect('/');
            }else{
               return $this->fetch();
            }
        }
    }


        //修改密码
        public function savePwd()
        {
            $info = cache("user_auth_" . session('UserAdmin'));
            if (request()->isPost()) {
                try {
                    $post_data = input('post.');
                    $result = $this->userModel->saveUserPwd($post_data);
                    if (!$result) {
                        Json::fail($this->userModel->getError());
                    }
                    Json::success($this->userModel->getError(), $result);
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
            if (is_user_login()) {
                $this->userModel->logout();
                $this->success('退出成功！', url('/Publics/login'));
            } else {
                $this->error('您还未登陆哟', url('/Publics/login'));
            }
        }
    
        
        
        //超时
        public function check_timeout() {
            if (!is_user_login()) {
                Json::fail('亲,请重新登陆~');
            } else {
                Json::success('OK');
            }
        }
}
?>