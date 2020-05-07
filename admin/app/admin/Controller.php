<?php
declare (strict_types = 1);

namespace app\admin;

use think\App;
use think\exception\ValidateException;
use think\facade\Validate;
use think\facade\View;
use think\facade\Db;

/**
 * 控制器基础类
 */
abstract class Controller
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    protected $mod,$role,$system,$nav,$menudata,$cache_model,$categorys,$module,$moduleid,$adminRules,$HrefId;
    protected $result = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        return $this->initialize();
    }

    /**
     * 后台控制器基础类
     */
    protected function initialize()
    {
        define('UID', is_login());
		define('MODULE_NAME', app('http')->getName());
		define('CONTROLLER_NAME', request()->controller());
        define('ACTION_NAME', request()->action());
        if (!UID && CONTROLLER_NAME != "Identity") {
			//转到登录页面
            $_SESSION["refurl"] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
            header('Location: ' . request()->domain() . '/admin/identity/login');
            die();
		}
        //当前操作权限ID


        if( UID != 1){
			$this->HrefId = Db::name('AdminRule')->where('href','/'.CONTROLLER_NAME .'/'.ACTION_NAME)->value('id');
            //当前管理员权限
			$map['a.id'] = UID;
			// $join_arr =['AdminGroup g', 'a.group_id = g.id'];
            $rules = Db::name('AdminUser')->alias('a')->leftjoin('AdminGroup g', 'a.group_id = g.id')->where($map)->value('g.rules');
			$this->adminRules = explode(',',$rules);

            if($this->HrefId){
                if(!in_array($this->HrefId,$this->adminRules)){
                   return $this->error("您无此操作权限");
                }
            }
		}
			
		$this->cache_model=array('Module','AdminRule','Category','Posid','Field','System','cm');
		//$this->cache_model=array('Module','AdminRule','Category','System','cm');
        foreach($this->cache_model as $r){
            if(!cache($r)){
                savecache($r);
            }
        }
        $this->system = cache('System');
        $this->categorys = cache('Category');
		$this->module = cache('Module');
		$this->mod = cache('Mod');
		$this->rule = cache('AdminRule');
		$this->cm = cache('cm');

        $this->result['now_user'] = cache("user_auth_" . session('admin'));
	}

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    protected function fetch($path = ''){
        View::assign($this->result);
        return View::fetch($path);
    }
    // json
    protected function error($msg, $code = 201, $result = []){
        $ret = [
            'state' => $code,
            'message'  => $msg,
            'result' => $result,
        ];

        return json($ret, $code);
    }
    // json
    protected function success($result = [], $msg = ''){
        $ret = [
            'state' => 200,
            'message'  => $msg,
            'result' => $result,
        ];

        return json($ret, 200);
    }
}
