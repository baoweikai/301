<?php
namespace app\admin\controller;

class AttributeController extends \app\admin\Controller
{
    protected function initialize()
    {
        parent::initialize();
    }

    protected function _filter(&$map)
    {
        $keyword = input('keyword');
        if (!empty($keyword)) {
            $map[] = ['name','like', '%' . $keyword . '%'];
        }
    }
    
}