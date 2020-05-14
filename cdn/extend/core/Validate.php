<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace core;

use think\exception\ClassNotFoundException;
use think\validate\ValidateRule;

class Validate extends \think\Validate
{
    public function __construct()
    {
        parent::__construct();
        // 添加字段值是否存在验证
        self::setTypeMsg('exist', ':attribute not exists');
        self::extend('exist', function($value, $rule, array $data = [], string $field = ''){ return !$this->unique($value, $rule, $data, $field);});
    }
}
