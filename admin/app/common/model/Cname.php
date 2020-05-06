<?php
namespace app\common\model;

class CnameModel   extends \think\Model
{
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = true;
    //定义属性
    protected $type = [
        'is_use' => 'integer',
        'status' => 'integer',
        'create_time' => 'timestamp:Y-m-d H:i:s',
        'update_time' => 'timestamp:Y-m-d H:i:s',
    ];


    protected function getStatusAttr($value, $data)
    {
        $status_arr = [0 => '关闭', 1 => '开启'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }

    protected function getIsUseAttr($value, $data)
    {
        $status_arr = [0 => '未使用', 1 => '已使用'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }
}

?>