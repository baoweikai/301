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
        $this->result['ipCount'] = Stat::where('date', date('Y-m-d', strtotime('-1 day')))->sum('ip_count');
        $this->result['jumpCount'] = Stat::where('date', date('Y-m-d', strtotime('-1 day')))->sum('jump_count');
        $this->result['citedCount'] = Stat::where('date', date('Y-m-d', strtotime('-1 day')))->sum('cited_count');
        $this->result['ipCount'] = Stat::where('date', date('Y-m-d', strtotime('-1 day')))->sum('ip_count');
        $this->result['domainCount'] = Domain::where('status', 1)->count();

        return $this->success($this->result);
    }
    //清除缓存
    public function clear(){
    }
}
