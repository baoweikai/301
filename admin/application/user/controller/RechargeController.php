<?php
namespace app\user\controller;
use think\Json;
class RechargeController  extends BaseController
{
    public function initialize()
    {
        parent::initialize();
    }


    public function index()
    {
        if (request()->isPost()) {
            try { 
                $keyword = input('keyword');
				//列表过滤器，生成查询Map对象
                $map[] = ["a.user_id","eq",USER_UID];
                if (!empty($keyword)) {
        
                    $map[] = ['a.desc|u.account','like', '%' . $keyword . '%'];

                }
        
                $join_arr = [
                    0 => ['User u', 'u.id = a.user_id']
                ];
                $field = 'a.number,a.desc,a.create_time,u.account';
                $result = $this->getListJson('Recharge', $map, $join_arr, $field);
                Json::success('ok', $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        } else {
            return $this->fetch();
        }
    }


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
               $map["id"] = (int)$post_data["user_id"];
               $number =(int)$post_data["number"];
               model("User")->where($map)->setInc("number",$number);
               $result = $model->create($post_data);
               if(!$result) {
                   Json::fail('充值失败');
               }
           Json::success('充值成功',$result);
           } catch (\Exception $e) {
               Json::fail($e->getMessage());
           }

       }else{
           $this->assign('title', '充值');
           $map["id"] = (int)input("userId");
           $userInfo = model("User")->get($map);
           $this->assign("info",$userInfo);
           return $this->fetch('form');
       }
    }

}
?>