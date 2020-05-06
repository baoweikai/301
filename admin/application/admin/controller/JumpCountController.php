<?php
namespace app\admin\controller;

use think\Db;
use think\Json; 
class JumpCountController extends BaseController
{
    protected $time_slot;
    public function initialize()
    {
        parent::initialize();
        $this->time_slot = config("time_slot");
        $this->assign("timeSlot",$this->time_slot);
    }

    public function index()
    {
        if (request()->isPost()) {
            try { 
                $keyword = input('keyword');
                //列表过滤器，生成查询Map对象
                $j_id = input("jId",0,'int');
                if($j_id>0){
                    $map[] = ['j_id','eq',$j_id];
                }else{
                    $map = [];
                }
  
                if (!empty($keyword)) {
                    $map[] = ['get_ip|jump_url','like', '%' . $keyword . '%'];
                }
                $list_row = input('limit', 10);
                $field="*";
                $result =  Db::connect("db_config9")->name("jump_count")->where($map)->field($field)->order("create_time desc")->paginate($list_row, false, ['query' => request()->param()]);
                Json::success('ok', $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        } else {
            return $this->fetch();
        }
    }
    /**
     * 跳转统计
     */
    public function jumpHighcharts()
    {

            $jump_id = input("id",0,'int');
            //查询被墙域名
            $shield_url = model("Jump")->where("id=".$jump_id)->value("shield_url");
            $title=$shield_url."-曲线图";
            $this->assign('title', $title);
            //曲线图
            $time = strtotime(date("Y-m-d",time()));
            $timeHourData = config("time_slot_hour"); 
            $jumpTodayNumer = array();
            $pvTodayNumer = array();
            $hour_time = array();
            $result = cache("num_".$jump_id);
            if(!$result){
                foreach($timeHourData  as $key=>$val){
                    $hour = explode("-",$val);
                    $start_time = strtotime($hour[0]);
                    $end_time = strtotime($hour[1]);
                    $jumpTodayNumer[$key] = Db::connect("db_config9")->name("jump_count")->where("j_id=".$jump_id." and create_time>=".$start_time." and create_time<".$end_time)->group("get_ip")->count("id");
                    $pvTodayNumer[$key]=  Db::connect("db_config9")->name("jump_count")->where("j_id=".$jump_id." and create_time>=".$start_time." and create_time<".$end_time)->sum("pv");
                    $hour_time[$key] = "'".date("H点",$start_time)."-".date("H点",$end_time)."'";

                }
                $result["jumpTodayNumer"] = implode(',',$jumpTodayNumer);
                $result["pvTodayNumer"] = implode(',',$pvTodayNumer);
                $result["hour_time"] = implode(',',$hour_time);
                cache("num_".$jump_id,$result,120);
            }
            $this->assign('result', $result);
            return $this->fetch(); 

    }
}

?>