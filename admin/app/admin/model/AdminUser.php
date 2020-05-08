<?php
namespace app\admin\model;

use think\Model;

class AdminUser extends Model  
{

	protected $autoWriteTimestamp = true;
	protected $type = [
        // 设置create_time为时间戳类型（整型）
		'create_time' => 'timestamp:Y-m-d H:i:s',
		'update_time' => 'timestamp:Y-m-d H:i:s',
		'last_login_time' => 'timestamp:Y-m-d H:i:s',
	];
    //对密码加密
    protected function setPasswordAttr($value) 
    {
		return md5(Sha1($value));
	}
	protected function getStatusAttr($value, $data)
	{
		$status_arr = [0 => '禁用', 1 => '正常'];
		return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
	}

    /**
	 * 用户登录认证
	 * @param  string  $username 用户名
	 * @param  string  $password 用户密码
	 */
     public  function login($username,$password)
     {
        $map['username'] = $username;
        /* 获取用户数据 */
        $user = $this->where($map)->find();
        if ($user) {
			$user = $user->toArray();
        }
        
        if (is_array($user) && $user['status']['val'] == 1) {
			/* 验证用户密码 */
			if (md5($password) == $user['password']) {
				$this->autoLogin($user);
				return $this->error = "登陆成功";
				return $user;
			} else {
				return $this->error = "用户不存在或密码错误";
				return false;
			}
        }else{
            return $this->error = "用户不存在或被禁用";
            return false;
        }
     }



	/**
	 * 注销当前用户
	 * @return void
	 */
	public function logout() 
	{
		cache('user_auth_'.session('admin'), NULL);
		cache('user_auth_sign_'.session('admin'), NULL);
		cache('adminRule',NULL);
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
			'username' => $user['username'],
			'nickname' => $user['nickname'] ? $user['nickname'] : $user['username'],
			'last_login_ip' => $ip,
			'last_login_time' => $user['last_login_time'],
			'status' => $user['status'],
        ];
        
        //更新登录信息
		$data = [
			'id' => $user['id'],
			'login_tre' => $user['login_tre']+1,
			'last_login_time' => time(),
			'last_login_ip' => $ip,
        ];

		session('admin',time());
		$this->where(array('id' => $user['id']))->update($data);
		cache('user_auth_'.session('admin'), $auth, 3600);
		cache('user_auth_sign_'.session('admin'), data_auth_sign($auth), 3600);
	 }
	 //修改用户信息
	 public function saveInfo($post)
	 {
		$map['id'] = intval($post['id']);
		
		$result = $this->where($map)->update($post);
		if(!$result) {
			return $this->error = '修改失败';
			return false;
		}

		$this->saveUserCache($map);
		return $this->error ='更新成功';
		return $result;

	 }

	 //修改用户密码
	 public function saveUserPwd($post)
	 {
		$map['id'] = intval($post['id']);
		$pass = trim($post['pass']);//旧密码
		$password = trim($post['password']);//新密码
		$pwd = trim($post['pwd']);//确认密码
		$user = $this->where($map)->find();
		if($user) {
			$user = $user->toArray();
		}

		if(md5(Sha1($pass)) != $user['password']) {
			return $this->error = '旧密码错误';
			return false;
		}
		
		if(mb_strlen($password, 'utf8') < 6 && mb_strlen($password, 'utf8') >15) {
			return $this->error = '密码长度在6-15个字符之间';
			return false;
		}

		if($pwd != $password) {
			return $this->error = '新密码与确认密码不一致';
			return false;
		}

		if(md5(Sha1($pwd)) == $user['password']) {
			return $this->error = '新密码与旧密码一致';
			return false;
		}

		$data['password'] = md5(Sha1($pwd));
		$result = $this->where($map)->update($data);
		if(!$result) {
			return $this->error ='密码修改失败';
			return false;
		}
		return $this->error = '密码修改成功';
		return true;

	 }

	 //更新后台用户缓存
	 public function saveUserCache($map)
	 {
		$user = $this->where($map)->find();
		$ip = request()->ip();
		$auth = [
			'id' => $user['id'],
			'username' => $user['username'],
			'nickname' => $user['nickname'] ? $user['nickname'] : $user['username'],
			'last_login_ip' => $ip,
			'last_login_time' => $user['last_login_time'],
			'status' => $user['status'],
		];
		cache('user_auth_' . session('admin'), $auth, 3600);
		cache('user_auth_sign_' . session('admin'), data_auth_sign($auth), 3600);
	 }
}

?>