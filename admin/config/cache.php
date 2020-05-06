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

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [


 // 缓存配置为复合类型
 'type'  =>  'complex',
 
 'default'	=>	[
     'type'	=>	'file',
     // 全局缓存有效期（0为永久有效）
     'expire'=> 259200,
     // 缓存前缀
     'prefix'=>  '',
     // 缓存目录
     'path'  =>  '../runtime/cache/',
 ],

 'redis_db0'	=>	[
     'type'	=>	'redis',
     'host'	=>	'222.186.3.230',
     'port' => 8168,
     'password' => 'Tw2012927++',
     'timeout'=> 259200,
     // 全局缓存有效期（0为永久有效）
     'expire'=>  259200,
     // 缓存前缀
     'prefix'=>  '',
     'persistent' => false,
 ],

 'redis_db1'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.15.61',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=>  '',
    'persistent' => false,
],


'redis_db2'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.15.65',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=>  '',
    'persistent' => false,
],

'redis_db3'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.15.170',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=>  '',
    'persistent' => false,
],

'redis_db4'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.15.204',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=>  '',
    'persistent' => false,
],

'redis_db5'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.15.218',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=>  '',
    'persistent' => false,
],


'redis_db6'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.30.91',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=>  '',
    'persistent' => false,
],


'redis_db7'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.31.233',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=>  '',
    'persistent' => false,
],


'redis_db8'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.42.167',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=>  '',
    'persistent' => false,
],


'redis_db9'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.169.46',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=>  '',
    'persistent' => false,
],

//国外服务器redis
'redis_db10'	=>	[
    'type'	=>	'redis',
    'host'	=>	'74.91.27.146',
    'port' => 8168,
    'password' => 'df2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=> '',
    'select' => 8,
    'persistent' => false,
],


'redis_db11'	=>	[
    'type'	=>	'redis',
    'host'	=>	'222.186.3.182',
    'port' => 8168,
    'password' => 'Tw2012927++',
    'timeout'=> 259200,
    // 全局缓存有效期（0为永久有效）
    'expire'=>  259200,
    // 缓存前缀
    'prefix'=> '',
    'select' => 8,
    'persistent' => false,
],

];
