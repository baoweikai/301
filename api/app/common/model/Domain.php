<?php
namespace app\common\model;

use think\cache\driver\Redis;

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
        'expire_at' => 'timestamp:Y-m-d',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['shield_host', 'jump_host', 'percent', 'user_id', 'is_param', 'is_open', 'expire_at', 'cited_range', 'status'];
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
    public static function onAfterWrite($model)
    {
        self::afterWrite($model);
    }
    // 
    public static function onAfterDelete($model)
    {
        self::afterWrite($model);
    }
    public static function afterWrite($model){
        $rows = self::where('id', 'in', $model->updateIds)->select()->toArray();
        $data = [];
        foreach($rows as $row){
            $data[$row['shield_host']] = serialize($row);
        }
        $configs = config('cache.stores');
        foreach($configs as $config){
            $redis = (new Redis($config))->handler();
            $redis->hmset('DomainList', $data);
        }
    }
    // 域名搜索
    public function searchShieldHostAttr($query, $value, $data)
    {
        !empty($value) && $query->where('shield_host', 'like', $value . '%');
    }
    // 用户搜索
    public function searchUserIdAttr($query, $value, $data)
    {
        !empty($value) && $query->where('user_id', $value);
    }
    // 状态搜索
    public function searchIsOpenAttr($query, $value, $data)
    {
        $value !== null && $value !== '' && $query->where('is_open', '=', $value);
    }
    // 状态搜索
    public function searchStatusAttr($query, $value, $data)
    {
        $value !== null && $value !== '' && $query->where('status', '=', $value);
    }
}