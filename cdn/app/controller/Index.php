<?php
namespace app\controller;

use app\BaseController;
use think\cache\driver\Redis;
use think\facade\Db;
use app\model\CitedDomain;
use app\model\Group;

class Index extends BaseController
{
    protected  $redis, $jump_url = '', $ip = '', $did = 0, $default = 'https://www.95egg.com';
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
            ["shield_host", '=', $host]
        ];
        //查询数据
        $domain = $this->redis->get('domain_' . $host);

        if(!$domain){
            $domain = Db::name('domain')->where($map)->field('id, jump_host, percent, expire_at, is_param, status, is_open, group_id')->find();
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
        // 如果网站已失效，直接引流
        if($domain['status'] === 0){
            $this->cited($domain['group_id']);
        }
        // 否则如果已开启引流，且五天内为引流者，按照概率引流
        else if($domain['is_open'] == 1 &&  mt_rand(1, 100) <= $domain['percent'] && $this->contrast()){
            // 引量统计
            $this->cited($domain['group_id']);
        }else{
            // 跳转地址
            $query= request()->query();
            $this->jump_url = $domain["is_param"] === 1 && $query !== '' ? $domain["jump_host"] . '?' . $query : $domain['jump_host'];
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
        $this->redis->handler()->sadd('IpCount_' . $this->did . '_' . date('m-d'), $this->ip);
    }

    //跳转统计
    private function jump()
    {
        $fh = fopen(runtime_path() . '/' . date('Y-m-d'), "a");
        fwrite($fh, date('Y-m-d H:i:s'). '|' . $this->ip . ':' . request()->host() . '=>' . $this->jump_url . "\n");
        fclose($fh);

        $this->redis->inc('JumpCount_' . $this->did . '_' . date('m-d'));
    }

    //引量统计
    private function cited($groupId = null){
        $citeds = $this->redis->get('Citeds');
        if($citeds === null){
            $citeds = CitedDomain::cache();
            $this->redis->set('Citeds', $citeds);
        }
        $groupId = $this->redis->get('DefaultGroupId');
        if($groupId === null){
            $groupId = Group::where('status', '=', 1)->orcer(['is_default' => 'desc', 'id' => 'ASC'])->value('id');
            $this->redis->set('DefaultGroupId', $groupId);
        }
        $domains = $groupId !== null && isset($citeds[$groupId]) ? $citeds[$groupId] : [];
        $l = count($domains);
        $this->jump_url = $l > 0 ? $domains[mt_rand(0, $l - 1)] : $this->default;
		$this->jump_url .= '?' . $this->did;
		
        $fh = fopen(runtime_path() . '/' . date('Y-m-d'), "a");
        fwrite($fh, date('Y-m-d H:i:s'). '|' . $this->ip . ':' . request()->host() . '=>' . $this->jump_url . "\n");
        fclose($fh);

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
