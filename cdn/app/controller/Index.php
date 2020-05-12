<?php
namespace app\controller;

use app\BaseController;
use think\cache\driver\Redis;
use think\facade\Db;
use app\model\CitedDomain;

class Index extends BaseController
{
    protected  $redis, $jump_url = '', $ip = '', $did = 0, $default = 'https://www.dt2277.com?301';
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
        $host = request()->host();
        //查询
        $map[] = [
            ["shield_host", '=', $host],
            ["status", '=', 1]
        ];
        //查询数据
        $domain = $this->redis->get('domain_' . $host);

        if(!$domain){
            $domain = Db::name('domain')->where($map)->field('id, jump_host, percent, expire_at, is_param, is_open, group_id')->find();
            if($domain !== null){      
                $this->redis->set('domain_' . $host, $domain);           
            } else {
                $this->cited();
                return redirect($this->jump_url);
            }
        }

        $this->did = $domain['id'];
        //ip统计
        $this->ip();
        $rand = mt_rand(1, 100);
        //判断是否引量
        if($domain['is_open'] == 1 && $domain['expire_at'] > time() && $rand <= $domain['percent'] && $this->contrast()){
            // 引量统计
            $this->cited($domain['group_id']);
        }else{
            // 跳转地址
            $query= urldecode(request()->query());
            $this->jump_url = $domain["is_param"] === 1 ? $domain["jump_host"] . '?' . $query : $domain['jump_host'];
            // 跳转统计
            $this->jump();
        }

        return redirect($this->jump_url);
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
    private function cited($groupId = null){
        $citeds = $this->redis->get('citeds');
        if($citeds === null){
            $citeds = CitedDomain::cache();
            $this->redis->set('citeds', $citeds);
        }
        $domains = $groupId !== null && isset($citeds[$groupId]) ? $citeds[$groupId] : [];
        $l = count($domains);
        $this->jump_url = $l > 0 ? $domains[mt_rand(0, $l - 1)] : $this->default;
        $this->redis->inc('CitedCount_' . $this->did . '_' . date('m-d'));
    }
    // 验证五天内该Ip是否引流
    private function contrast()
    {
        $lastAt = $this->redis->handler()->hget('IpList', $this->did . $this->ip);
        if($lastAt && time() - $lastAt < 3600 * 120){
            return false;
        }
        $this->redis->handler()->hset('IpList', $this->did . $this->ip, time());
        return true;
    }
}
