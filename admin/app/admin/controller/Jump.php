<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\Cache;
use think\facade\Json;

class Jump extends \app\admin\Controller
{
    protected $time_slot;
    public function initialize()
    {
        parent::initialize();
        $this->time_slot = config("time_slot");
        $this->result['timeSlot'] = $this->time_slot;
    }

    public function index()
    {
        if (request()->isPost()) {
            try { 
                $keyword = input('keyword');
				//列表过滤器，生成查询Map对象
                $map = [];
                if (!empty($keyword)) {
                    $map[] = ['a.jump_url|a.shield_url|u.account','like', '%' . $keyword . '%'];
                }
                $join_arr = [
                    0 => ['User u', 'u.id = a.user_id'],
                    1 => ['Attribute at', 'u.attr_id = at.id'],
                    2 => ['UserCname uc', 'uc.user_id = u.id'],
                    3 => ['Cname c', 'c.id = uc.c_id'],
                ];
                $field = 'a.*,c.cname,u.account';
                $result = $this->getListJson('Jump', $map, $join_arr, $field);
                $list = $result->toArray();
                $time = time();
                $dt=date('Y-m-d',$time);
                $new_time = date("Y-m-d",strtotime("$dt+1day"));
                foreach($list["data"] as $key=>$val){        
                    $list["data"][$key]['jumpTodayNumer'] = Db::connect("db_config9")->name("jump_count")->where("j_id=".$val["id"]." and create_time>=".strtotime($dt)." and create_time<".strtotime($new_time))->group("get_ip")->count("id");
                    $list["data"][$key]["pvTodayNumer"] =  Db::connect("db_config9")->name("jump_count")->where("j_id=".$val["id"]." and create_time>=".strtotime($dt)." and create_time<".strtotime($new_time))->sum("pv");
                    $list["data"][$key]["citedTodayNumer"] = Db::connect("db_config9")->name("cited_count")->where("j_id=".$val["id"]." and create_time>=".strtotime($dt)." and create_time<".strtotime($new_time))->count("id");
                }
    
                Json::success('ok', $list);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        } else {
            return $this->fetch();
        }
    }

    /**
     * 修改引量跳转域名
     */
    public function saveJumpUrl()
    {
		if (request()->isPost()) {
			try {	
                $model = model("Jump");
                $post_data = request()->post();
                $admin_jump_url = trim($post_data['admin_jump_url']);
                if(!$admin_jump_url){
                    Json::fail("请填写引量跳转链接");
                }
                //数据查询
                $list = $model->field("id,shield_url")->order("id asc")->select();
                foreach($list  as $val){
                    $data["admin_jump_url"] = $admin_jump_url."?".$val["id"];
                    //修改引量跳转量
                    $map["id"] = $val["id"];
                    $model->where($map)->update($data);
                    $host = trim($val["shield_url"]);
                    //删除缓存
                    $this->delJumpRedis($host);
                }
				Json::success("修改成功!!!");
            } catch (\Exception $e) {
				Json::fail($e->getMessage());
            }
        }else{
            return $this->fetch('jump'); 
        }
    }

    /**
     * 修改需要引量的IP
     */
    public function saveEndIp()
    {
		if (request()->isPost()) {
			try {	
                $model = model("Jump");
                $post_data = request()->post();
                $end_ip = trim($post_data['end_ip']);
                if(!$end_ip){
                    Json::fail("请填写引量IP");
                }
                //数据查询
                $list = $model->field("id,shield_url")->order("id asc")->select();
                foreach($list  as $val){
                    $data["end_ip"] = $end_ip;
                    //修改引量跳转量
                    $map["id"] = $val["id"];
                    $model->where($map)->update($data);
                    $host = trim($val["shield_url"]);
                    //删除缓存
                    $this->delJumpRedis($host);
                }
				Json::success("修改成功!!!");
            } catch (\Exception $e) {
				Json::fail($e->getMessage());
            }
        }else{
            return $this->fetch('end_ip'); 
        }
    }
    
    	 //编辑
	public function edit()
	{
		$model = model("Jump");
		if (request()->isPost()) {
			try {

				$post_data = request()->post();
				$validate = validate("Jump");
				if (!$validate->check($post_data)) {
					Json::fail($validate->getError());
                }
                if($post_data["startTime"]){
                    $post_data["start_time"] = implode(",",$post_data["startTime"]);
                }
                unset($post_data['startTime']);
				$result = $model->update($post_data);
				if (!$result) {
					Json::fail('编辑失败');
                }
                $map["id"] = (int)$post_data["id"];
                $info = $model->where($map)->find();
                $host = trim($info["shield_url"]);
                //删除缓存
                $this->delJumpRedis($host);

				Json::success('编辑成功', $result);
			} catch (\Exception $e) {
				Json::fail($e->getMessage());
			}

		} else {
            $id =input($model->getPk());
            $map["a.id"] = $id;
            $join_arr = [
                0 => ['User u', 'u.id = a.user_id'],
                1 => ['Attribute at', 'u.attr_id = at.id'],
                2 => ['UserCname uc', 'uc.user_id = u.id'],
                3 => ['Cname c', 'c.id = uc.c_id'],
            ];
            $field = 'a.*,c.cname,u.account';
            $info = $this->getInfoJson("Jump",$map,$join_arr,$field);
 
            $startTime = $info["start_time"]!="" ? explode(",",$info['start_time']['val']) :[];
            $this->result['startTime'] = $startTime;
			$this->result['title'] = '编辑';
			$this->result['info'] = json_encode($info, true);
			return $this->fetch('form');
		}
    }
    //是否开启引量
    public function setIsOpen()
	{
		if (request()->isPost()) {
			try {
				$model = model(CONTROLLER_NAME);
				$post_data = request()->post();
				if(!array_key_exists('id',$post_data)){
					Json::fail('ID不存在');
				}
				if (!array_key_exists('is_open', $post_data)) {
					Json::fail('状态错误！！');
				}
				$result = $model->update($post_data);
				if (!$result) {
					Json::fail('设置失败');
                }
                $map["id"] = (int)$post_data["id"];
                $info = $model->where($map)->find();
                $host = trim($info["shield_url"]);
                $this->delJumpRedis($host);
				Json::success('设置成功', $result);
			}catch(\Exception $e){
				Json::fail($e->getMessage());
			}
		}else{
		   Json::fail('错误请求');	
		}
    }
    
    //多选引量
    public function openLeadAmount()
    {
        if (request()->isPost()) {
			try {
				$model = model(CONTROLLER_NAME);
				$post_data = request()->post();
				if(!array_key_exists('ids',$post_data)){
					Json::fail('异常参数');
                }
                $ids = trim($post_data['ids']);
                if(!$ids) {
                    Json::fail('请选择需要开启引量的数据！！');
                }
                $map[] = ["id","in",$ids];
                $data["is_open"] = 1;
				$result = $model->where($map)->update($data);
				if (!$result) {
					Json::fail('设置失败');
                }

                $list = $model->where($map)->select();
                foreach($list as $val){
                    $this->delJumpRedis($val['shield_url']);
                }
				Json::success('设置成功', $result);
			}catch(\Exception $e){
				Json::fail($e->getMessage());
			}
		}else{
		   Json::fail('错误请求');	
		}  
    }
    //一键关闭引量
    public function closeLeadAmount()
    {
        if (request()->isPost()) {
			try {
                $model = model(CONTROLLER_NAME);
                //查询是否有开启引量的
                $citedList = $model->where("is_open=1")->column("id");
                if(count($citedList)>0) {
                    $arr["jump_id"] = implode(",",$citedList);
                    $arr["save_time"] = time();
                    if(model("SwitchCited")->count()>0){
                         model("SwitchCited")->where('id=1')->update($arr);
                    }else{
                        model("SwitchCited")->insert($arr);
                    }
                }
                $data["is_open"] = 0;
                $map[] = ['1','eq','1'];
				$result = $model->where($map)->update($data);
				if (!$result) {
					Json::fail('设置失败');
                }
                
                $list = $model->where($map)->select();
                foreach($list as  $val){
                    $this->delJumpRedis($val['shield_url']);
                }
				Json::success('设置成功', $result);
			}catch(\Exception $e){
				Json::fail($e->getMessage());
			}
		}else{
		   Json::fail('错误请求');	
		}  
    }

    //开启上一次引量
    public  function  openLastAmount()
    {
        if (request()->isPost()) {
			try {
                $model = model(CONTROLLER_NAME);
                //跳转id
                $jump_ids = model("SwitchCited")->where("id=1")->value("jump_id");
                if(!$jump_ids){
                    Json::fail('设置失败，数据可能不存在');
                }
                $data["is_open"] = 1;
                $map =" id  in (".$jump_ids.")";
                $result = $model->where($map)->update($data);
				if (!$result) {
					Json::fail('设置失败');
                }
                $list = $model->where($map)->select();
                //删除缓存
                foreach($list as $val){
                    $this->delJumpRedis($val['shield_url']);
                }
				Json::success('设置成功', $result);
			}catch(\Exception $e){
				Json::fail($e->getMessage());
			}
		}else{
		   Json::fail('错误请求');	
		}  
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
                
                $map["id"] = (int)$post_data["id"];
                $info = $model->where($map)->find();
                $host = trim($info["shield_url"]);
                //删除缓存
                $this->delJumpRedis($host);
				Json::success('设置成功', $result);
			}catch(\Exception $e){
				Json::fail($e->getMessage());
			}
		}else{
		   Json::fail('错误请求');	
		}
    }
    

    public function del()
	{
		$id = input('id');

		if (empty($id)) {
			Json::fail('请选择要操作的数据！');
		}

        $map["id"] = $id;     
        $model = model(CONTROLLER_NAME);    
        $info = $model->where($map)->find();
        $host = trim($info["shield_url"]);
        //删除缓存
        $this->delJumpRedis($host);
		$result = model($name)->where($map)->delete();
		if(!$result) {
			Json::fail('删除失败!');
		}
		Json::success('删除成功!');
	}


    /**
     * 删除跳转链接
     */
    protected function delJumpRedis($jump_url ="")
    {

        for($i=0;$i<=11;$i++){
            Cache::store('redis_db'.$i)->rm("Jump".$jump_url); 
        }
    }

}

?> 