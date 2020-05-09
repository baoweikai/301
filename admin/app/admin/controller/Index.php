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
        $uid = request()->uid;
        $where = [['status', '=', 1]];
        $uid > 1 && ($where[] = ['id', 'in', $AuthRule]);

        $rows = Db::name('AuthRule')->where($where)->column('id, title, href, pid', 'id');
        // 声明数组
        $menus = [];
        foreach ($rows as $row){
            if($row['pid'] === 0) {
                $menus[$row['id']]  = $row;
            } else{
                if(!isset($menus[$row['pid']])){
                    $menus[$row['pid']] = $rows[$row['pid']];
                }
                $menus[$row['pid']]['children'][]  = $row;
            }
        }
        // var_dump($menus);die();
        $this->result['menus'] = $menus;
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
