<?php
namespace app\common\model;

class Domain extends \core\Model
{
    //定义属性
    protected $type = [
        'is_use' => 'integer',
        'status' => 'integer',
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'update_at' => 'timestamp:Y-m-d H:i:s',
        'start_time' =>  'array',
    ];
}