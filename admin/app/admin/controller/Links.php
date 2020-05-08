<?php
namespace app\admin\controller;

class Links extends \app\admin\Controller
{
    protected $middleware = ['auth'];
    protected function _filter(&$map)
    {
        $keyword = input('keyword');
        if (!empty($keyword)) {
            $map[] = ['title','like', '%' . $keyword . '%'];
        }
    }
}



?>