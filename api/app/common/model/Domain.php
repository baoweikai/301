<?php
namespace app\common\model;

class Domain extends \core\Model
{
    // protected $table = 'domain';     // 系统管理员表
    protected $autoWriteTimestamp = true;    // 自动时间戳
    // protected $updateTime = false;    // 自动时间戳
    //定义属性
    protected $type = [
        'user_id' => 'integer',
        'is_use' => 'integer',
        'status' => 'integer',
        'is_param' => 'integer',
        'is_open' => 'integer',
        'cited_range' => 'array',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['shield_host', 'jump_host', 'percent', 'user_id', 'is_param', 'is_open', 'cited_range', 'status'];
    protected $filter = ['shield_host', 'jump_host', 'is_param', 'is_open', 'user_id', 'status'];  // 搜索项
    protected $rule = [
        'shield_host'  => 'require',
        'jump_host'  => 'require',
        'percent'  => 'require|integer'
    ];
    // 用户
    public function user (){
        return $this->belongsTo(User::class)->bind(['account' => 'account']);
    }
    // 分组
    public function group (){
        return $this->belongsTo(Group::class)->bind(['group_name' => 'name']);
    }
    // 
    public static function onAfterUpdate($model)
    {
    	cache('domain_' . $model->domain, $model->column('jump_host, is_param, is_open, percent, cited_range'));
    }
    // 
    public static function onAfterDelete($model)
    {
		cache('domain_' . $model->domain, null);
    }
}