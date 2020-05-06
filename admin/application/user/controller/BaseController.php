<?php
namespace app\user\controller;
use think\Controller;

class BaseController extends Controller
{
    public function initialize()
    {
        define('USER_UID', is_user_login());
		define('MODULE_NAME',request()->module());
		define('CONTROLLER_NAME',request()->controller());
		define('ACTION_NAME', request()->action());
        if (!USER_UID && (CONTROLLER_NAME != "Publics" || CONTROLLER_NAME != "publics")) {
			//转到登录页面
			$_SESSION["refurl"] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
			$this->redirect("/Publics/login");
        }
        
        $this->assign("now_user", cache("user_auth_" . session('UserAdmin')));
    }


    /**
	 * 基本操作 列表
	 */

	 public function  index()
	 {
		if (request()->isPost()) {
			try {
				//列表过滤器，生成查询Map对象
				$map = [];
				//有无列表前置操作
				if (method_exists($this, '_before_index')) {
					$this->_before_index();
				}

				//查询条件
				if (method_exists($this, '_filter')) {
					$this->_filter($map);
				}
				//排序字段
				if (method_exists($this, '_order')) {
				  $this->_order($order);
				}else{
					$order = "id desc";
				}
				if (!empty($this->modelname)) {
					$name = $this->modelname;
				} else {
					$name = CONTROLLER_NAME;
				}
				$model = model($name);
				$field = '*';
				if (!$model) {
					Json::fail('模型' . CONTROLLER_NAME . '未找到');
				} 
				$result = $this->_listJson($name, $map, $field, $order);
				Json::success('ok', $result);
			} catch (\Exception $e) {
				Json::fail($e->getMessage());
			}
		}else{
			return $this->fetch();
		}
	
	 }


	 //添加
	 public function  add()
	 {
		if (request()->isPost()) {
			try {
				$model = model(CONTROLLER_NAME);
				$post_data = request()->post();
				$validate = validate(CONTROLLER_NAME);
				if (!$validate->check($post_data)) {
					Json::fail($validate->getError());
				}
				$result = $model->create($post_data);
				if(!$result) {
					Json::fail('添加失败');
				}
			Json::success('添加成功',$result);
			} catch (\Exception $e) {
				Json::fail($e->getMessage());
			}

		}else{
			$this->assign('title', '添加');
			$this->assign('info', 'null');
			return $this->fetch('form');
		}
	 }

	 //编辑
	public function edit()
	{
		$model = model(CONTROLLER_NAME);
		if (request()->isPost()) {
			try {

				$post_data = request()->post();
				$validate = validate(CONTROLLER_NAME);
				if (!$validate->check($post_data)) {
					Json::fail($validate->getError());
				}
				$result = $model->update($post_data);
				if (!$result) {
					Json::fail('编辑失败');
				}
				Json::success('编辑成功', $result);
			} catch (\Exception $e) {
				Json::fail($e->getMessage());
			}

		} else {
			$id =input($model->getPk());
			$info = $model->get($id);
			$this->assign('title', '编辑');
			$this->assign('info', json_encode($info, true));
			return $this->fetch('form');
		}
	}

	//逻辑删除
	public function del()
	{
		$id = input('id');

		if (empty($id)) {
			Json::fail('请选择要操作的数据！');
		}
		if (!empty($this->modelname)) {
			$name = $this->modelname;
		} else {
			$name = CONTROLLER_NAME;
		}
		$map["id"] = ['in', $id];
		$info = model($name)->get($map);
		$info = $info->toArray();
		if(array_key_exists('litpic',$info)){
			if($info['litpic']) {
				unlink('.'.$info['litpic']);
			}
		}
		$result = model($name)->where($map)->delete();
		if(!$result) {
			Json::fail('删除失败!');
		}
		Json::success('删除成功!');
	}
	//更改状态
	public function setStatus()
	{
		if (request()->isPost()) {
			try {
				$model = model(CONTROLLER_NAME);
				$post_data = request()->post();
				if(!array_key_exists('id',$post_data)){
					Json::fail('ID不存在');
				}
				if (!array_key_exists('status', $post_data)) {
					Json::fail('状态错误！！');
				}
				$result = $model->update($post_data);
				if (!$result) {
					Json::fail('设置失败');
				}
				Json::success('设置成功', $result);
			}catch(\Exception $e){
				Json::fail($e->getMessage());
			}
		}else{
		   Json::fail('错误请求');	
		}
	}


	//排序修改
	public function  setSort()
	{
		if (request()->isPost()) {
			$model = model(CONTROLLER_NAME);
			$post_data = request()->post();
			if (!array_key_exists('id', $post_data)) {
				Json::fail('ID不存在');
			}
			if (!array_key_exists('sort', $post_data)) {
				Json::fail('排序错误！！');
			}
			$id = intval($post_data['id']);
			$data['sort'] = intval($post_data['sort']);
			$result = $model->where(['id'=>$id])->update($data);
			if (!$result) {
				Json::fail('修改失败');
			}
			Json::success('修改成功', $result);
		}else{
			Json::fail('错误请求');	
		}	
	}

	//单表查询
	protected  function _listJson($table,$map, $field = "*", $order = 'id desc') 
	{
		$model = model($table);
		$list_row = input('page_size', 10);
		$page = input('page', 1);
		$list = $model->where($map)->field($field)->order($order)->paginate($list_row, false, ['query' => request()->param()]);
		return $list;
	}

	
	//多表查询
    public function getListJson($table,$map, $join_arr, $field = "a.*", $order = 'a.id desc') {
        $model = model($table);
		$list_row = input('page_size', 10);
		$page = input('page', 1);
        $list = $model->alias('a')->where($map)->join($join_arr)->field($field)->order($order)->paginate($list_row, false, ['query' => request()->param()]);
        // echo $model->getlastsql();exit;
		return $list;
	}


	//定义空方法时跳转到404错误页面
    public function _empty($e)
    {
		Json::fail('错误的方法');
    }
}

?>