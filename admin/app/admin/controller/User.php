<?php
namespace app\admin\controller;

use app\common\model\Attribute;
use think\facade\Json;

class User extends \app\admin\Controller
{
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
               $model = model("User");
               $post_data = request()->post();
               $validate = validate("User");
               if (!$validate->check($post_data)) {
                   return $this->error($validate->getError());
               }
               $post_data["password"] = emcryPwd($post_data["password"]);
               $result = $model->create($post_data);
   
               if(!$result) {
                   return $this->error('添加失败');
               }
               $data["user_id"] = $result["id"];
               $data["attr_id"]=  $post_data["attr_id"];
               //随机
               $CnameInfo = model("Cname")->where("attr_id=".$post_data["attr_id"])->field("id")->orderRaw("rand()")->find();
               $data["c_id"] = $CnameInfo["id"];
               model("UserCname")->create($data);
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
		$model = model("User");
		if (request()->isPost()) {
			try {

                $post_data = request()->post();
				$validate = validate("User");
				if (!$validate->scene("edit")->check($post_data)) {
					return $this->error($validate->getError());
				}
				$result = $model->update($post_data);
				if (!$result) {
					return $this->error('编辑失败');
                }
                $map["user_id"] = $post_data["id"];
                $CnameInfo = model("Cname")->where("attr_id=".$post_data["attr_id"])->field("id")->orderRaw("rand()")->find();
                $data["c_id"] = $CnameInfo["id"];
                $data["attr_id"]=  $post_data["attr_id"];
                model("UserCname")->where($map)->update($data);
                // $map["user_id"] = $post_data["id"];
                // $map["attr_id"] = $post_data["attr_id"];
                // if(model("UserCname")->where($map)->count()==0){
                //     //随机
                //     $CnameInfo = model("Cname")->where("attr_id=".$post_data["attr_id"])->field("id")->orderRaw("rand()")->find();
                //     $data["c_id"] = $CnameInfo["id"];
                //     $data["user_id"]=  $post_data["id"];
                //     $data["attr_id"]=  $post_data["attr_id"];
                //     model("UserCname")->create($data);
                // }
				return $this->success('编辑成功', $result);
			} catch (\Exception $e) {
				return $this->error($e->getMessage());
			}

		} else {
			$id =input($model->getPk());
			$info = $model->get($id);
			$this->result['title'] = '编辑';
			$this->result['info'] = json_encode($info, true);
			return $this->fetch('edit');
		}
    }
    
    public function editPwd()
	{
		$model = model("User");
		if (request()->isPost()) {
			try {

                $post_data = request()->post();
                if(strlen($post_data['password'])< 6 || strlen($post_data['password'])>15){
                    return $this->error("密码长度在6-15字符之间");
                }
                $map["id"] = (int)$post_data['id'];
                $data["password"] = emcryPwd(trim($post_data['password']));
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
			$info = $model->get($id);
			$this->result['title'] = '编辑';
			$this->result['info'] = json_encode($info, true);
			return $this->fetch();
		}
	}

    
}
?>