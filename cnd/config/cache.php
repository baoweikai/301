<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => env('cache.driver', 'file'),

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // 更多的缓存连接

        'redis0'	=>	[
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
       
        'redis1'	=>	[
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
    ]
];
