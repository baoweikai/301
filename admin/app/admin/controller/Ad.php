<?php
namespace app\admin\controller;

use think\facade\Json;
use app\common\model\Adtype;
/**
 * 广告管理
 */
class Ad extends \app\admin\Controller
{
    protected  $AdtypeModel;
    public function initialize()
    {
        parent::initialize();
        $this->AdtypeModel = new Adtype();
        $adtypeList = $this->AdtypeModel->adTypeAll();
        $this->result['adtypeList'] = $adtypeList;
    }


    public function index()
    {
        if (request()->isPost()) {
            try { 
                $keyword = input('keyword');
				//列表过滤器，生成查询Map对象
                $map = [];
                if (!empty($keyword)) {
        
                    $map[] = ['a.title|at.typename','like', '%' . $keyword . '%'];

                }
        
                $join_arr = [
                    0 => ['Adtype at', 'at.id = a.typeid', 'LEFT']
                ];
                $field = 'a.*,at.typename';
                $result = $this->getListJson('Ad', $map, $join_arr, $field);
                Json::success('ok', $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        } else {
            return $this->fetch();
        }
    }
}