<?php
namespace app\index\controller;

use app\index\Controller;
use think\cache\driver\Redis;
use think\facade\Db;

class Index extends Controller
{
    protected  $redis,$redisHandler,$getIp,$getEndIp,$timeout,$jumpIpKey,$jumpIpValue,$redisConfig;
    public function initialize(){
        //ip处理
        $this->getIp = request()->ip();
        $this->getEndIp = substr($this->getIp,-1);
        $this->jumpIpKey = "ip".$this->getIp;//跳转IP的键
        $this->jumpIpValue = $this->getIp;//跳转IP值
        //$redis配置
        $redisConfig = config('cache.stores.redis' . $this->getEndIp);
        $this->redis = new Redis($redisConfig);
        //过期时间
        $this->timeout = $redisConfig['timeout'];
    }

    public function index()
    {
        $host = request()->domain();
        $request_uri = request()->url();
        //查询
        $map[] = [
            ["shield_url", '=', $host],
            ["is_expire", '=', 0],
            ["status", '=', 1],
            ["is_start", '=', 1]
        ];

        //查询数据
        $info = $this->redis->get('Jump' . $host);

        if($info === null){
            $info = Db::name('jump')->where($map)->find();
            $this->redis->set('Jump' . $host, $info);
        }
        if($info){
            $is_open = $info["is_open"]["val"];

            //ip验证
            $end_ip = explode(",", $info["end_ip"]);
            $getEndIpNumber = substr(get_ip(),-1);
            $ip_bool = in_array($getEndIpNumber, $end_ip);//查找尾数是否存在，存在true 

            //判断是否引量
            if($is_open == 1 && $ip_bool == true && $info["admin_jump_url"] && $this->contrast() == false){
                //跳转地址
                $jump_url = $info["admin_jump_url"];
                //ip统计
                $this->getJumpIp();
                //引量统计
                $this->getShield($info["id"],$info['jump_url']);
                //跳转统计
                $this->getJump($info["id"],$info['jump_url']);
                
            }else{
                //跳转链接
                $request_uri= urldecode($request_uri);
                $jump_url = $info["is_param"]["val"] == 1 ? $info["jump_url"].$request_uri :$info['jump_url'];
                //ip统计
                $this->getJumpIp();
                //跳转统计
                $this->getJump($info["id"],$info['jump_url']);
            }

            // header('HTTP/1.1 301 Moved Permanently');
            return redirect($jump_url);
        }else{
            //没有找到数据跳转
            return redirect('https://www.dt2277.com?301');
            //exit;
        }
    }

    /**
     * 跳转IP统计,过期时间重新统计
     */
    private function getJumpIp()
    {
        $ipInfo = $this->redis->get($this->jumpIpKey);
        $this->redis->set($this->jumpIpKey, $this->jumpIpValue);
        // if(!$ipInfo){
        //     $rd->set($this->jumpIpKey,$this->jumpIpValue);
        // }
    }

    //跳转统计
    private function getJump($jump_id = 0, $jump_url='')
    {
        $jumpId = (int) $jump_id;
        $jumpUrl = trim($jump_url);
        //时间
        $time = time();
        $string = $jumpId."|".$this->getIp."|".$jumpUrl."|".$time;
        //获取引量统计
        $this->redis->handler()->sadd('jumpList', $string);
        //设置过期时间
        $setTime = $this->redis->get("setTimeout");
        if(!$setTime){
            $this->redis->set("setTimeout",$this->timeout);
            $this->redis->handler()->setTimeout('jumpList',$this->timeout);
        }

    }

    //引量统计
    private function  getShield($jump_id = 0,$jump_url=''){
        $jumpId = (int) $jump_id;
        $jumpUrl = trim($jump_url);
        //时间
        $time = time();
        $string = $jumpId."|".$this->getIp."|".$jumpUrl."|".$time;
        //获取引量统计
        $this->redis->handler()->sadd('citedList',$string);
        //设置过期时间
        $setTime = $this->redis->get("setTimeoutShield");
        if(!$setTime){
            $this->redis->set("setTimeoutShield",$this->timeout);
            $this->redis->handler()->setTimeout('citedList',$this->timeout);
        }
    }

    //ip验证
    private function contrast()
    {
        return bolling($this->redis->get($this->jumpIpKey));
    }
}
