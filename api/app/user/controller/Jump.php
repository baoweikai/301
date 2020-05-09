<?php
namespace app\user\controller;

use think\facade\Db;
use think\Json;

class Jump extends \app\user\Controller
{
    public function index()
    {
        if (request()->isPost()) {
            try { 
                $keyword = input('keyword');
				//列表过滤器，生成查询Map对象
                $map["a.user_id"] = USER_UID;
                if (!empty($keyword)) {
        
                    $map[] = ['a.jump_url|a.shield_url','like', '%' . $keyword . '%'];

                }
                $join_arr = [
                    0 => ['User u', 'u.id = a.user_id'],
                    1 => ['Attribute at', 'u.attr_id = at.id'],
                    2 => ['UserCname uc', 'uc.user_id = u.id'],
                    3 => ['Cname c', 'c.id = uc.c_id'],
                ];
                $field = 'a.shield_url,a.jump_url,a.status,a.is_param,a.is_start,a.is_expire,a.create_time,c.cname';
                $result = $this->getListJson('Jump', $map, $join_arr, $field);
                return $this->success($result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        } else {
            return $this->fetch();
        }
    }



    public function  add()
    {
       if (request()->isPost()) {
           Db::startTrans();//开启回滚
           try {
               $model = Db::name("Jump");
               $post = request()->post();
               $validate = validate("Jump");
               if (!$validate->check($post)) {
                   return $this->error($validate->error);
               }
               //判断条数
               $map["id"] = USER_UID;
               $userInfo = Db::name("User")->where($map)->find();
               if($userInfo["number"] == 0){
                    //回滚事务
                    Db::rollback();
                return $this->error("条数不够，请联系客服");
                    
               }
               //扣除条数
                $number = $userInfo["number"]-1;
                Db::name("User")->where($map)->update(["number"=>$number]);
                $data["user_id"] = USER_UID;
                $data["number"] = 1;
                $data["desc"] = '添加跳转域名，扣除1条';
                Db::name("Expend")->create($data);
                //设置用户Cname是否使用
                $conMap[] = [
                    0 =>["user_id",'eq',USER_UID],
                    1 =>["attr_id",'eq',$userInfo['attr_id']],
                ];
                $cdata["is_use"] = 1;
                $cdata["use_time"] = time();
                Db::name("UserCname")->where($conMap)->update($cdata);
                //添加跳转域名
               $post["user_id"] = USER_UID;
               //过期时间
               $expire_time = date("Y-m-d H:i:s", strtotime("+5 months",time()));
               $post["expire_time"] = strtotime($expire_time);
               $post["expire_time"] = strtotime($expire_time);
               $post["end_ip"] = 1;
               $post["start_time"] = 0;
               $result = $model->create($post);
               if(!$result) {
                    //回滚事务
                    Db::rollback();
                   return $this->error('添加失败');
               }
            //回滚事务
            Db::commit();
           return $this->success('添加成功',$result);
           } catch (\Exception $e) {
                //回滚事务
                Db::rollback();
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
		$model = Db::name("Jump");
		if (request()->isPost()) {
			try {

				$post = request()->post();
				$validate = validate("Jump");
				if (!$validate->check($post)) {
					return $this->error($validate->error);
                }
                $post["user_id"] = USER_UID;
				$result = $model->update($post);
				if (!$result) {
					return $this->error('编辑失败');
				}
				return $this->success('编辑成功', $result);
			} catch (\Exception $e) {
				return $this->error($e->getMessage());
			}

		} else {
			$id =input($model->getPk());
			$info = $model->where('id', $id)->find();
			$this->result['title'] = '编辑');
			$this->result['info'] = json_encode($info, true));
			return $this->fetch('edit');
		}
	}
}


?>