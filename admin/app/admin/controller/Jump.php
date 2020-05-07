<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\Cache;
use think\facade\Json;

class Jump extends \app\admin\Controller
{
    protected $time_slot;
    protected function initialize()
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
    
                return $this->success($list);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
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
                $model = Db::name("Jump");
                $post = request()->post();
                $admin_jump_url = trim($post['admin_jump_url']);
                if(!$admin_jump_url){
                    return $this->error("请填写引量跳转链接");
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
				return $this->success("修改成功!!!");
            } catch (\Exception $e) {
				return $this->error($e->getMessage());
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
                $model = Db::name("Jump");
                $post = request()->post();
                $end_ip = trim($post['end_ip']);
                if(!$end_ip){
                    return $this->error("请填写引量IP");
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
				return $this->success("修改成功!!!");
            } catch (\Exception $e) {
				return $this->error($e->getMessage());
            }
        }else{
            return $this->fetch('end_ip'); 
        }
    }
    
    	 //编辑
	public function edit()
	{
		$model = Db::name("Jump");
		if (request()->isPost()) {
			try {

				$post = request()->post();
				$validate = validate("Jump");
				if (!$validate->check($post)) {
					return $this->error($validate->error);
                }
                if($post["startTime"]){
                    $post["start_time"] = implode(",",$post["startTime"]);
                }
                unset($post['startTime']);
				$result = $model->update($post);
				if (!$result) {
					return $this->error('编辑失败');
                }
                $map["id"] = (int)$post["id"];
                $info = $model->where($map)->find();
                $host = trim($info["shield_url"]);
                //删除缓存
                $this->delJumpRedis($host);

				return $this->success('编辑成功', $result);
			} catch (\Exception $e) {
				return $this->error($e->getMessage());
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
				$post = request()->post();
				if(!array_key_exists('id',$post)){
					return $this->error('ID不存在');
				}
				if (!array_key_exists('is_open', $post)) {
					return $this->error('状态错误！！');
				}
				$result = $model->update($post);
				if (!$result) {
					return $this->error('设置失败');
                }
                $map["id"] = (int)$post["id"];
                $info = $model->where($map)->find();
                $host = trim($info["shield_url"]);
                $this->delJumpRedis($host);
				return $this->success('设置成功', $result);
			}catch(\Exception $e){
				return $this->error($e->getMessage());
			}
		}else{
		   return $this->error('错误请求');	
		}
    }
    
    //多选引量
    public function openLeadAmount()
    {
        if (request()->isPost()) {
			try {
				$model = model(CONTROLLER_NAME);
				$post = request()->post();
				if(!array_key_exists('ids',$post)){
					return $this->error('异常参数');
                }
                $ids = trim($post['ids']);
                if(!$ids) {
                    return $this->error('请选择需要开启引量的数据！！');
                }
                $map[] = ["id","in",$ids];
                $data["is_open"] = 1;
				$result = $model->where($map)->update($data);
				if (!$result) {
					return $this->error('设置失败');
                }

                $list = $model->where($map)->select();
                foreach($list as $val){
                    $this->delJumpRedis($val['shield_url']);
                }
				return $this->success('设置成功', $result);
			}catch(\Exception $e){
				return $this->error($e->getMessage());
			}
		}else{
		   return $this->error('错误请求');	
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
                    if(Db::name("SwitchCited")->count()>0){
                         Db::name("SwitchCited")->where('id=1')->update($arr);
                    }else{
                        Db::name("SwitchCited")->insert($arr);
                    }
                }
                $data["is_open"] = 0;
                $map[] = ['1','eq','1'];
				$result = $model->where($map)->update($data);
				if (!$result) {
					return $this->error('设置失败');
                }
                
                $list = $model->where($map)->select();
                foreach($list as  $val){
                    $this->delJumpRedis($val['shield_url']);
                }
				return $this->success('设置成功', $result);
			}catch(\Exception $e){
				return $this->error($e->getMessage());
			}
		}else{
		   return $this->error('错误请求');	
		}  
    }

    //开启上一次引量
    public  function  openLastAmount()
    {
        if (request()->isPost()) {
			try {
                $model = model(CONTROLLER_NAME);
                //跳转id
                $jump_ids = Db::name("SwitchCited")->where("id=1")->value("jump_id");
                if(!$jump_ids){
                    return $this->error('设置失败，数据可能不存在');
                }
                $data["is_open"] = 1;
                $map =" id  in (".$jump_ids.")";
                $result = $model->where($map)->update($data);
				if (!$result) {
					return $this->error('设置失败');
                }
                $list = $model->where($map)->select();
                //删除缓存
                foreach($list as $val){
                    $this->delJumpRedis($val['shield_url']);
                }
				return $this->success('设置成功', $result);
			}catch(\Exception $e){
				return $this->error($e->getMessage());
			}
		}else{
		   return $this->error('错误请求');	
		}  
    }

    	//更改状态
	public function setStatus()
	{
		if (request()->isPost()) {
			try {
				$model = model(CONTROLLER_NAME);
				$post = request()->post();
				if(!array_key_exists('id',$post)){
					return $this->error('ID不存在');
				}
				if (!array_key_exists('status', $post)) {
					return $this->error('状态错误！！');
				}
				$result = $model->update($post);
				if (!$result) {
					return $this->error('设置失败');
                }
                
                $map["id"] = (int)$post["id"];
                $info = $model->where($map)->find();
                $host = trim($info["shield_url"]);
                //删除缓存
                $this->delJumpRedis($host);
				return $this->success('设置成功', $result);
			}catch(\Exception $e){
				return $this->error($e->getMessage());
			}
		}else{
		   return $this->error('错误请求');	
		}
    }
    

    public function del()
	{
		$id = input('id');

		if (empty($id)) {
			return $this->error('请选择要操作的数据！');
		}

        $map["id"] = $id;     
        $model = model(CONTROLLER_NAME);    
        $info = $model->where($map)->find();
        $host = trim($info["shield_url"]);
        //删除缓存
        $this->delJumpRedis($host);
		$result = model($name)->where($map)->delete();
		if(!$result) {
			return $this->error('删除失败!');
		}
		return $this->success('删除成功!');
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