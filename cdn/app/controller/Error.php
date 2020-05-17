<?php
namespace app\controller;

use app\BaseController;
use think\cache\driver\Redis;
use think\facade\Db;
use app\model\CitedDomain;
use app\model\Group;

class Error extends BaseController
{
    protected  $redis, $date, $jump_url = '', $ssid = '', $did = 0, $default = 'https://www.95egg.com';
    public function __call($method, $args)
    {
        return $this->index();
    }
    protected function initialize(){
        // SSID处理
        $this->ssid = cookie('SSID');
        if($this->ssid === null){
            $this->ssid = uniqid(mt_rand(0,9));
            cookie('SSID', $this->ssid);
        }
        $ssidLast = ord($this->ssid);
        $this->date = date('Ymd');
        $i = $ssidLast % count(config('cache.stores'));
        // $redis配置
        $config = config('cache.stores.redis' . $i);
        $this->redis = (new Redis($config))->handler();
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
            $this->ssid();
            $this->cited();
            return redirect($this->jump_url, 301);
        }
        $domain = unserialize($domain);

        //验证后缀是否是图片，js，css等
        if(verifyExt(request()->ext())){
            return redirect($domain["jump_host"] . request()->url(), 301);
        }

        $this->did = $domain['id'];
        // ssid统计
        $this->ssid();
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

        return redirect($this->jump_url, 301);
    }
    /**
     * uv统计
     */
    private function ssid()
    {
        // 如果ssid不存在当日ssid列表，则增加uv统计
        if(!$this->redis->sismember('SsidList' . $this->date, $this->did . '_' . $this->ssid)){
            $this->redis->hincrby('IpCount' . $this->date, $this->did, 1);
            $this->redis->sadd('SsidList' . $this->date,  $this->did . '_' . $this->ssid);
        }   
    }

    //跳转统计
    private function jump()
    {
        $this->redis->hincrby('JumpCount' . $this->date, $this->did, 1);
        
        $fh = fopen(runtime_path() . '/' . $this->date, "a");
        fwrite($fh, date('Y-m-d H:i:s'). '|' . $this->ssid . ':' . request()->host() . '=>' . $this->jump_url . "\n");
        fclose($fh);
    }

    //引量统计
    private function cited($groupId = null){
        $citeds = $this->redis->get('Citeds');
        if($citeds === false){
            $citeds = CitedDomain::cache();
            $this->redis->set('Citeds', serialize($citeds));
        } else {
            $citeds = unserialize($citeds);
        }
        if($groupId === null){
            $groupId = $this->redis->get('DefaultGroupId');
            if($groupId === false){
                $groupId = Group::where('status', '=', 1)->order(['is_default' => 'desc', 'id' => 'ASC'])->value('id');
                $this->redis->set('DefaultGroupId', $groupId);
            }    
        }
        $domains = isset($citeds[$groupId]) ? $citeds[$groupId] : [];
        $l = count($domains);
        $this->jump_url = $l > 0 ? $domains[mt_rand(0, $l - 1)] : $this->default;
        $this->jump_url .= '?' . $this->did;
        
        // 验证后缀非 html，php， asp等
        if(verifyExt(request()->ext())){
            return redirect($this->jump_url, 301);
        }
        
        $this->redis->hincrby('CitedCount' . $this->date, $this->did, 1);
        // 引量ssid列表
        $this->redis->hset('CitedSsidList', $this->did . $this->ssid, time());

        $fh = fopen(runtime_path() . '/' . $this->date, "a");
        fwrite($fh, date('Y-m-d H:i:s'). '|' . $this->ssid . ':' . request()->host() . '=>' . $this->jump_url . "\n");
        fclose($fh);
    }
    // 验证五天内该Ip是否引流
    private function contrast()
    {
        $lastAt = $this->redis->hget('CitedSsidList', $this->did . $this->ssid);
        if($lastAt && time() - $lastAt < 3600 * 120){
            return false;
        }
        return true;
    }
}