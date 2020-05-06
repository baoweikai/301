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


Route::domain('admin', 'admin');
Route::group('', function () {
	Route::any('/:version/:controller/:action', 'api/:version.:controller/:action', ['domain' => 'api'], ['version' => 'v\d{1,3}', 'controller' => '\w+', 'action' => '\w+']);

});
