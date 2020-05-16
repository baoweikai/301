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
        $today = date('Y-m-d', strtotime('-1 hour'));
        foreach($configs as $config){
            $redis = (new Redis($config))->handler();
            $IpCount = $redis->hgetall('IpCount' . $date);
            $JumpCount = $redis->hgetall('JumpCount' . $date);
            $CitedCount = $redis->hgetall('CitedCount' . $date);
 
            if(!isset($list[0])){
                $list[0] = ['date' => $today, 'domain_id' => 0, 'ip_count' => 0, 'jump_count' => 0, 'cited_count' => 0];
                // $list[0]['ip_count'] += isset($IpCount[0]) ? $IpCount[0] : 0;
			}
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
        if($model->saveAll($list)){
            $this->result['ipCount'] = $this->result['jumpCount'] = $this->result['citedCount'] = [0, 0, 0];
            $this->result['domainCount'] = Domain::where('status', 1)->count();
            $date = date('Y-m-d', strtotime('-8 day'));
            $rows = Model::where('date', '>', $date)->select();
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $today = date('Y-m-d');
            $sevenday = date('Y-m-d', strtotime('-7 day'));
            foreach($rows as $row){
                if($row->date == $today){
                    $this->result['ipCount'][0] += $row->ip_count;
                    $this->result['jumpCount'][0] += $row->jump_count;
                    $this->result['citedCount'][0] += $row->cited_count;
                }
                if($row->date == $yesterday){
                    $this->result['ipCount'][1] += $row->ip_count;
                    $this->result['jumpCount'][1] += $row->jump_count;
                    $this->result['citedCount'][1] += $row->cited_count;
                }
                if($row->date >= $sevenday){
                    $this->result['ipCount'][2] += $row->ip_count;
                    $this->result['jumpCount'][2] += $row->jump_count;
                    $this->result['citedCount'][2] += $row->cited_count;
                }
            }
            return $this->success($this->result, '更新成功');
            // return $this->success(['state' => 200, 'message' => '更新成功']);
        }
        return $this->error('更新失败', 201);
    }
}