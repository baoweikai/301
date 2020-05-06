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
use think\facade\Route;

Route::get('/', 'index/index');
Route::get(':controller/:id', ':controller/view')->pattern(['controller' => '[\w|\-]+', 'id' => '\d+'])->allowCrossDomain();
Route::rule(':controller/:action/:id', ':controller/:action')->pattern(['controller' => '[\w|\-]+', 'action' => '[\w|\-]+', 'id' => '\d+'])->allowCrossDomain();
Route::rule(':controller/:action', ':controller/:action')->pattern(['controller' => '[\w|\-]+', 'action' => '[\w|\-]+'])->allowCrossDomain();
