<?php
namespace app\admin\controller;

use com\Backup;
use think\facade\Json;
use think\facade\Db;

class Database extends \app\admin\Controller
{
    protected $db = '', $datadir;
    public function initialize()
    {
        parent::initialize();
        $this->config = [
            'path' => './Data/',//数据库备份路径
            'part' => 20971520,//数据库备份卷大小
            'compress' => 0,//数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level' => 9 //数据库备份文件压缩级别 1普通 4 一般  9最高
        ];
        $this->db = new Backup($this->config);
    }

    public function index()
    {
        if (request()->isPost()) {
            try {
                $list = $this->db->dataList();
                $total = 0;
                foreach ($list as $k => $v) {
                    $list[$k]['size'] = format_bytes($v['data_length']);
                    $total += $v['data_length'];
                }

                $result = [
                    'data' =>$list,
                    'total' => format_bytes($total),
                    'tableNum' => count($list)
                ];
                Json::success('获取成功', $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        }else{
           return $this->fetch();
        }
    }

    //优化
    public function optimize()
    {
        try {
            $tables = input('tables/a');
            if (empty($tables)) {
                Json::fail('请选择要优化的表！');
            }
            if ($this->db->optimize($tables)) {
                Json::success('数据表优化成功！');
            } else {
                Json::fail('数据表优化出错请重试！');
            }
        } catch (\Exception $e) {
            Json::fail($e->getMessage());
        }
    }

    //修复
    public function repair()
    {
        try {
            $tables = input('tables/a');
            if (empty($tables)) {
                Json::fail('请选择要修复的表！');
            }
            if ($this->db->repair($tables)) {
                Json::success('数据表修复成功！');
            } else {
                Json::fail('数据表修复出错请重试！');
            }
        } catch (\Exception $e) {
            Json::fail($e->getMessage());
        }
    }


     //备份
    public function backup()
    {
        try {
            $tables = input('post.tables/a');
            if (!empty($tables)) {
                foreach ($tables as $table) {
                    $this->db->setFile()->backup($table, 0);
                }
                Json::success('备份成功！');
            } else {
                Json::fail('请选择要备份的表！');
            }
        } catch (\Exception $e) {
            Json::fail($e->getMessage());
        }
    }




    //备份列表
    public function backupList()
    {
        if(request()->isPost()){
            try {
                $result = $this->db->fileList();
                Json::success('Ok',$result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        }else{
            return  $this->fetch();
        }
    }

    
    //执行还原数据库操作
    public function import()
    {
        if (request()->isPost()) {
            try{
                $time = input('time');
                $result = $this->db->getFile('timeverif', $time);
                $this->db->setFile($result)->import(1);
                Json::success('导入成功',$result);
            } catch(\Exception $e){
                Json::fail($e->getMessage());
            }
    
        }else{
            Json::fail('请求错误');
        }
    }

    //下载sql文件
    public function downFile()
    {
        if (request()->isGet()) {
            try {
                $time = input('time');
                $this->db->downloadFile($time);
                Json::success('下载成功',$result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }

        } else {
            Json::fail('请求错误');
        }
    }


    //删除sql文件
    public function delSqlFiles()
    {
        if (request()->isPost()) {
            $time = input('post.time');
            if ($this->db->delFile($time)) {
                Json::success('备份文件删除成功！');
            } else {
                Json::fail('备份文件删除失败，请检查权限！');
            }
        }else{
            Json::fail('请求错误');
        }
    }

}
?>