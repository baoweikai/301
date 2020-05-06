<?php
namespace app\admin\model;

use think\Model;

class AdminRule extends Model
{

    protected $autoWriteTimestamp = true;
    protected $insert = ['create_time'];  
    protected $update = ['update_time'];  

    protected function getStatusAttr($value,$data)
    {
        $status_arr = [0=>'隐藏',1=>'显示'];
        return isset($data['return_arr'])&&$data['return_arr'] ? $status_arr : ['val'=>$value,'text'=>$status_arr[$value]];
    }
 

    //添加节点
    public function ruleAdd($post_data) 
    {
        if(!$post_data){
            $this->error = '数据不存在';
            return false;
        }
        $result =  $this->create($post_data);
        if(!$result) {
            $this->error = '创建失败';
            return false;
        }
        cache('adminRule', NULL);
        cache('adminRuleList', NULL);
        $this->error = '创建成功';
        return $result;
    }

    //编辑节点
    public function  ruleEdit($post_data){
        if(!$post_data){
            $this->error = '数据不存在';
            return false;
        }
        if((int)$post_data['id'] == 0) {
            $this->error = '信息不存在';
            return false;
        }
        $result =  $this->update($post_data);
        if(!$result) {
            $this->error = '修改失败';
            return false;
        }
        cache('adminRule', NULL);
        cache('adminRuleList', NULL);
        $this->error = '修改成功';
        return $result;
    }

    public function  ruleDel($rule_id = 0)
    {
        $rule_id = intval($rule_id);
        if( $rule_id == 0) {
            $this->error = 'ID错误';
            return false;
        }
        $result = $this->destroy($rule_id);
        if(!$result) {
            $this->error = '删除失败';
            return false;
        }

        $this->error = '删除成功';
        return $result;
    }

    //清除节点缓存
    public function clearNode(){
        $result = $this->where('pid','neq',0)->select();
        foreach ($result as $k=>$v){
            $p =  $this->where('id',$v['pid'])->find();
            if(!$p){
              $this->where('id',$v['id'])->delete();
            }
        }
        cache('adminRule', NULL);
        cache('adminRuleList', NULL);
        $this->error = '清除成功';
        return true;
    }
    //设置节点状态
    public function setRuleStatus($rule_id = 0,$status)
    {
        $rule_id = intval($rule_id);
        if( $rule_id == 0) {
            $this->error = 'ID错误';
            return false;
        }
        $map['id'] = $rule_id;
        $data['status'] = intval($status);
        $result = $this->where($map)->update($data);
        if(!$result) {
            $this->error = '设置失败';
            return false;
        }
        cache('adminRule', NULL);
        cache('adminRuleList', NULL);
        $this->error = '设置成功';
        return $result;
    }


    //设置验证权限
    public function setRuleOpen($rule_id = 0,$authopen)
    {
        $rule_id = intval($rule_id);
        if( $rule_id == 0) {
            $this->error = 'ID错误';
            return false;
        }
        $map['id'] = $rule_id;
        $data['authopen'] = intval($authopen);
        $result = $this->where($map)->update($data);
        if(!$result) {
            $this->error = '设置失败';
            return false;
        }
        cache('adminRule', NULL);
        cache('adminRuleList', NULL);
        $this->error = '设置成功';
        return $result;
    }


    //设置排序
    public function ruleSort($rule_id =0 ,$sort)
    {
        $rule_id = intval($rule_id);
        if( $rule_id == 0) {
            $this->error = 'ID错误';
            return false;
        }
        $map['id'] = $rule_id;
        $data['sort'] = intval($sort);
        $result = $this->where($map)->update($data);
        if(!$result) {
            $this->error = '设置失败';
            return false;
        }
        cache('adminRule', NULL);
        cache('adminRuleList', NULL);
        $this->error = '设置成功';
        return $result; 
    }
}

?>