<?php
namespace app\admin\controller;

class Error extends \core\Controller
{
    public function __call($method, $args)
    {
        return $this->error('系统错误');
    }
}