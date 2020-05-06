<?php
namespace app\admin\controller;
use think\Json;
class AdminUserController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
        $groupList = model('AdminGroup')->groupList();
        $this->assign('groupList',$groupList);
    }


    public function index()
    {
        if (request()->isPost()) {
            try {
				//列表过滤器，生成查询Map对象
                $map = [];
                $join_arr = [
                    0=>['AdminGroup ag','ag.id = a.group_id','LEFT']
                ];
                $field = 'a.*,ag.title';
                $result = $this->getListJson('AdminUser', $map, $join_arr,$field);
                Json::success('ok', $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        } else {
            return $this->fetch();
        }

    }


    
	 //编辑
    public function edit()
    {
        $model = model("AdminUser");
        if (request()->isPost()) {
            try {
                $post_data = request()->post();
                $validate = validate("AdminUser");
                if (!$validate->check($post_data)) {
                    Json::fail($validate->getError());
                }
                $result = $model->update($post_data);
                if (!$result) {
                    Json::fail('编辑失败');
                }
                if(UID == $result['id']){
                    $map['id'] = UID;
                    $model->saveUserCache($map);
                }
                Json::success('编辑成功', $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }

        } else {
            $id = $_REQUEST[$model->getPk()];
            $info = $model->get($id);
            $this->assign('title', '编辑');
            $this->assign('info', json_encode($info, true));
            return $this->fetch('form');
        }
    }
}