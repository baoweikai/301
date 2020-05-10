<?php

namespace app\admin\controller;


class Index extends \core\Controller
{    
    public function index()
    {
        return $this->success();
    }
    // 工作台
    public function workplace(){
        return $this->success($this->result);
    }
    //清除缓存
    public function clear(){
    }
}
