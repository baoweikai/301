<?php
namespace app\common\model;

class User extends \think\Model
{
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    //定义属性
    protected $type = [
        'status' => 'integer',
        'create_time' => 'timestamp:Y-m-d H:i:s',
        'update_time' => 'timestamp:Y-m-d H:i:s',
    ];

    protected function getStatusAttr($value, $data)
    {
        $status_arr = [0 => '禁用', 1 => '正常'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }

        /**
	 * 用户登录认证
	 * @param  string  $account 用户名
	 * @param  string  $password 用户密码
	 */
    public  function login($account,$password)
    {
       $map['account'] = $account;
       /* 获取用户数据 */
       $user = $this->get($map);
       if ($user) {
           $user = $user->toArray();
       }
       
       if (is_array($user) && $user['status']['val'] == 1) {
           /* 验证用户密码 */
           if (emcryPwd($password) == $user['password']) {
               $this->autoLogin($user);
               $this->error = "登陆成功";
               return $user;
           } else {
               $this->error = "用户不存在或密码错误";
               return false;
           }
       }else{
           $this->error = "用户不存在或被禁用";
           return false;
       }
    }

    /**
	 * 自动登录用户
	 * @param  integer $user 用户信息数组
	 */
    private function autoLogin($user) 
    {
       $ip = request()->ip();
       //记录登录SESSION和COOKIES 
       $auth = [
           'id' => $user['id'],
           'account' => $user['account'],
           'last_login_ip' => $ip,
           'login_time' => $user['login_time'],
           'status' => $user['status'],
       ];
       
       //更新登录信息
       $data = [
           'id' => $user['id'],
           'login_tre' => $user['login_tre']+1,
           'login_time' => time(),
           'last_login_ip' => $ip,
       ];

       session('UserAdmin',time());
       $this->where(array('id' => $user['id']))->update($data);
       cache('user_auth_'.session('UserAdmin'), $auth, 36000);
       cache('user_auth_sign_'.session('UserAdmin'), data_auth_sign($auth), 36000);
    }

	 //修改用户密码
	 public function saveUserPwd($post_data)
	 {
		$map['id'] = intval($post_data['id']);
		$pass = trim($post_data['pass']);//旧密码
		$password = trim($post_data['password']);//新密码
		$pwd = trim($post_data['pwd']);//确认密码
		$user = $this->get($map);
		if($user) {
			$user = $user->toArray();
		}

		if(emcryPwd($pass) != $user['password']) {
			$this->error = '旧密码错误';
			return false;
		}
		
		if(mb_strlen($password, 'utf8') < 6 && mb_strlen($password, 'utf8') >15) {
			$this->error = '密码长度在6-15个字符之间';
			return false;
		}

		if($pwd != $password) {
			$this->error = '新密码与确认密码不一致';
			return false;
		}

		if(emcryPwd($pwd) == $user['password']) {
			$this->error = '新密码与旧密码一致';
			return false;
		}

		$data['password'] = emcryPwd($pwd);
		$result = $this->where($map)->update($data);
		if(!$result) {
			$this->error ='密码修改失败';
			return false;
		}
		$this->error = '密码修改成功';
		return true;

     }
     

    /**
	 * 注销当前用户
	 * @return void
	 */
	public function logout() 
	{
		cache('user_auth_'.session('UserAdmin'), NULL);
		cache('user_auth_sign_'.session('UserAdmin'), NULL);
	}



}

?>