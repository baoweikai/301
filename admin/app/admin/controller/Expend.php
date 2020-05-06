<?php
namespace app\admin\controller;
use think\facade\Json;

class Expend extends \app\admin\Controller
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
                $map = [];
                if (!empty($keyword)) {
        
                    $map[] = ['a.desc|u.account','like', '%' . $keyword . '%'];

                }
        
                $join_arr = [
                    0 => ['User u', 'u.id = a.user_id']
                ];
                $field = 'a.*,u.account';
                $result = $this->getListJson('Expend', $map, $join_arr, $field);
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
           try {
               $model = model(CONTROLLER_NAME);
               $post_data = request()->post();
               $validate = validate(CONTROLLER_NAME);
               if (!$validate->check($post_data)) {
                   return $this->error($validate->getError());
               }
               $map["id"] = (int)$post_data["user_id"];
               $number =(int)$post_data["number"];
               model("User")->where($map)->setDec("number",$number);
               $result = $model->create($post_data);
               if(!$result) {
                   return $this->error('扣除失败');
               }
             return $this->success('扣除成功',$result);
           } catch (\Exception $e) {
               return $this->error($e->getMessage());
           }

       }else{
           $this->result['title'] = '扣除';
           $map["id"] = (int)input("userId");
           $userInfo = model("User")->get($map);
           $this->result['info'] = $userInfo;
           return $this->fetch('form');
       }
    }

}
?>