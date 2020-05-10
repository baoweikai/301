<?php
namespace app\user\controller;

use think\facade\Db;

class Expend extends \core\Controller
{
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
                $field = 'a.number,a.desc,a.create_at,u.account';
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
               $post = request()->post();
               $validate = validate(CONTROLLER_NAME);
               if (!$validate->check($post)) {
                   return $this->error($validate->error);
               }
               $map["id"] = (int)$post["user_id"];
               $number =(int)$post["number"];
               Db::name("User")->where($map)->setDec("number",$number);
               $result = $model->create($post);
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
           $userInfo = Db::name("User")->where($map)->find();
           $this->result['info'] = $userInfo;
           return $this->fetch('form');
       }
    }

}
?>