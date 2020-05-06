<?php
 namespace app\admin\service;
 use app\admin\model\AdminRuleModel;
 use think\facade\Validate;
 use app\admin\model\AdminGroupModel;
 class AuthService {
    private $error = '';
    private $AdminRuleModel;
    private $AdminGroupModel;

    public function __construct() {
        $this->AdminRuleModel = new AdminRuleModel();
        $this->AdminGroupModel = new AdminGroupModel();
    }
    
    //节点添加
    public function ruleAdd($post_data)
    {
        if(!$post_data){
            $this->error = '数据不能为空';
            return false;
        }
        
        $check = validate('AdminRule');
        if (!$check->check($post_data)) {
            $this->error = $check->getError();
            return false;
        }

        $result = $this->AdminRuleModel->ruleAdd($post_data);
        if(!$result) {
            $this->error = $this->AdminRuleModel->getError();
            return false;
        }
        $this->error = $this->AdminRuleModel->getError();
        return $result;
    }

    //节点编辑
    public function ruleEdit($post_data)
    {
        if(!$post_data){
            $this->error = '数据不能为空';
            return false;
        }
        
        $check = validate('AdminRule');
        if (!$check->check($post_data)) {
            $this->error = $check->getError();
            return false;
        }

        $result = $this->AdminRuleModel->ruleEdit($post_data);
        if(!$result) {
            $this->error = $this->AdminRuleModel->getError();
            return false;
        }
        $this->error = $this->AdminRuleModel->getError();
        return $result;
    }

    //删除节点
    public function ruleDel($rule_id = 0) {
        $rule_id = intval($rule_id);
        if($rule_id == 0){
             $this->error = '节点ID错误';
             return false;
        }

        $result = $this->AdminRuleModel->ruleDel($rule_id);
        if(!$result) {
            $this->error = $this->AdminRuleModel->getError();
            return false;
        }
        $this->error = $this->AdminRuleModel->getError();
        return $result;
    }

    /**
     * 清除节点缓存
     */
    public function clearNode()
    {
         $result = $this->AdminRuleModel->clearNode();
         if(!$result) {
             $this->error = $this->AdminRuleModel->getError();
             return false;
         }
         $this->error = $this->AdminRuleModel->getError();
         return true;
    }
    //更新角色
    public function saveRole($post_data)
    {
        if(!$post_data) {
            $this->error = '不存在数据';
            return  false;
        }
        $result = $this->AdminRuleModel->saveRole($post_data);
        if(!$result){
            $this->error = $this->AdminRuleModel->getError();
            return false;
        } 
        $this->error = $this->AdminRuleModel->getError();
        return $result;
    }

    //设置节点菜单状态
    public function setRuleStatus($rule_id = 0,$status) 
    {
        $rule_id = intval($rule_id);
        if($rule_id == 0){
            $this->error = '节点ID错误';
            return false;
        }
        $result = $this->AdminRuleModel->setRuleStatus($rule_id,$status);
        if(!$result) {
            $this->error = $this->AdminRuleModel->getError();
            return false;
        }
        $this->error = $this->AdminRuleModel->getError();
        return $result;
    }
    
        //设置验证权限
    public function setRuleOpen($rule_id = 0,$authopen) 
    {
        $rule_id = intval($rule_id);
        if($rule_id == 0){
            $this->error = '节点ID错误';
            return false;
        }
        $result = $this->AdminRuleModel->setRuleOpen($rule_id,$authopen);
        if(!$result) {
            $this->error = $this->AdminRuleModel->getError();
            return false;
        }
        $this->error = $this->AdminRuleModel->getError();
        return $result;
    }

    //设置排序
    public function ruleSort($rule_id = 0,$sort)
    {
        $result = $this->AdminRuleModel->ruleSort($rule_id,$sort);
        if(!$result) {
            $this->error = $this->AdminRuleModel->getError();
            return false;
        }
        $this->error = $this->AdminRuleModel->getError();
        return $result;
    }



    /**************************************** 角色管理*********************************************/

    //角色添加
    public function groupAdd($post_data)
    {
        if(!$post_data && !in_array($post_data)) {
        $this->error = '数据不存在';
        return false;
        }
    
        $check = validate('AdminGroup');
        if (!$check->check($post_data)) {
        $this->error = $check->getError();
        return false;
        }

        $result = $this->AdminGroupModel->groupAdd($post_data);
        if(!$result) {
        $this->error = $this->AdminGroupModel->getError();
        return false;
        }
    
        $this->error = $this->AdminGroupModel->getError();
        return $result;
    }

    public function groupEdit($post_data)
    {
        if(!$post_data && !in_array($post_data)) {
        $this->error = '数据不存在';
        return false;
        }
    
        $check = validate('AdminGroup');
        if (!$check->check($post_data)) {
        $this->error = $check->getError();
        return false;
        }

        $result = $this->AdminGroupModel->groupEdit($post_data);
        if(!$result) {
        $this->error = $this->AdminGroupModel->getError();
        return false;
        }
    
        $this->error = $this->AdminGroupModel->getError();
        return $result;
    }

    //查询角色
    public function getGroupInfo($group_id = 0)
    {
        $group_id = intval($group_id);
        $result = $this->AdminGroupModel->get($group_id);
        if(!$result){
            $this->error = '角色不存在';
            return false;
        }
        return $result;
    }
    //角色删除
    public function groupDel($group_id = 0)
     {
        $group_id = intval($group_id);
        if($group_id == 0){
             $this->error = 'ID错误';
             return false;
        }

        $result = $this->AdminGroupModel->groupDel($group_id);
        if(!$result) {
            $this->error = $this->AdminGroupModel->getError();
            return false;
        }
        $this->error = $this->AdminGroupModel->getError();
        return $result;
    }

    public function setGroupStatus($group_id = 0,$status){
        $group_id = intval($group_id);
        if($group_id == 0){
            $this->error = 'ID错误';
            return false;
        }
        $result = $this->AdminGroupModel->setGroupStatus($group_id,$status);
        if(!$result) {
            $this->error = $this->AdminGroupModel->getError();
            return false;
        }
        $this->error = $this->AdminGroupModel->getError();
        return $result;
    }

    //返回错误信息
	public function getError() {
		return $this->error;
	}
 }


?>