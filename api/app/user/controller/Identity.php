<?php
namespace app\user\controller;

use app\common\model\User;

class Identity extends \core\Controller
{
    public function login()
    {

		if (request()->isPost()) {
            $post = input('post.');
            //验证参数
            $validate = $this->validate($post, 'Login');
            if (true !== $validate) {
               return $this->error($validate);
            }
            $user = new User();
            $result = $user->login($post['account'],$post['password']);
            if(!$result) {
               return $this->error($user->error);
            }
           
            return $this->success($user->error,$result);
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
                    $post = input('post.');
                    $result = $this->userModel->saveUserPwd($post);
                    if (!$result) {
                        return $this->error($this->userModel->error);
                    }
                    return $this->success($this->userModel->error, $result);
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
                return $this->success('退出成功！', url('/identity/login'));
            } else {
                return $this->error('您还未登陆哟', url('/identity/login'));
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