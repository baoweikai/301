<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
class AdminGroupModel extends Model
  {
      
    protected $autoWriteTimestamp = true;
    protected $insert = ['create_time'];  
    protected $update = ['update_time'];  
    
    protected function getStatusAttr($value,$data)
    {
        $status_arr = [0=>'关闭',1=>'开启'];
        return isset($data['return_arr'])&&$data['return_arr'] ? $status_arr : ['val'=>$value,'text'=>$status_arr[$value]];
    }

    public  function groupAdd($post_data)
    {
        $check = validate('AdminGroup');
        if (!$check->check($post_data)) {
           $this->error = $check->getError();
           return false;
        }
    
        $result =  $this->create($post_data);
        if(!$result) {
            $this->error = '创建失败';
            return false;
        }
        $this->error = '创建成功';
        return $result;
    }

    
    public  function groupEdit($post_data)
    {
        $check = validate('AdminGroup');
        if (!$check->check($post_data)) {
           $this->error = $check->getError();
           return false;
        }
    
        $result =  $this->update($post_data);
        if(!$result) {
            $this->error = '编辑失败';
            return false;
        }
        $this->error = '编辑成功';
        return $result;
    }

    //设置状态
    public function setGroupStatus($group_id = 0,$status)
    {
        $group_id = intval($group_id);
        if( $group_id == 0) {
            $this->error = 'ID错误';
            return false;
        }
        $map['id'] = $group_id;
        $data['status'] = intval($status);

        $result = $this->where($map)->update($data);
        if(!$result) {
            $this->error = '设置失败';
            return false;
        }
        $this->error = '设置成功';
        return $result;
    }
    

    //删除
    public function  groupDel($group_id = 0)
    {
        $group_id = intval($group_id);
        if( $group_id == 0) {
            $this->error = 'ID错误';
            return false;
        }
        $result = $this->destroy($group_id);
        if(!$result) {
            $this->error = '删除失败';
            return false;
        }

        $this->error = '删除成功';
        return $result;
    }

    //角色列表
    public  function  groupList()
    {
        $map['status'] = 1;
        $result = $this->field('id,title')->all($map);
        return $result; 
    }
  }

?>