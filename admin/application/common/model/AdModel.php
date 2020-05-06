<?php
namespace app\common\model;
/**
 * 广告
 */
class AdModel extends BaseModel
{
    protected $resultSetType = 'collection';
    protected $auto = ['status', 'sort', 'litpic'];
    protected $autoWriteTimestamp = true;
    //定义属性
    protected $type = [
        'sort' => 'integer',
        'status' => 'integer',
        'create_time' => 'timestamp:Y-m-d H:i:s',
        'update_time' => 'timestamp:Y-m-d H:i:s',
    ];

    protected function setSortAttr($value)
    {
        return $value ? $value : 50;
    }

    protected function getStatusAttr($value, $data)
    {
        $status_arr = [0 => '关闭', 1 => '开启'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }


    protected function getOpensAttr($value, $data)
    {
        $opens_arr = [0 => '当前窗口', 1 => '新窗口'];
        return isset($data['return_arr']) && $data['return_arr'] ? $opens_arr : ['val' => $value, 'text' => $opens_arr[$value]];
    }
}


?>