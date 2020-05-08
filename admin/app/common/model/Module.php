<?php
namespace app\common\model;
/**
 * 模型
 */
class Module extends \think\Model
{

    //定义属性
    protected $type = [
        'sort' => 'integer',
        'status' => 'integer',
    ];
    protected function getStatusAttr($value, $data)
    {

        $status_arr = [0 => '禁用', 1 => '开启'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }
}


?>