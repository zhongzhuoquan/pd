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
    // 驱动方式
    'type'   => 'complex',
    'default' =>
        [
            'type' => 'file',
            'path' => ''
        ],
    'file' =>
        [
            'type' => 'file',
            'path' => ''
        ],
    'redis' =>
        [
            'type' => 'redis',
            'expire' => 0,
            'host' => '127.0.0.1',
            'port' => '6379',
            'password' =>'',
            'prefix' => 'qsdc',
        ],
];