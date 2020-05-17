<?php
namespace app\controller;

use app\BaseController;
use think\cache\driver\Redis;
use think\facade\Db;
use app\model\CitedDomain;
use app\model\Group;

class Cache extends BaseController
{
    public function domain()
    {
        $rows = Db::name('domain')->column('id, shield_host, jump_host, percent, expire_at, is_param, status, is_open, group_id', 'shield_host');

        $domains = [];
        foreach($rows as $key => $row){
            $domains[$key] = serialize($row);
        }
        $configs = config('cache.stores');
        foreach($configs as $config){
            $redis = (new Redis($config))->handler();
            // $redis->del('DomainList');
            $redis->hmset('DomainList', $domains);
        } 
    }
    public function cited(){

        $citeds = CitedDomain::cache();
        $groupId = Group::where('status', '=', 1)->order(['is_default' => 'desc', 'id' => 'ASC'])->value('id');

        $configs = config('cache.stores');
        foreach($configs as $config){
            $redis = (new Redis($config))->handler();
            $redis->set('Citeds', serialize($citeds));
            $redis->set('DefaultGroupId', $groupId);
        }
    }
    public function count(){
        $configs = config('cache.stores');
        $date = date('Ymd', strtotime('-1 hour'));
        foreach($configs as $config){
            $redis = (new Redis($config))->handler();
            $redis->del('SsidList' . $date);
            $redis->del('IpCount' . $date);
            $redis->del('JumpCount' . $date);
            $redis->del('CitedCount' . $date);
            $redis->del('CitedSsidList');
        } 
    }
    public function citedIp(){
        $configs = config('cache.stores');
        // $date = date('Ymd', strtotime('-1 hour'));
        foreach($configs as $config){
            $redis = (new Redis($config))->handler();
            $redis->del('CitedSsidList');
        } 
    }
    public function realIp(){
        var_dump(ord('222'));die();
        cookie('SSID', uniqid(), 3600);
        // var_dump(cookie('SSID'));die();
        return cookie('SSID');
    }
}
