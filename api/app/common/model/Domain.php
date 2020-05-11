<?php
namespace app\common\model;

class Domain extends \core\Model
{
    // protected $table = 'domain';     // 系统管理员表
    //定义属性
    protected $type = [
        'user_id' => 'integer',
        'is_use' => 'integer',
        'status' => 'integer',
        'is_param' => 'integer',
        'is_open' => 'integer',
        'start_time' => 'array',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['shield_url', 'jump_url', 'cited_url', 'percent', 'user_id', 'is_param', 'is_open', 'start_time', 'status'];
    protected $filter = ['shield_url', 'jump_url', 'is_param', 'is_open', 'user_id', 'status'];  // 搜索项
    protected $rule = [
        'shield_url'  => 'require',
        'jump_url'  => 'require',
        'cited_url'  => 'require',
        'percent'  => 'require|integer',
        'is_param'  => 'integer',
        'is_open'  => 'integer',
        'is_param'  => 'require|integer',
        'status'   => 'integer',
    ];
    // 用户
    public function user (){
        return $this->belongsTo(User::class)->bind(['account' => 'account']);
    }
    // 
    public static function onAfterUpdate($model)
    {
    	cache('domain_' . $model->domain, $model->getData(['jump_url', 'cited_url', 'is_param', 'is_open', 'percent', 'start_time']));
    }
    // 
    public static function onAfterDelete($model)
    {
		cache('domain_' . $model->domain, null);
    }
}