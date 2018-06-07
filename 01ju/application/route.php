<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
// 注册路由到index模块的News控制器的read操作
Route::rule('news/:id','Index/index/news_deta');
Route::rule('case_app/:id','Index/index/case_data');
return [
	'index' => 'Index/index/index',
	'about' => 'Index/index/about',
	'app' => 'Index/index/app',
	'shop' => 'Index/index/shop',
	'case_app' => 'Index/index/case_app',
	'news' => 'Index/index/news',
	'news_deta' => 'Index/index/news_deta',
	'case_data' => 'Index/index/case_data',
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
