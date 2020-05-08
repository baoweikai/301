<?php
 namespace app\admin\service;
 use app\admin\model\AuthRuleModel;
 use think\facade\Validate;
 use app\admin\model\AdminGroupModel;
 class AuthService {
    private $error = '';
    private $AuthRuleModel;
    private $AdminGroupModel;

    public function __construct() {
        $this->AuthRuleModel = new AuthRuleModel();
        $this->AdminGroupModel = new AdminGroupModel();
    }
    
    //节点添加
    public function ruleAdd($post)
    {
        if(!$post){
            return $this->error = '数据不能为空';
            return false;
        }
        
        $check = validate('AuthRule');
        if (!$check->check($post)) {
            return $this->error = $check->error;
            return false;
        }

        $result = $this->AuthRuleModel->ruleAdd($post);
        if(!$result) {
            return $this->error = $this->AuthRuleModel->error;
            return false;
        }
        return $this->error = $this->AuthRuleModel->error;
        return $result;
    }

    //节点编辑
    public function ruleEdit($post)
    {
        if(!$post){
            return $this->error = '数据不能为空';
            return false;
        }
        
        $check = validate('AuthRule');
        if (!$check->check($post)) {
            return $this->error = $check->error;
            return false;
        }

        $result = $this->AuthRuleModel->ruleEdit($post);
        if(!$result) {
            return $this->error = $this->AuthRuleModel->error;
            return false;
        }
        return $this->error = $this->AuthRuleModel->error;
        return $result;
    }

    //删除节点
    public function ruleDel($rule_id = 0) {
        $rule_id = intval($rule_id);
        if($rule_id == 0){
             return $this->error = '节点ID错误';
             return false;
        }

        $result = $this->AuthRuleModel->ruleDel($rule_id);
        if(!$result) {
            return $this->error = $this->AuthRuleModel->error;
            return false;
        }
        return $this->error = $this->AuthRuleModel->error;
        return $result;
    }

    /**
     * 清除节点缓存
     */
    public function clearNode()
    {
         $result = $this->AuthRuleModel->clearNode();
         if(!$result) {
             return $this->error = $this->AuthRuleModel->error;
             return false;
         }
         return $this->error = $this->AuthRuleModel->error;
         return true;
    }
    //更新角色
    public function saveRole($post)
    {
        if(!$post) {
            return $this->error = '不存在数据';
            return  false;
        }
        $result = $this->AuthRuleModel->saveRole($post);
        if(!$result){
            return $this->error = $this->AuthRuleModel->error;
            return false;
        } 
        return $this->error = $this->AuthRuleModel->error;
        return $result;
    }

    //设置节点菜单状态
    public function setRuleStatus($rule_id = 0,$status) 
    {
        $rule_id = intval($rule_id);
        if($rule_id == 0){
            return $this->error = '节点ID错误';
            return false;
        }
        $result = $this->AuthRuleModel->setRuleStatus($rule_id,$status);
        if(!$result) {
            return $this->error = $this->AuthRuleModel->error;
            return false;
        }
        return $this->error = $this->AuthRuleModel->error;
        return $result;
    }
    
        //设置验证权限
    public function setRuleOpen($rule_id = 0,$authopen) 
    {
        $rule_id = intval($rule_id);
        if($rule_id == 0){
            return $this->error = '节点ID错误';
            return false;
        }
        $result = $this->AuthRuleModel->setRuleOpen($rule_id,$authopen);
        if(!$result) {
            return $this->error = $this->AuthRuleModel->error;
            return false;
        }
        return $this->error = $this->AuthRuleModel->error;
        return $result;
    }

    //设置排序
    public function ruleSort($rule_id = 0,$sort)
    {
        $result = $this->AuthRuleModel->ruleSort($rule_id,$sort);
        if(!$result) {
            return $this->error = $this->AuthRuleModel->error;
            return false;
        }
        return $this->error = $this->AuthRuleModel->error;
        return $result;
    }



    /**************************************** 角色管理*********************************************/

    //角色添加
    public function groupAdd($post)
    {
        if(!$post && !in_array($post)) {
        return $this->error = '数据不存在';
        return false;
        }
    
        $check = validate('AdminGroup');
        if (!$check->check($post)) {
        return $this->error = $check->error;
        return false;
        }

        $result = $this->AdminGroupModel->groupAdd($post);
        if(!$result) {
        return $this->error = $this->AdminGroupModel->error;
        return false;
        }
    
        return $this->error = $this->AdminGroupModel->error;
        return $result;
    }

    public function groupEdit($post)
    {
        if(!$post && !in_array($post)) {
        return $this->error = '数据不存在';
        return false;
        }
    
        $check = validate('AdminGroup');
        if (!$check->check($post)) {
        return $this->error = $check->error;
        return false;
        }

        $result = $this->AdminGroupModel->groupEdit($post);
        if(!$result) {
        return $this->error = $this->AdminGroupModel->error;
        return false;
        }
    
        return $this->error = $this->AdminGroupModel->error;
        return $result;
    }

    //查询角色
    public function getGroupInfo($group_id = 0)
    {
        $group_id = intval($group_id);
        $result = $this->AdminGroupModel->get($group_id);
        if(!$result){
            return $this->error = '角色不存在';
            return false;
        }
        return $result;
    }
    //角色删除
    public function groupDel($group_id = 0)
     {
        $group_id = intval($group_id);
        if($group_id == 0){
             return $this->error = 'ID错误';
             return false;
        }

        $result = $this->AdminGroupModel->groupDel($group_id);
        if(!$result) {
            return $this->error = $this->AdminGroupModel->error;
            return false;
        }
        return $this->error = $this->AdminGroupModel->error;
        return $result;
    }

    public function setGroupStatus($group_id = 0,$status){
        $group_id = intval($group_id);
        if($group_id == 0){
            return $this->error = 'ID错误';
            return false;
        }
        $result = $this->AdminGroupModel->setGroupStatus($group_id,$status);
        if(!$result) {
            return $this->error = $this->AdminGroupModel->error;
            return false;
        }
        return $this->error = $this->AdminGroupModel->error;
        return $result;
    }

    //返回错误信息
	public function getError() {
		return return $this->error;
	}
 }


?>