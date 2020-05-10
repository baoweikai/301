<?php
namespace app\common\model;

class Recharge extends \core\Model
{
    //定义属性
    protected $type = [
        'create_at' => 'timestamp:m-d H:i',
    ];
    public function user (){
        return $this->belongsTo(User::class)->bind(['account']);
    }
}