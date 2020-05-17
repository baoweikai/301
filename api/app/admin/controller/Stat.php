<?php
namespace app\admin\controller;

use think\cache\driver\Redis;
use app\common\model\Domain;
use app\common\model\Stat as Model;

class Stat extends \core\Controller
{
    protected $middleware = ['auth'];
    protected $model = '\app\common\model\Stat'; // 对应表格
    protected $with = ['domain'];
    protected $name = '管理员';

    public function index()
    {
        return $this->_index(['date' => [date('Y-m-d'), date('Y-m-d')]]);
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
    // 刷新数据
    public function frush()
    {
        $type = input('post.type/d', 0);
        $date = date('Y-m-d', $type > 0 ? strtotime('-' . $type . ' day') :  time());
        $configs = config('cache.stores');
        $list = [];
        $today = date('Ymd', strtotime($date));

        foreach($configs as $config){
            $redis = (new Redis($config))->handler();
            $IpCount = $redis->hgetall('IpCount' . $today);
            $JumpCount = $redis->hgetall('JumpCount' . $today);
            $CitedCount = $redis->hgetall('CitedCount' . $today);
 
            if(!isset($list[0])){
                $list[0] = ['date' => $date, 'domain_id' => 0, 'ip_count' => 0, 'jump_count' => 0, 'cited_count' => 0];
			}
            foreach($IpCount as $k => $val){
                if (!isset($list[$k])) {
                    $list[$k] = ['date' => $date, 'domain_id' => $k, 'ip_count' => 0, 'jump_count' => 0, 'cited_count' => 0];
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

        $ids = Model::where([['date', '=', $date], ['domain_id', 'in', array_keys($list)]])->column('id', 'domain_id');

        foreach($list as $k => $v){
            if(isset($ids[$k])){
                $list[$k]['id'] = $ids[$k];
            }
        }
        $model = new Model;
        if($model->saveAll($list)){
            $this->result['today'] = $this->result['yesterday'] = $this->result['sevenday'] = [0, 0, 0];
            $this->result['domainCount'] = Domain::where('status', 1)->count();

            $rows = Model::where('date', '>', date('Y-m-d', strtotime('-8 day')))->select();
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $sevenday = date('Y-m-d', strtotime('-7 day'));
            foreach($rows as $row){
                if($row->date == $today){
                    $this->result['today'][0] += $row->ip_count;
                    $this->result['today'][1] += $row->jump_count;
                    $this->result['today'][2] += $row->cited_count;
                }
                if($row->date == $yesterday){
                    $this->result['yesterday'][0] += $row->ip_count;
                    $this->result['yesterday'][1] += $row->jump_count;
                    $this->result['yesterday'][2] += $row->cited_count;
                }
                if($row->date >= $sevenday){
                    $this->result['sevenday'][0] += $row->ip_count;
                    $this->result['sevenday'][1] += $row->jump_count;
                    $this->result['sevenday'][2] += $row->cited_count;
                }
            }
            return $this->success($this->result);
            // return $this->success(['state' => 200, 'message' => '更新成功']);
        }
        return $this->error('更新失败', 201);
    }
}