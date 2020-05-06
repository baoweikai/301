<?php
namespace app\admin\controller;

use think\facade\Env;
use think\facade\Json;

class Index extends \app\admin\Controller
{
    public function index()
    {
        // 获取缓存数据
        $adminRule = cache('adminRule');

        if(!$adminRule){
            $adminRule = db('AdminRule')->where('status=1')->order('sort')->select();
            cache('adminRule', $adminRule, 3600);
       }

        //声明数组
        $menus = [];
        foreach ($adminRule as $key=>$val){
         //  $adminRule[$key]['href'] = url($val['href']);
            $adminRule[$key]['href'] = $val['href'];
            if($val['pid']==0){
                if(UID != 1){
                    if(in_array($val['id'],$this->adminRules)){
                        $menus[] = $val;
                    }
                }else{
                    $menus[] = $val;
                }
            }
        }

        foreach ($menus as $k=>$v){
            foreach ($adminRule as $kk=>$vv){
                if($v['id']==$vv['pid']){
                    if(UID != 1) {
                        if (in_array($vv['id'], $this->adminRules)) {
                           $menus[$k]['children'][]  = $vv;
                        }
                    }else{
                        $menus[$k]['children'][]  = $vv;
                    }
                }
            }
        }
        $this->result['menus',json_encode($menus,true);
        return $this->fetch();
    }

    public function main()
    {

        return $this->fetch();
    }


    //清除缓存
    public function clear(){
        $R = Env::get('runtime_path');
        if (!$this->_deleteDir($R)) {
            return $this->error('清除缓存失败');
        } 
        $result['url'] = url('/index/index');
        return $this->success('清除成功',$result);
    }

    private function _deleteDir($R)
    {
        $handle = opendir($R);
        while (($item = readdir($handle)) !== false) {
            if ($item != '.' and $item != '..') {
                if (is_dir($R . '/' . $item)) {
                    $this->_deleteDir($R . '/' . $item);
                } else {
                    if (!unlink($R . '/' . $item))
                        die('error!');
                }
            }
        }
        closedir($handle);
        return rmdir($R);
    }

}
