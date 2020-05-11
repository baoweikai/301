<?php
namespace app\common\model;

class UserCname extends \core\Model
{
    //定义属性
    protected $type = [
        'create_at' => 'timestamp:m-d H:i',
        'use_at' => 'timestamp:m-d H:i',
    ];
}