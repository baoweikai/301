<?php
namespace app\common\model;

class Cname extends \core\Model
{
    // protected $table = 'cname';     // 别名表
    protected $autoWriteTimestamp = true;    // 自动时间戳
    protected $updateTime = false;    // 自动时间戳
    //定义属性
    protected $type = [
        'cate_id' => 'integer',
        'is_use' => 'integer',
        'status' => 'integer',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['name', 'cate_id', 'is_use', 'status'];
    protected $filter = ['name', 'cate_id', 'user_id', 'is_use', 'status'];  // 搜索项
    protected $rule = [
        'name'  => 'require|unique:cname',
        'cate_id'  => 'require',
        'is_use'  => 'require',
        'status'   => 'integer'
    ];
    // 分类
    public function cate (){
        return $this->belongsTo(Cate::class)->bind(['cate_name' => 'name']);
    }
}