<?php
namespace app\common\model;

class UserCname extends \think\Model
{
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = true;
    //定义属性
    protected $type = [
        'create_time' => 'timestamp:Y-m-d H:i:s',
        'use_time' => 'timestamp:Y-m-d H:i:s',
    ];


    
    protected function getIsUseAttr($value, $data)
    {
        $status_arr = [0 => '否', 1 => '是'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }

    protected function getIsUniqueAttr($value, $data)
    {
        $status_arr = [0 => '否', 1 => '是'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }
}


?>