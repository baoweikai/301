<?php
namespace app\admin\controller;
use think\facade\Request;
use think\facade\Json;
use think\facade\Validate;


class Base extends \app\admin\Controller 
{
	protected $mod,$role,$system,$nav,$menudata,$cache_model,$categorys,$module,$moduleid,$adminRules,$HrefId;
	//protected $adminRules,$HrefId;
    /**
     * 后台控制器基础类
     */
    public function initialize()
    {
        define('UID', is_login());
		define('MODULE_NAME',request()->module());
		define('CONTROLLER_NAME',request()->controller());
		define('ACTION_NAME', request()->action());
        if (!UID && CONTROLLER_NAME != "Publics") {
			//转到登录页面
			$_SESSION["refurl"] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
			return redirect("/public/login");
		}
        //当前操作权限ID
        if( UID != 1){
			$this->HrefId = model('AdminRule')->where('href','/'.CONTROLLER_NAME .'/'.ACTION_NAME)->value('id');
            //当前管理员权限
			$map['a.id'] = UID;
			$join_arr =[
				0=>['AdminGroup g','a.group_id = g.id','LEFT'],
			];
			$rules = model('AdminUser')->alias('a')->join($join_arr)->where($map)->value('g.rules');
			$this->adminRules = explode(',',$rules);

            if($this->HrefId){
                if(!in_array($this->HrefId,$this->adminRules)){
                   return $this->error("您无此操作权限");
                }
            }
		}
			
		$this->cache_model=array('Module','AdminRule','Category','Posid','Field','System','cm');
		//$this->cache_model=array('Module','AdminRule','Category','System','cm');
        foreach($this->cache_model as $r){
            if(!cache($r)){
                savecache($r);
            }
        }
        $this->system = cache('System');
        $this->categorys = cache('Category');
		$this->module = cache('Module');
		$this->mod = cache('Mod');
		$this->rule = cache('AdminRule');
		$this->cm = cache('cm');

        $this->result['now_user'] = cache("user_auth_" . session('admin'));
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
					return $this->error('模型' . CONTROLLER_NAME . '未找到');
				} 
				$result = $this->_listJson($name, $map, $field, $order);
				return $this->success($result);
			} catch (\Exception $e) {
				return $this->error($e->getMessage());
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
					return $this->error($validate->getError());
				}
				$result = $model->create($post_data);
				if(!$result) {
					return $this->error('添加失败');
				}
			return $this->success('添加成功',$result);
			} catch (\Exception $e) {
				return $this->error($e->getMessage());
			}

		}else{
			$this->result['title'] = '添加';
			$this->result['info'] = 'null';
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
					return $this->error($validate->getError());
				}
				$result = $model->update($post_data);
				if (!$result) {
					return $this->error('编辑失败');
				}
				return $this->success('编辑成功', $result);
			} catch (\Exception $e) {
				return $this->error($e->getMessage());
			}

		} else {
			$id =input($model->getPk());
			$info = $model->get($id);
			$this->result['title'] = '编辑';
			$this->result['info'] = json_encode($info, true);
			return $this->fetch('form');
		}
	}

	//逻辑删除
	public function del()
	{
		$id = input('id');

		if (empty($id)) {
			return $this->error('请选择要操作的数据！');
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
			return $this->error('删除失败!');
		}
		return $this->success('删除成功!');
	}
	//更改状态
	public function setStatus()
	{
		if (request()->isPost()) {
			try {
				$model = model(CONTROLLER_NAME);
				$post_data = request()->post();
				if(!array_key_exists('id',$post_data)){
					return $this->error('ID不存在');
				}
				if (!array_key_exists('status', $post_data)) {
					return $this->error('状态错误！！');
				}
				$result = $model->update($post_data);
				if (!$result) {
					return $this->error('设置失败');
				}
				return $this->success('设置成功', $result);
			}catch(\Exception $e){
				return $this->error($e->getMessage());
			}
		}else{
		   return $this->error('错误请求');	
		}
	}


	//排序修改
	public function  setSort()
	{
		if (request()->isPost()) {
			$model = model(CONTROLLER_NAME);
			$post_data = request()->post();
			if (!array_key_exists('id', $post_data)) {
				return $this->error('ID不存在');
			}
			if (!array_key_exists('sort', $post_data)) {
				return $this->error('排序错误！！');
			}
			$id = intval($post_data['id']);
			$data['sort'] = intval($post_data['sort']);
			$result = $model->where(['id'=>$id])->update($data);
			if (!$result) {
				return $this->error('修改失败');
			}
			return $this->success('修改成功', $result);
		}else{
			return $this->error('错误请求');	
		}	
	}

	//单表查询
	protected  function _listJson($table,$map, $field = "*", $order = 'id desc') 
	{
		$model = model($table);
		$list_row = input('limit', 10);
		$page = input('page', 1);
		$list = $model->where($map)->field($field)->order($order)->paginate($list_row, false, ['query' => request()->param()]);
		return $list;
	}

	
	//多表查询
    public function getListJson($table,$map, $join_arr, $field = "a.*", $order = 'a.id desc') {
        $model = model($table);
		$list_row = input('limit', 10);
		$page = input('page', 1);
		$list = $model->alias('a')->where($map)->join($join_arr)->field($field)->order($order)->paginate($list_row, false, ['query' => request()->param()]);
		// echo $model->getLastSql();exit;
		return $list;
	}

    public function getInfoJson($table,$map, $join_arr, $field = "a.*") {
        $model = model($table);
		$info = $model->alias('a')->where($map)->join($join_arr)->field($field)->find();
		return $info;
	}


	//定义空方法时跳转到404错误页面
    public function _empty($e)
    {
		return $this->error('错误的方法');
    }
}


?>