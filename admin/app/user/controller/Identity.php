<?php
namespace app\user\controller;

use app\common\model\User;
// use think\response\Json;

class Identity extends \app\user\Controller
{
    private $userModel;
    protected function initialize(){
        parent::initialize();
        $this->userModel = new User();
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
            $user = new User();
            $result = $user->login($post_data['account'],$post_data['password']);
            if(!$result) {
               return $this->error($user->getError());
            }
           
            return $this->success($user->getError(),$result);
        }else{
            if (is_user_login()) {
                return redirect('/');
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
                        return $this->error($this->userModel->getError());
                    }
                    return $this->success($this->userModel->getError(), $result);
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
            if (is_user_login()) {
                $this->userModel->logout();
                return $this->success('退出成功！', url('/Publics/login'));
            } else {
                return $this->error('您还未登陆哟', url('/Publics/login'));
            }
        }
    
        
        
        //超时
        public function check_timeout() {
            if (!is_user_login()) {
                return $this->error('亲,请重新登陆~');
            } else {
                return $this->success();
            }
        }
}
?>