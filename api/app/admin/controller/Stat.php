<?php
namespace app\admin\controller;

use think\cache\driver\Redis;
use app\common\model\Domain;
use app\common\model\Stat as Model;

class Stat extends \core\Controller
{
    protected $middleware = ['auth'];
    protected $model = '\app\common\model\Stat'; // 对应表格
    protected $name = '管理员';

    public function index()
    {
        return $this->_index();
    }
    // 添加
    public function add()
    {
        return $this->_add();
    }
    // 保存
    public function save()
    {
        return $this->_save();
    }
    // 编辑
    public function edit($id)
    {
        return $this->_edit($id);
    }
    // 编辑
    public function update()
    {
        $configs = config('cache.stores');
        $list = [];
        $date = date('Ymd', strtotime('-1 hour'));
        foreach($configs as $config){
            $redis = new Redis($config);
            $IpCount = $redis->handler()->hgetall('IpCount' . $date);
            $JumpCount = $redis->handler()->hgetall('JumpCount' . $date);
            $CitedCount = $redis->handler()->hgetall('CitedCount' . $date);
            $today = date('Y-m-d', strtotime('-1 hour'));

            foreach($IpCount as $k => $val){
                if (!isset($list[$k])) {
                    $list[$k] = ['date' => $today, 'domain_id' => $k, 'ip_count' => 0, 'jump_count' => 0, 'cited_count' => 0];
                }
                $list[$k]['ip_count'] += $val;
            }
            foreach($JumpCount as $k => $val){
                $list[$k]['jump_count'] += $val;
            }
            foreach($CitedCount as $k => $val){
                $list[$k]['cited_count'] += $val;
            }
        }

        $ids = Model::where([['date', '=', $today], ['domain_id', 'in', array_keys($list)]])->column('id', 'domain_id');

        foreach($list as $k => $v){
            if(isset($ids[$k])){
                $list[$k]['id'] = $ids[$k];
            }
        }
        $model = new Model;
        $model->saveAll($list);
    }
}