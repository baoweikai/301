<?php
namespace app\admin\controller;

use app\common\model\AdtypeModel;
use think\facade\Json;
use think\facade\Db;

/**
 * 广告分类
 */
class AdtypeController extends \app\admin\Controller
{
    protected $middleware = ['auth'];

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
                return $this->error($AdtypeModel->error);
            }
            return $this->success($AdtypeModel->error);
        }catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}