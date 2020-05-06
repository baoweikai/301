<?php
namespace app\common\model;
/**
 * 友情链接
 */
class LinksModel extends BaseModel
{
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = true;
    //定义属性
    protected $type = [
        'sort' => 'integer',
        'status' => 'integer',
        'create_time' => 'timestamp:Y-m-d H:i:s',
        'update_time' => 'timestamp:Y-m-d H:i:s',
    ];

    protected function getStatusAttr($value, $data)
    {
        $status_arr = [0 => '禁用', 1 => '开启'];
        return isset($data['return_arr']) && $data['return_arr'] ? $status_arr : ['val' => $value, 'text' => $status_arr[$value]];
    }

}


?>