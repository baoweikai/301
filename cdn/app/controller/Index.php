<?php
namespace app\controller;

use app\BaseController;
use think\cache\driver\Redis;
use think\facade\Db;

class Index extends BaseController
{
    protected  $redis, $ip, $did = 0;
    protected function initialize(){
        //ip处理
        $this->ip = request()->ip();
        $ipLast = substr($this->ip, -1);
        $i = $ipLast % count(config('cache.stores'));
        //$redis配置
        $config = config('cache.stores.redis' . $i);

        $this->redis = new Redis($config);
    }

    public function index()
    {
        $host = request()->domain();
        //查询
        $map[] = [
            ["shield_url", '=', $host],
            ["is_expire", '=', 0],
            ["status", '=', 1]
        ];

        //查询数据
        $domain = $this->redis->get('domain' . $host);

        if($domain === null){
            $res = Db::name('domain')->where($map)->find();
            if($res !== null){
                $this->redis->set('domain' . $host, $res->toArray());
                $domain = $res->toArray();
            } else {
                $this->jump();
                return redirect('https://www.dt2277.com?301');
            }
        }

        $this->did = $domain['id'];
        //ip统计
        $this->ip();
        $rand = mt_rand(1, 100);
        //判断是否引量
        if($domain['is_open'] == 1 && $rand <= $domain['percent'] && $domain["cited_url"] && $this->contrast()){
            // 引量地址
            $jump_url = $domain["cited_url"];
            // 引量统计
            $this->cited();
        }else{
            // 跳转地址
            $request_url= urldecode(request()->url());
            $jump_url = $domain["is_param"] === 1 ? $domain["jump_url"] . $request_url : $domain['jump_url'];
            // 跳转统计
            $this->jump();
        }

        return redirect($jump_url);
    }

    /**
     * 跳转IP统计
     */
    private function ip()
    {
        $this->redis->handler()->sadd('domain_' . $this->did . '_' . date('m-d'), $this->ip);
    }

    //跳转统计
    private function jump()
    {
        $this->redis->inc('JumpCount_' . $this->did . '_' . date('m-d'));
    }

    //引量统计
    private function cited(){
        $this->redis->inc('CitedCount_' . $this->did . '_' . date('m-d'));
    }
    // 验证五天内该Ip是否引流
    private function contrast()
    {
        $lastAt = $this->redis->handler()->hget('ip' . $this->ip, $this->did);
        if($lastAt && time() - $lastAt < 3600 * 120){
            return false;
        }
        $this->redis->handler()->hset('ip' . $this->ip, $this->did, time());
        return false;
    }
}
