<?php
namespace app\controller;

use app\BaseController;
use think\cache\driver\Redis;
use think\facade\Db;
use app\model\CitedDomain;
use app\model\Group;

class Error extends BaseController
{
    public function __call($method, $args)
    {
        return $this->index();
    }
    protected  $redis, $date, $jump_url = '', $ip = '', $did = 0, $default = 'https://www.95egg.com';
    protected function initialize(){
        // ip处理
        $this->ip = get_ip();
        $ipLast = substr($this->ip, -1);
        $this->date = date('Ymd');
        $i = $ipLast % count(config('cache.stores'));
        // $redis配置
        $config = config('cache.stores.redis' . $i);
        $this->redis = new Redis($config);
    }

    public function index()
    {
        // 如果不存在DomainList缓存，则建立
        if(!$this->redis->exists('DomainList')){
            $rows = Db::name('domain')->field('id, shield_host, jump_host, percent, expire_at, is_param, status, is_open, group_id')->select();
            $domains = [];
            foreach($rows as $row){
                $domains[$row['shield_host']] = serialize($row);
            }
            $this->redis->hmset('DomainList', $domains);
        }
        $host = request()->host();
        // 查询数据
        $domain = $this->redis->hget('DomainList', $host);

        if($domain === false){
            $this->ip();
            $this->cited();
            return redirect($this->jump_url);
        }
        $domain = unserialize($domain);

        //验证后缀是否是图片，js，css等
        if(verifyExt(request()->ext())){
            return redirect($domain["jump_host"] . request()->url());
        }

        $this->did = $domain['id'];
        // ip统计
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
            $url= request()->url();
            $this->jump_url = $domain["is_param"] === 1 ? $domain["jump_host"] . $url : $domain['jump_host'];
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
        // 如果ip 不存在当日ip，则增加ip统计
        if(!$this->redis->sismember('IpList' . $this->date, $this->did . '_' . $this->ip)){
            $this->redis->hincrby('IpCount' . $this->date, $this->did, 1);
            $this->redis->sadd('IpList' . $this->date,  $this->did . '_' . $this->ip);
        }   
    }

    //跳转统计
    private function jump()
    {
        $this->redis->hincrby('JumpCount' . $this->date, $this->did, 1);
        
        $fh = fopen(runtime_path() . '/' . $this->date, "a");
        fwrite($fh, date('Y-m-d H:i:s'). '|' . $this->ip . ':' . request()->host() . '=>' . $this->jump_url . "\n");
        fclose($fh);
    }

    //引量统计
    private function cited($groupId = null){
        $citeds = $this->redis->get('Citeds');
        if($citeds === null){
            $citeds = CitedDomain::cache();
            $this->redis->set('Citeds', $citeds);
        }
        if($groupId === null){
            $groupId = $this->redis->get('DefaultGroupId');
            if($groupId === null){
                $groupId = Group::where('status', '=', 1)->order(['is_default' => 'desc', 'id' => 'ASC'])->value('id');
                $this->redis->set('DefaultGroupId', $groupId);
            }    
        }
        $domains = $groupId !== null && isset($citeds[$groupId]) ? $citeds[$groupId] : [];
        $l = count($domains);
        $this->jump_url = $l > 0 ? $domains[mt_rand(0, $l - 1)] : $this->default;
		$this->jump_url .= '?' . $this->did;
		
        $this->redis->hincrby('CitedCount' . $this->date, $this->did, 1);
        // 引流ip列表
        $this->redis->hset('CitedIpList', $this->did . $this->ip, time());

        $fh = fopen(runtime_path() . '/' . $this->date, "a");
        fwrite($fh, date('Y-m-d H:i:s'). '|' . $this->ip . ':' . request()->host() . '=>' . $this->jump_url . "\n");
        fclose($fh);
    }
    // 验证五天内该Ip是否引流
    private function contrast()
    {
        $lastAt = $this->redis->hget('CitedIpList', $this->did . $this->ip);
        if($lastAt && time() - $lastAt < 3600 * 120){
            return false;
        }
        return true;
    }
}