<?php
namespace app\model;

class CitedDomain extends \core\Model
{
    //定义属性
    protected $type = [
        'weight' => 'integer',
        'status' => 'integer',
        'create_at' => 'timestamp:m-d H:i'
    ];
    protected $fillable = ['host', 'weight', 'status'];
    protected $filter = ['host', 'weight', 'status'];  // 搜索项
    protected $rule = [
        'host'  => 'require|unique:host',
        'weight'  => 'require|integer|length:1',
        'status'   => 'integer',
    ];
    // 分组
    public function group (){
        return $this->belongsTo(Group::class)->bind(['group_name' => 'name']);
    }
    // 
    public static function cache() {
        $rows = CitedDomain::where('status', 1)->field(['host', 'group_id'])->select();
        $citeds = [];
        foreach($rows as $row){
            $citeds[$row->group_id][] = $row->host;
        }
        return $citeds;
    }
}