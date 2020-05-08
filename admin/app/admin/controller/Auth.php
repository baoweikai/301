<?php
namespace app\admin\controller;

use app\admin\service\AuthService;
use think\facade\Db;
use com\LeftNav;

class Auth extends \app\admin\Controller
{
    protected $middleware = ['auth'];
    public  $authService;
    public $leftNav;
    protected function initialize()
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
				$result = Db::name('AuthRule')->order('pid asc,sort asc')->select();
				foreach($result as $k=>$v){
                    $result[$k]['lay_is_open'] = false;
                }
                cache('adminRuleList', $result, 3600);
            }
            return $this->success('获取成功',$this->leftNav->menu($result));
        }else{
            return $this->fetch(); 
        }
    }

    public function ruleAdd()
    {
        if(request()->isPost()){
            try {
                $post = input('post.');        
                $result = $this->authService->ruleAdd($post);
                if(!$result){
                    return $this->error($this->authService->error);
                }
                return $this->success($this->authService->error,$result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }else{        
            $pid = intval(input('pid'));
            $adminRule = Db::name('AuthRule')->all(function($query){
                $query->order('sort', 'asc');
            });
            $result = $this->leftNav->menu($adminRule);
            cache('adminRuleList', $adminRule, 3600);
            $this->result['pid'] = $pid;
            $this->result['admin_rule'] = $result;//权限列表
            return $this->fetch();
        }
    }

    public function ruleEdit()
    {
        if(request()->isPost()){
            try {
                $post = input('post.');        
                $result = $this->authService->ruleEdit($post);
                if(!$result){
                    return $this->error($this->authService->error);
                }
                return $this->success($this->authService->error,$result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }else{
            //菜单分类
            $adminRule = Db::name('AuthRule')->all(function($query){
                $query->order('sort', 'asc');
            });
            $result = $this->leftNav->menu($adminRule);
            cache('adminRuleList', $adminRule, 3600);
            $this->result['admin_rule'] = $result;//权限列表
            //权限详情
            $rule = Db::name('AuthRule')->get(function($query){
                $query->where(['id'=>input('id')])->field('id,href,title,icon,sort,pid,status');
            });
            $this->result['rule'] = $rule;

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
                return $this->error($this->authService->error);
            }
            return $this->success($this->authService->error,$result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
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
                return $this->error($this->authService->error);
            }
            return $this->success($this->authService->error,$result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    //删除节点
    public function ruleDel()
    {
        try {
            $rule_id = input('rule_id',0);        
            $result = $this->authService->ruleDel($rule_id);
            if(!$result){
                return $this->error($this->authService->error);
            }
            return $this->success($this->authService->error,$result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    //清除节点缓存
    public function clearNode(){
		try {
			$result = $this->authService->clearNode(); 
			if (!$result) {
			    return $this->error($this->authService->error);
			}
			return $this->success($this->authService->error, $result);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}

    }

    //节点排序
    public function  ruleSort()
    {
        try {
            $post = input();        
            $result = $this->authService->ruleSort($post['rule_id'],$post['sort']);
            if(!$result){
                return $this->error($this->authService->error);
            }
         
            $arr['url'] = url('/Auth/ruleList');
            return $this->success($this->authService->error,$arr);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
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
            return $this->success('获取成功',$result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
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
                $post = input('post.');        
                $result = $this->authService->groupAdd($post);
                if(!$result){
                    return $this->error($this->authService->error);
                }
                return $this->success($this->authService->error,$result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }else{
            $this->result['title'] = '添加角色';
            $this->result['info'] = 'null';
            return $this->fetch('groupForm');
        }
    }

    //角色编辑
    public function groupEdit()
    {
        if(request()->isPost()){
            try {
                $post = input('post.');        
                $result = $this->authService->groupEdit($post);
                if(!$result){
                    return $this->error($this->authService->error);
                }
                return $this->success($this->authService->error,$result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }else{
            $group_id = input('group_id');
            $info = $this->authService->getGroupInfo($group_id);
            $this->result['info'] =  json_encode($info,true);
            $this->result['title'] = '编辑角色';
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
                return $this->error($this->authService->error);
            }
            return $this->success($this->authService->error,$result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function setGroupStatus()
    {
        try {
            $post = input();
            $result = $this->authService->setGroupStatus($post['group_id'],$post['status']);
            if(!$result){
                return $this->error($this->authService->error);
            }
            return $this->success($this->authService->error,$result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    //配置规则
    public function groupAccess()
    {
        if(request()->isPost()){
            try {
                $rules = input('post.rules');
                if(empty($rules)){
                    return $this->error('请选择权限');
                }
                $data = input('post.');
                $where['id'] = $data['group_id'];
                if(Db::name('AdminGroup')->update($data,$where)){
                    $result['url'] = url('/Auth/groupList');
                    return $this->success('权限配置成功',$result);
                }else{
                    return $this->error('保存错误');
                } 
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            } 
        }else{
            $admin_rule=Db::name('AuthRule')->field('id,pid,title')->order('sort asc')->select();
            $rules = Db::name('AdminGroup')->where('id',input('id'))->value('rules');
            $arr =  $this->leftNav->auth($admin_rule,$pid=0,$rules);
            $arr[] = array(
                "id"=>0,
                "pid"=>0,
                "title"=>"全部",
                "open"=>true
            );
            $this->result['data'] = json_encode($arr,true);
            return $this->fetch();
         }
    }


}