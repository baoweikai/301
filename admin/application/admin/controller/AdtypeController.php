<?php
namespace app\admin\controller;

use app\common\model\AdtypeModel;
use think\Json;

/**
 * 广告分类
 */
class AdtypeController  extends BaseController
{
    public function initialize()
    {
        parent::initialize();
    }
    protected function _filter(&$map)
    {
        $keyword = input('keyword');
        if (!empty($keyword)) {
            $map[] = ['typename|tag|description','like', '%' . $keyword . '%'];
        }
    }

    //删除
    public  function del()
    {
        try {
            $typeid = intval(input('id'));
            $AdtypeModel = new AdtypeModel();
            //删除分类
            $result = $AdtypeModel->delAdtype($typeid);
            if(!$result) {
                Json::fail($AdtypeModel->getError());
            }
            Json::success($AdtypeModel->getError());
        }catch (\Exception $e) {
            Json::fail($e->getMessage());
        }
    }
}