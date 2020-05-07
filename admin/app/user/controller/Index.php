<?php
namespace app\user\controller;

use think\facade\Db;

class Index extends \app\user\Controller
{
    public function index()
    {
        $adminRule = Db::name('navs')->where('status=1')->order('sort')->select()->toArray();

        //声明数组
        $menus = [];
        foreach ($adminRule as $key=>$val){
         //  $adminRule[$key]['href'] = url($val['href']);
            $adminRule[$key]['href'] = $val['href'];
            if($val['pid']==0){
                $menus[] = $val;
            }
        }

        foreach ($menus as $k=>$v){
            foreach ($adminRule as $vv){
                if($v['id']==$vv['pid']){
                    $menus[$k]['children'][]  = $vv;
                }
            }
        }
        $this->result['menus'] = json_encode($menus,true);

        return $this->fetch();
    }
      

    public function  userInfo(){
        $map["id"] = USER_UID;
        $info = Db::name("User")->where($map)->find();
        $this->result['title'] = '个人信息';
        $this->result['info'] = json_encode($info, true);
        return $this->fetch();
    }
    
    public function main()
    {

        return $this->fetch();
    }

}