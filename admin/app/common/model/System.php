<?php
namespace app\common\model;
/**
 * 系统设置
 */
class System  extends \think\Model
{
    protected $resultSetType = 'collection';
    /**
     * 获取信息
     */
    public function getSysInfo($sys_id = 1) {
        $map['id'] = intval($sys_id);

        $sys_info = $this->get($map);
        if ($sys_info) {
			$sys_info = $sys_info->toArray();
        }
        
        return $this->error = "查询成功";
        return $sys_info;
    }

    //添加修改
    public  function addSystem($data = [])
    {
        if(!is_array($data)){
            return $this->error = '数据错误';
            return false;
        }
        //  $validate = validate($data);
        // if (!$validate->check($data)) {
        //     return $this->error = $validate->getError();
        //     return false;
        // }
        $sys_id = intval($data['id']);
        if($sys_id > 0) {
            $result = $this->where(['id'=>$sys_id])->update($data);
        }else{
            $result = $this->create($data);
        }

        if($result){
            return $this->error ="编辑成功";
            return $result;
        }else{
            return $this->error ="编辑失败";
            return false; 
        }
    }
}
?>