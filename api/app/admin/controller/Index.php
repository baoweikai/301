<?php

namespace app\admin\controller;

use app\common\model\Stat;
use app\common\model\Domain;

class Index extends \core\Controller
{    
    public function index()
    {
        return $this->success();
    }
    // 工作台
    public function workplace(){
        $this->result['today'] = $this->result['yesterday'] = $this->result['sevenday'] = [0, 0, 0];
        $this->result['domainCount'] = Domain::where('status', 1)->count();
        $date = date('Y-m-d', strtotime('-7 day'));
        $rows = Stat::where('date', '>=', $date)->select();
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $today = date('Y-m-d');
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
    }
    //清除缓存
    public function clear(){
    }
}
