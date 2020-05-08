<?php
namespace app\admin\controller;

use think\facade\Json;
use think\facade\Db;

class AdminUser extends \app\admin\Controller
{
    protected $middleware = ['auth'];
    protected function initialize()
    {
        parent::initialize();
        $groupList = Db::name('AdminGroup')->groupList();
        $this->result['groupList'] = $groupList;
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
                return $this->success($result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        } else {
            return $this->fetch();
        }

    }


    
	 //编辑
    public function edit()
    {
        $model = Db::name("AdminUser");
        if (request()->isPost()) {
            try {
                $post = request()->post();
                $validate = validate("AdminUser");
                if (!$validate->check($post)) {
                    return $this->error($validate->error);
                }
                $result = $model->update($post);
                if (!$result) {
                    return $this->error('编辑失败');
                }
                if(UID == $result['id']){
                    $map['id'] = UID;
                    $model->saveUserCache($map);
                }
                return $this->success('编辑成功', $result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

        } else {
            $id = $_REQUEST[$model->getPk()];
            $info = $model->where('id', $id)->find();
            $this->result['title'] = '编辑');
            $this->result['info'] = json_encode($info, true);
            return $this->fetch('form');
        }
    }
}