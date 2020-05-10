<?php
namespace app\user\controller;

// use think\facade\Db;
// use app\admin\model\Rule;

class Index extends \core\Controller
{
    public function index()
    {
        return $this->success($this->result);
    }
    
    public function main()
    {
        return $this->success();
    }

}