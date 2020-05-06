<?php
namespace app\admin\controller;

use app\common\model\CitedCount;
use app\common\model\JumpCount;
use think\facade\Cache;
use think\facade\Json;

//脚本
class Script extends \app\admin\Controller
{
    protected  $redis,$jumpCountModel,$citedCountModel;
    public function initialize(){
        // ini_set("max_execution_time", "1800");
        $this->redis = Cache::store('redis')->handler();
        $this->jumpCountModel = new JumpCount();
        $this->citedCountModel = new CitedCount();
    }


    public function index()
    {
        $jumpList =  $this->redis->smembers("JumpList");     
        //时间查找
        $time = time();
        $start_time = date("Y-m-d",$time);//开始时间
        $end_time = date("Y-m-d",strtotime("+1 day",strtotime($start_time)));
        $list = array_filter($jumpList, function($v) use ($start_time, $end_time) { $arr = explode("|",$v); return   $arr[3] >= strtotime($start_time) && $arr[3] <= strtotime($end_time);});    
        //三分钟前
        $mtime= $time -120;
        $lists =  array_filter($list, function($item) use ($mtime, $time) { $arrs = explode("|",$item); return   $arrs[3] >= $mtime && $arrs[3] <= $time;}); 
        foreach($lists as $val){
            $array = explode("|",$val);
            $data["j_id"] = (int)$array[0];
            $data["get_ip"] = trim($array[1]);
            $data["jump_url"] = trim($array[2]);
            $data["create_time"] =(int) $array[3];
            //查询
            // $res = $this->jumpCountModel->where($data)->count("id");
            // if($res == 0){
                $this->jumpCountModel->insert($data); 
            // }

        }

        unset($list);
        unset($jumpList);
        unset($data);
        unset($array);
        Json::success("成功");
    }


    public function addCited()
    {

        $CitedList =  $this->redis->smembers("CitedList");        
        //时间查找
        $time = time();
        $start_time = date("Y-m-d",$time);//开始时间
        $end_time = date("Y-m-d",strtotime("+1 day",strtotime($start_time)));
        $list = array_filter($CitedList, function($v) use ($start_time, $end_time) { $arr = explode("|",$v); return   $arr[3] >= strtotime($start_time) && $arr[3] <= strtotime($end_time);});    
        foreach($list as $val){
            $array = explode("|",$val);
            $data["j_id"] = (int)$array[0];
            $data["get_ip"] = trim($array[1]);
            $data["jump_url"] = trim($array[2]);
            $data["create_time"] =(int) $array[3];
            //查询
            $res = $this->citedCountModel->where($data)->count("id");
            if($res == 0){
                $this->citedCountModel->insert($data); 
            }

        }
        unset($CitedList);
        unset($list);
        unset($data);
        unset($array);
        Json::success("成功");
    }

}
?>