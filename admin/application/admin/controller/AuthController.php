<?php
namespace app\admin\controller;

use app\admin\service\AuthService;
use think\Json;
use com\LeftNav;
class AuthController extends BaseController
{
    public  $authService;
    public $leftNav;
    public function initialize()
    {
        parent::initialize();
        $this->authService = new AuthService();
        $this->leftNav = new LeftNav();
    }

    
    /********************************节点管理*******************************/
    public function ruleList(){
        if(request()->isPost()){
            $result = cache('adminRuleList');
            if(!$result){
				$result = model('AdminRule')->order('pid asc,sort asc')->select();
				foreach($result as $k=>$v){
                    $result[$k]['lay_is_open'] = false;
                }
                cache('adminRuleList', $result, 3600);
            }
            Json::success('获取成功',$this->leftNav->menu($result));
        }else{
            return $this->fetch(); 
        }
    }

    public function ruleAdd()
    {
        if(request()->isPost()){
            try {
                $post_data = input('post.');        
                $result = $this->authService->ruleAdd($post_data);
                if(!$result){
                    Json::fail($this->authService->getError());
                }
                Json::success($this->authService->getError(),$result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        }else{        
            $pid = intval(input('pid'));
            $adminRule = model('AdminRule')->all(function($query){
                $query->order('sort', 'asc');
            });
            $result = $this->leftNav->menu($adminRule);
            cache('adminRuleList', $adminRule, 3600);
            $this->assign('pid',$pid);
            $this->assign('admin_rule',$result);//权限列表
            return $this->fetch();
        }
    }

    public function ruleEdit()
    {
        if(request()->isPost()){
            try {
                $post_data = input('post.');        
                $result = $this->authService->ruleEdit($post_data);
                if(!$result){
                    Json::fail($this->authService->getError());
                }
                Json::success($this->authService->getError(),$result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        }else{
            //菜单分类
            $adminRule = model('AdminRule')->all(function($query){
                $query->order('sort', 'asc');
            });
            $result = $this->leftNav->menu($adminRule);
            cache('adminRuleList', $adminRule, 3600);
            $this->assign('admin_rule',$result);//权限列表
            //权限详情
            $rule = model('AdminRule')->get(function($query){
                $query->where(['id'=>input('id')])->field('id,href,title,icon,sort,pid,status');
            });
            $this->assign('rule',$rule);

            return $this->fetch();
        }
    }

    //设置节点状态
    public function setRuleStatus() 
    {
        try {
            $rule_id = input('rule_id',0);        
            $status = input('status',0); 
            $result = $this->authService->setRuleStatus($rule_id,$status);
            if(!$result){
                Json::fail($this->authService->getError());
            }
            Json::success($this->authService->getError(),$result);
        } catch (\Exception $e) {
            Json::fail($e->getMessage());
        }
    }


    //设置节点菜单权限
    public function setRuleOpen() 
    {
        try {
            $rule_id = input('rule_id',0);        
            $authopen = input('authopen',0); 
            $result = $this->authService->setRuleOpen($rule_id,$authopen);
            if(!$result){
                Json::fail($this->authService->getError());
            }
            Json::success($this->authService->getError(),$result);
        } catch (\Exception $e) {
            Json::fail($e->getMessage());
        }
    }
    //删除节点
    public function ruleDel()
    {
        try {
            $rule_id = input('rule_id',0);        
            $result = $this->authService->ruleDel($rule_id);
            if(!$result){
                Json::fail($this->authService->getError());
            }
            Json::success($this->authService->getError(),$result);
        } catch (\Exception $e) {
            Json::fail($e->getMessage());
        }
    }
    //清除节点缓存
    public function clearNode(){
		try {
			$result = $this->authService->clearNode(); 
			if (!$result) {
			    Json::fail($this->authService->getError());
			}
			Json::success($this->authService->getError(), $result);
		} catch (\Exception $e) {
			Json::fail($e->getMessage());
		}

    }

    //节点排序
    public function  ruleSort()
    {
        try {
            $post_data = input();        
            $result = $this->authService->ruleSort($post_data['rule_id'],$post_data['sort']);
            if(!$result){
                Json::fail($this->authService->getError());
            }
         
            $arr['url'] = url('/Auth/ruleList');
            Json::success($this->authService->getError(),$arr);
        } catch (\Exception $e) {
            Json::fail($e->getMessage());
        } 
    }

    /*************************添加角色 *************************/
    //角色列表
    public function groupList()
    {
        if(request()->isPost()){
            try {
            $map = [];
            $result = $this->_listJson('AdminGroup',$map);
            Json::success('获取成功',$result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }

        }else{
            return $this->fetch(); 
        }
 
    }
    //角色添加
    public function groupAdd()
    {
        if(request()->isPost()){
            try {
                $post_data = input('post.');        
                $result = $this->authService->groupAdd($post_data);
                if(!$result){
                    Json::fail($this->authService->getError());
                }
                Json::success($this->authService->getError(),$result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        }else{
            $this->assign('title','添加角色');
            $this->assign('info','null');
            return $this->fetch('groupForm');
        }
    }

    //角色编辑
    public function groupEdit()
    {
        if(request()->isPost()){
            try {
                $post_data = input('post.');        
                $result = $this->authService->groupEdit($post_data);
                if(!$result){
                    Json::fail($this->authService->getError());
                }
                Json::success($this->authService->getError(),$result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        }else{
            $group_id = input('group_id');
            $info = $this->authService->getGroupInfo($group_id);
            $this->assign('info', json_encode($info,true));
            $this->assign('title','编辑角色');
            return $this->fetch('groupForm');
        }
    }

    //角色删除
    public function groupDel()
     {
        try {
            $rule_id = input('group_id',0);        
            $result = $this->authService->groupDel($rule_id);
            if(!$result){
                Json::fail($this->authService->getError());
            }
            Json::success($this->authService->getError(),$result);
        } catch (\Exception $e) {
            Json::fail($e->getMessage());
        }
    }

    public function setGroupStatus()
    {
        try {
            $post_data = input();
            $result = $this->authService->setGroupStatus($post_data['group_id'],$post_data['status']);
            if(!$result){
                Json::fail($this->authService->getError());
            }
            Json::success($this->authService->getError(),$result);
        } catch (\Exception $e) {
            Json::fail($e->getMessage());
        }
    }

    //配置规则
    public function groupAccess()
    {
        if(request()->isPost()){
            try {
                $rules = input('post.rules');
                if(empty($rules)){
                    Json::fail('请选择权限');
                }
                $data = input('post.');
                $where['id'] = $data['group_id'];
                if(model('AdminGroup')->update($data,$where)){
                    $result['url'] = url('/Auth/groupList');
                    Json::success('权限配置成功',$result);
                }else{
                    Json::fail('保存错误');
                } 
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            } 
        }else{
            $admin_rule=model('AdminRule')->field('id,pid,title')->order('sort asc')->select();
            $rules = db('AdminGroup')->where('id',input('id'))->value('rules');
            $arr =  $this->leftNav->auth($admin_rule,$pid=0,$rules);
            $arr[] = array(
                "id"=>0,
                "pid"=>0,
                "title"=>"全部",
                "open"=>true
            );
            $this->assign('data',json_encode($arr,true));
            return $this->fetch();
         }
    }


}