<?php
namespace app\model;

class Jump extends \core\Model
{
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';
    //定义属性
    protected $type = [
        'is_use' => 'integer',
        'status' => 'integer',
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'update_at' => 'timestamp:Y-m-d H:i:s',
        // 'start_time' =>  'timestamp:Y-m-d H:i:s',
        'expire_time' =>  'timestamp:Y-m-d H:i:s',
    ];
    //是否过期
    protected function getIsExpireAttr($value, $data)
    {
        $status_arr = [0 => '否', 1 => '是'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }
    //状态
    protected function getStatusAttr($value, $data)
    {
        $status_arr = [0 => '关闭', 1 => '开启'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }

    //是否直跳
    protected function getIsStraightAttr($value, $data)
    {
        $straight_arr = [0 => '否', 1 => '是'];
        return isset($data['return_arr']) && $data['return_arr'] ? $straight_arr : ['val' => $value, 'text' => $straight_arr[$value]];
    }

    //开启状态
    protected function getIsStartAttr($value, $data)
    {
        $status_arr = [0 => '未开始', 1 => '已开始'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }

    
    //是否全局带参数
    protected function getIsParamAttr($value, $data)
    {
        $status_arr = [0 => '否', 1 => '是'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }
        
    //是否引量
    protected function getIsOpenAttr($value, $data)
    {
        $status_arr = [0 => '否', 1 => '是'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }

    protected  function  getStartTimeAttr($value,$data)
    {
        if(!$value){
            return '';
        }
        $startTime = explode(",",$value);
        return   isset($data["return_arr"]) && $data["return_arr"] ? $startTime : ['val'=>$value,'text'=>$startTime];
    }
}

?>