<?php
namespace app\common\model;

use think\cache\driver\Redis;

class Group extends \core\Model
{
    protected $autoWriteTimestamp = true;    // 自动时间戳
    //定义属性
    protected $type = [
        'status' => 'integer',
        'is_default'   => 'integer',
        'create_at' => 'timestamp:Y-m-d H:i:s',
        'update_at' => 'timestamp:Y-m-d H:i:s',
    ];
    protected $fillable = ['name', 'status', 'is_default'];
    protected $filter = ['name', 'status', 'is_default'];  // 搜索项
    protected $rule = [
        'name'  => 'require|unique:cate',
        'status'   => 'integer',
    ];
    // 
    public static function onAfterUpdate($model)
    {
        self::cache();
    }
    // 
    public static function onAfterDelete($model)
    {
		self::cache();
    }
    // 
    public static function cache() {
        $rows = CitedDomain::where('status', 1)->hasWhere('group_id', ['status' => 1])->select();
        $groupId = Group::where('status', 1)->order(['is_default' => 'desc', 'id' => 'asc'])->find();
        $citeds = [];
        foreach($rows as $row){
            $citeds[$row->group_id][] = $row->host;
        }
        $configs = config('cache.stores');
        foreach($configs as $config){
            $redis = new Redis($config);
            $redis->set('Citeds', $citeds);
            $redis->set('DefaultGroupId', $groupId);
        }
    }
}