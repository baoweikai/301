<?php
namespace app\common\model;

use think\cache\driver\Redis;

class CitedDomain extends \core\Model
{
    //定义属性
    // protected $table = 'cited_domain';
    protected $autoWriteTimestamp = true;    // 自动时间戳
    protected $updateTime = false;    // 自动时间戳
    protected $type = [
        'weight' => 'integer',
        'status' => 'integer',
        'create_at' => 'timestamp:m-d H:i'
    ];
    protected $fillable = ['host', 'weight', 'status', 'group_id'];
    protected $filter = ['host', 'group_id', 'status'];  // 搜索项
    protected $rule = [
        'host'  => 'require|unique:cited_domain',
        'weight'  => 'require|integer|length:1',
        'status'   => 'integer'
    ];
    // 分组
    public function group (){
        return $this->belongsTo(Group::class)->bind(['group_name' => 'name']);
    }
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
        $rows = CitedDomain::where('status', 1)->select();
        $citeds = [];
        foreach($rows as $row){
            $citeds[$row->group_id][] = $row->host;
        }
        $configs = config('cache.stores');
        foreach($configs as $config){
            $redis = new Redis($config);
            $redis->set('Citeds', $citeds);
        } 
    }
}