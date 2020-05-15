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
        $this->result['ipCount'] = $this->result['jumpCount'] = $this->result['citedCount'] = [0, 0, 0];
        $this->result['domainCount'] = Domain::where('status', 1)->count();
        $date = date('Y-m-d', strtotime('-8 day'));
        $rows = Stat::where('date', '>', $date)->select();
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $today = date('Y-m-d', strtotime('-1 day'));
        $sevenday = date('Y-m-d', strtotime('-7 day'));
        foreach($rows as $row){
            if($row->date = $today){
                $this->result['ipCount'][0] += $row->ip_count;
                $this->result['jumpCount'][0] += $row->jump_count;
                $this->result['citedCount'][0] += $row->cited_count;
            }
            if($row->date = $yesterday){
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
        return $this->success($this->result);
    }
    //清除缓存
    public function clear(){
    }
}
