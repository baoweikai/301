<?php
namespace app\common\model;

class UserCname extends \core\Model
{
    //定义属性
    protected $type = [
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'use_time' => 'timestamp:Y-m-d H:i:s',
    ];
}