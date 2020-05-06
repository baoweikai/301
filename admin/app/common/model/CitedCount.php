<?php
namespace app\common\model;

class CitedCount extends \think\Model
{
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = true;
    //定义属性
    protected $type = [
        'create_time' => 'timestamp:Y-m-d H:i:s',
    ];
}



?>