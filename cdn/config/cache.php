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
        /*
       'redis2'	=>	[
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
       
       'redis3'	=>	[
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
       
       'redis4'	=>	[
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
       
       'redis5'	=>	[
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
       
       
       'redis6'	=>	[
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
       
       
       'redis7'	=>	[
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
       
       
       'redis8'	=>	[
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
       
       
       'redis9'	=>	[
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
       'redis10'	=>	[
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
       ]
       */
    ]
];
