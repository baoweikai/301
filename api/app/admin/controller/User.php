<?php
namespace app\admin\controller;

use app\common\model\Attribute;
use think\facade\Db;

class User extends \app\admin\Controller
{
    protected $middleware = ['auth'];
    protected $attributeModel,$attrList;
    protected function initialize()
    {
        parent::initialize();
        $this->attributeModel = new Attribute();
        $this->attrList = $this->attributeModel->getAttrList();
        $this->result['attrList'] = $this->attrList;
    }


    public function add()
    {
       if (request()->isPost()) {
           try {
               $model = Db::name("User");
               $post = request()->post();
               $validate = validate("User");
               if (!$validate->check($post)) {
                   return $this->error($validate->error);
               }
               $post["password"] = emcryPwd($post["password"]);
               $result = $model->create($post);
   
               if(!$result) {
                   return $this->error('添加失败');
               }
               $data["user_id"] = $result["id"];
               $data["attr_id"]=  $post["attr_id"];
               //随机
               $CnameInfo = Db::name("Cname")->where("attr_id=".$post["attr_id"])->field("id")->orderRaw("rand()")->find();
               $data["c_id"] = $CnameInfo["id"];
               Db::name("UserCname")->create($data);
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
		$model = Db::name("User");
		if (request()->isPost()) {
			try {

                $post = request()->post();
				$validate = validate("User");
				if (!$validate->scene("edit")->check($post)) {
					return $this->error($validate->error);
				}
				$result = $model->update($post);
				if (!$result) {
					return $this->error('编辑失败');
                }
                $map["user_id"] = $post["id"];
                $CnameInfo = Db::name("Cname")->where("attr_id=".$post["attr_id"])->field("id")->orderRaw("rand()")->find();
                $data["c_id"] = $CnameInfo["id"];
                $data["attr_id"]=  $post["attr_id"];
                Db::name("UserCname")->where($map)->update($data);
                // $map["user_id"] = $post["id"];
                // $map["attr_id"] = $post["attr_id"];
                // if(Db::name("UserCname")->where($map)->count()==0){
                //     //随机
                //     $CnameInfo = Db::name("Cname")->where("attr_id=".$post["attr_id"])->field("id")->orderRaw("rand()")->find();
                //     $data["c_id"] = $CnameInfo["id"];
                //     $data["user_id"]=  $post["id"];
                //     $data["attr_id"]=  $post["attr_id"];
                //     Db::name("UserCname")->create($data);
                // }
				return $this->success('编辑成功', $result);
			} catch (\Exception $e) {
				return $this->error($e->getMessage());
			}

		} else {
			$id =input($model->getPk());
			$info = $model->where('id', $id)->find();
			$this->result['title'] = '编辑';
			$this->result['info'] = json_encode($info, true);
			return $this->fetch('edit');
		}
    }
    
    public function editPwd()
	{
		$model = Db::name("User");
		if (request()->isPost()) {
			try {

                $post = request()->post();
                if(strlen($post['password'])< 6 || strlen($post['password'])>15){
                    return $this->error("密码长度在6-15字符之间");
                }
                $map["id"] = (int)$post['id'];
                $data["password"] = emcryPwd(trim($post['password']));
				$result = $model->where($map)->update($data);
				if (!$result) {
					return $this->error('编辑失败');
                }
				return $this->success('编辑成功'] = $result);
			} catch (\Exception $e) {
				return $this->error($e->getMessage());
			}

		} else {
			$id =input($model->getPk());
			$info = $model->where('id', $id)->find();
			$this->result['title'] = '编辑';
			$this->result['info'] = json_encode($info, true);
			return $this->fetch();
		}
	}

    
}
?>