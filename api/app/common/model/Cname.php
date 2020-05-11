<?php
namespace app\common\model;

class Cname extends \core\Model
{
    // protected $table = 'cname';     // 系统管理员表
    //定义属性
    protected $type = [
        'cate_id' => 'integer',
        'is_use' => 'integer',
        'status' => 'integer',
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'update_at' => 'timestamp:Y-m-d H:i:s',
    ];
    protected $fillable = ['name', 'cate_id', 'is_use', 'status'];
    protected $filter = ['name', 'cate_id', 'is_use', 'status'];  // 搜索项

    public function cate (){
        return $this->belongsTo(Cate::class)->bind(['cate_name' => 'name']);
    }
}