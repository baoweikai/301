<?php
namespace app\common\model;

class Stat extends \core\Model
{
    // protected $table = 'stat';     // 流量统计表
    protected $type = [
        'date' => 'date',
        'domain_id' => 'integer',
        'ip_count' => 'integer',
        'jump_count' => 'integer',
        'cited_count' => 'integer',
        'create_at' => 'timestamp:m-d H:i',
        'update_at' => 'timestamp:m-d H:i',
    ];
    protected $fillable = ['date', 'domain_id', 'ip_count', 'jump_count', 'cited_count'];
    protected $filter = ['date', 'domain_id'];  // 搜索项
    // 域名
    public function domain (){
        return $this->belongsTo(Domain::class)->bind(['shield_host' => 'shield_host']);
    }
    // 状态搜索
    public function searchDomainIdAttr($query, $value, $data)
    {
        $value !== null && $query->hasWhere('domain', Domain::where('shield_host', 'like', $value . '%'));
    }
    // 创建日期搜索
    public function searchDateAttr($query, $value, $data)
    {
        is_array($value) && $query->where('date', '>=', $value[0])->where('date', '<=', $value[1]);
    }
}