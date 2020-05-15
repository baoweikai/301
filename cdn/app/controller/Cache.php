<?php
namespace app\controller;

use app\BaseController;
use think\cache\driver\Redis;
use think\facade\Db;
use app\model\CitedDomain;
use app\model\Group;

class Cache extends BaseController
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

    public function domain()
    {
        $rows = Db::name('domain')->column('id, shield_host, jump_host, percent, expire_at, is_param, status, is_open, group_id', 'shield_host');
        $domains = [];
        foreach($rows as $key => $row){
            $domains[$key] = json_encode($row);
        }
        $configs = config('cache.stores');
        foreach($configs as $config){
            $redis = new Redis($config);
            $redis->handler()->hdel('DomainList');
            $redis->handler()->hmset('DomainList', $domains);
        } 
    }
    public function cited(){

        $citeds = CitedDomain::cache();
        
        $groupId = $this->redis->get('DefaultGroupId');
        if($groupId === null){
            $groupId = Group::where('status', '=', 1)->order(['is_default' => 'desc', 'id' => 'ASC'])->value('id');
            
        }

        $configs = config('cache.stores');
        foreach($configs as $config){
            $redis = new Redis($config);
            $redis->set('Citeds', $citeds);
            $redis->set('DefaultGroupId', $groupId);
        } 
    }
    public function count(){
        $configs = config('cache.stores');
        $date = date('Ymd', strtotime('-1 hour'));
        foreach($configs as $config){
            $redis = new Redis($config);
            $redis->set('IpCount' . $date, null);
            $redis->set('JumpCount' . $date, null);
            $redis->set('CitedCount' . $date, null);
        } 
    }
}
