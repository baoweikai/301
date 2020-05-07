<?php
namespace app\admin\controller;

use think\facade\Json; 
class CitedCount extends \app\admin\Controller
{
    protected $time_slot;
    protected function initialize()
    {
        parent::initialize();
        $this->time_slot = config("time_slot");
        $this->result['timeSlot'] = $this->time_slot;
    }

    public function index()
    {
        if (request()->isPost()) {
            try { 
                $keyword = input('keyword');
                //列表过滤器，生成查询Map对象
                $j_id = input("jId",0,'int');
                if($j_id>0){
                    $map[] = ['a.j_id','eq',$j_id];
                }else{
                    $map = [];
                }

                if (!empty($keyword)) {
                    $map[] = ['a.get_ip|a.jump_url|j.shield_url','like', '%' . $keyword . '%'];
                }
                $join_arr = [
                    0 => ['Jump j', 'j.id = a.j_id'],
                ];
                $field = 'a.*,j.shield_url';
                $result = $this->getListJson('CitedCount', $map, $join_arr, $field);
                return $this->success($result);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        } else {
            return $this->fetch();
        }
    }
}

?>