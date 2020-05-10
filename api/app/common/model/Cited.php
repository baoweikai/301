<?php
namespace app\common\model;

class Cited extends \core\Model
{
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = true;
    //定义属性
    protected $type = [
        'create_at' => 'timestamp:Y-m-d H:i:s',
    ];

    public function domain (){
        return $this->belongsTo(Domain::class);
    }
}