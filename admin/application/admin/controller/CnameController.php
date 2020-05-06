<?php
namespace app\admin\controller;

use app\common\model\AttributeModel;
use think\Json;
class CnameController extends BaseController
{
    protected $attributeModel,$attrList;
    public function initialize()
    { 
        parent::initialize();
        $this->attributeModel = new AttributeModel();
        $this->attrList = $this->attributeModel->getAttrList();
        $this->assign("attrList",$this->attrList);
    }


    public function  index()
    {
        if (request()->isPost()) {
            try { 
                $keyword = input('keyword');
				//列表过滤器，生成查询Map对象
                $map = [];
                if (!empty($keyword)) {
        
                    $map[] = ['a.cname|at.name','like', '%' . $keyword . '%'];

                }
                $join_arr = [
                    0 => ['Attribute at', 'at.id = a.attr_id']
                ];
                $field = 'a.*,at.name';
                $result = $this->getListJson('Cname', $map, $join_arr, $field);
                Json::success('ok', $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        
        }else{
			return $this->fetch();
		}
    }
}

?>