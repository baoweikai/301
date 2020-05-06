<?php
declare (strict_types = 1);

namespace app\user;

use think\App;
use think\exception\ValidateException;
use think\facade\Validate;
use think\facade\View;

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
        $this->initialize();
    }

    public function initialize()
    {
        define('USER_UID', is_user_login());
		define('MODULE_NAME',request()->module());
		define('CONTROLLER_NAME',request()->controller());
		define('ACTION_NAME', request()->action());
        if (!USER_UID && (CONTROLLER_NAME != "Publics" || CONTROLLER_NAME != "publics")) {
			//转到登录页面
			$_SESSION["refurl"] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
			$this->redirect("/Publics/login");
        }
        
        $this->result['now_user'] = cache("user_auth_" . session('UserAdmin'));
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

    public function fetch($path = ''){
        View::assign($this->result);
        return View::fetch($path);
    }

}
