<?php
namespace app\common\model;

class Stat extends \core\Model
{
    // protected $pk = ['date', 'domain_id'];
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
}