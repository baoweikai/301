<?php
namespace app\user\controller;

class IndexController extends BaseController{
    
    public function initialize()
    {
        parent::initialize();
    }
    public function index()
    {

        $adminRule = db('navs')->where('status=1')->order('sort')->all();

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
        $this->assign('menus',json_encode($menus,true));

        return $this->fetch();
    }
      

    public function  userInfo(){
        $map["id"] = USER_UID;
        $info = model("User")->get($map);
        $this->assign('title', '个人信息');
        $this->assign('info', json_encode($info, true));
        return $this->fetch();
    }
    
    public function main()
    {

        return $this->fetch();
    }

}