<?php
namespace app\admin\controller;

class AttributeController extends \app\admin\Controller
{
    protected $middleware = ['auth'];

    protected function _filter(&$map)
    {
        $keyword = input('keyword');
        if (!empty($keyword)) {
            $map[] = ['name','like', '%' . $keyword . '%'];
        }
    }
    
}