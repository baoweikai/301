<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => env('cache.driver', 'redis0'),

    // 缓存连接方式配置
    'stores'  => [
        'redis0'	=>	[
            'type'	=>	'redis',
            'host'	=>	'redis',
            'port' => 6379,
            'password' => 'huaren54321',
            'timeout'=> 259200,
            // 全局缓存有效期（0为永久有效）
            'expire'=>  259200,
            // 缓存前缀
            'prefix'=>  '',
            'persistent' => false,
        ],
        'redis1'	=>	[
           'type'	=>	'redis',
           'host'	=>	'redis',
           'port' => 6379,
           'password' => 'huaren54321',
           'timeout'=> 259200,
           // 全局缓存有效期（0为永久有效）
           'expire'=>  259200,
           // 缓存前缀
           'prefix'=>  '',
           'persistent' => false,
       ],     
    ],
];
