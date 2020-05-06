<?php
namespace app\admin\controller;

class  LinksController  extends BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    protected function _filter(&$map)
    {
        $keyword = input('keyword');
        if (!empty($keyword)) {
            $map[] = ['title','like', '%' . $keyword . '%'];
        }
    }
}



?>