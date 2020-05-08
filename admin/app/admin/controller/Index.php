<?php
namespace app\admin\controller;

use think\facade\Db;

class Index extends \app\admin\Controller
{
    protected $middleware = ['auth'];
    public function index()
    {
        // 获取缓存数据
        $AuthRule = cache('AuthRule');

        if(!$AuthRule){
            $AuthRule = Db::name('AuthRule')->where('status', 1)->order('sort')->select()->toArray();
            cache('AuthRule', $AuthRule, 3600);
        }
        //声明数组
        $menus = [];
        foreach ($AuthRule as $key=>$val){
            $AuthRule[$key]['href'] = $val['href'];
            if($val['pid']==0){
                if(request()->uid > 1){
                    if(in_array($val['id'], $this->AuthRules)){
                        $menus[] = $val;
                    }
                }elseif(request()->uid === 1){
                    $menus[] = $val;
                }
            }
        }

        foreach ($menus as $k=>$v){
            foreach ($AuthRule as $kk=>$vv){
                if($v['id']==$vv['pid']){
                    if(request()->uid != 1) {
                        if (in_array($vv['id'], $this->AuthRules)) {
                           $menus[$k]['children'][]  = $vv;
                        }
                    }else{
                        $menus[$k]['children'][]  = $vv;
                    }
                }
            }
        }
        $this->result['menus'] = json_encode($menus,true);
        return $this->fetch();
    }

    public function main()
    {

        return $this->fetch();
    }


    //清除缓存
    public function clear(){
        $R = env('runtime_path');
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
