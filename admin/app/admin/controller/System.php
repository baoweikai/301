<?php
namespace app\admin\controller;

use think\facade\Json;
use think\facade\Request;
use think\facade\Env;
use app\common\model\System;
use think\facade\Db;

/**
 * 系统设置
 */
class System extends \app\admin\Controller
{
    public $sysModel,$sysData,$path;
    public function initialize()
    {
        parent::initialize();
        $this->path =  Env::get("app_path").'/common/config/system.php';
        $this->sysData = include $this->path;
        $this->sysModel = new System();
    }

    //参数设置
    public function  index()
    {
        if(request()->isPost()){
            try { 

              $data = request()->post();
              $this->sysData["is_open"] = $data["is_open"];
              $this->sysData["start_time"] = $data["start_time"];
              $this->sysData["end_time"] = $data["end_time"];
              $this->sysData["end_ip"] = $data["end_ip"];
              $this->sysData["jump_id"] = $data["jump_id"];
              $this->sysData["jumpUrl"] = $data["jumpUrl"];
              $arr ="<?php\r\nreturn " . var_export($this->sysData, true) . ";\r\n?>";
              $result = file_put_contents($this->path,$arr);
              if($result) {
                Json::success("修改成功");
              }else{
                Json::fail('修改失败， 请修改' . $this->path . '的写入权限');
              }
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        }else{
            $this->result['title'] = '参数设置';
            $this->result['sys'] = json_encode($this->sysData,true);
            return $this->fetch();
        }
    }

    /**
     * 清除跳转数据
     */
    public function clearJump()
    {
        if (request()->isPost()) {
            try {            
                $type = (int)request()->post("type");
                if($type==1){
                    $today = strtotime(date("Y-m-d"));
                    $map[] = ["create_time","<",$today];
                    Db::connect("db_config9")->name("jump_count")->where($map)->delete();
                    Db::connect("db_config9")->name("cited_count")->where($map)->delete();
                }else{
                    
                    Db::connect("db_config9")->name("jump_count")->execute("truncate table cz_jump_count"); 
                    Db::connect("db_config9")->name("cited_count")->execute("truncate table cz_cited_count"); 
                }
                Json::success("清除成功");
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        } else {
            $jumpcount =  Db::connect("db_config9")->name("jump_count")->count();
            $citedcount = Db::connect("db_config9")->name("cited_count")->count();
            $this->result['jumpcount'] = $jumpcount;
            $this->result['citedcount'] = $citedcount;
            return $this->fetch();
        }
    }


    public function jumpList()
    {
        if (request()->isPost()) {
            try { 
                $keyword = input('keyword');
				//列表过滤器，生成查询Map对象
                $map[] = [
                    0=>  ["a.is_expire",'eq',0],
                    1=>  ["a.status",'eq',1],
                    2=>  ["a.is_start",'eq',1],
                    3=>  ["a.start_time",'lt',time()],
                    // 4=> ["a.expire_time","between",[0,time()]],
              ];
                if (!empty($keyword)) {
        
                    $map[] = ['a.jump_url|a.shield_url|u.account','like', '%' . $keyword . '%'];

                }
                $join_arr = [
                    0 => ['User u', 'u.id = a.user_id'],
                    1 => ['Attribute at', 'u.attr_id = at.id'],
                    2 => ['UserCname uc', 'uc.user_id = u.id'],
                    3 => ['Cname c', 'c.id = uc.c_id'],
                ];
                $field = 'a.*,c.cname,u.account';
                $result = $this->getListJson('Jump', $map, $join_arr, $field);
                Json::success('ok', $result);
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        } else {
            return $this->fetch();
        }
    }

    //站点设置
    public function system(){
        if (request()->isPost()) {
            try { 
                $data = Request::except('file');
                $result = $this->sysModel->addSystem($data);
                if($result){
                    adminAddLog(UID,$this->now_user["username"].",".CONTROLLER_NAME."修改了系统设置");
                    Json::success($this->sysModel->getError(), $result);
                }else{
                    Json::fail($this->sysModel->getError());
                }
        
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        } else {
            $sys_id = 1;
            $system = $this->sysModel->getSysInfo($sys_id);
            $this->result['title'] = '系统设置';
            $this->result['system'] = json_encode($system,true);
            return $this->fetch();
      
        }
    }

    /**
     * 生成首页
     *
     * @return void
     */
    public function  createHtml()
    {
        if (request()->isPost()) {
            try { 
             $result = $this->staticFetch(true,Env::get("root_path")."application/home/view/index/index.html");
             if(!$result){
                 Json::fail("生成失败");
             }
             Json::success("生成成功");
            } catch (\Exception $e) {
                Json::fail($e->getMessage());
            }
        }else{
          return $this->fetch();
        }
    }
}


?>