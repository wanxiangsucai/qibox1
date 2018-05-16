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

//\think\Route::rule('bbs/show/:id','\\app\\bbs\\index\\Content@show');
use think\Route;

Route::group(['name'=>'cms','ext'=>'html'], [
        'show-<id>$'	=>['cms/content/show',['method'=>'get'],['id' => '\d+']],
        'list-<fid>$'=>['cms/content/index',['method'=>'get'],['fid' => '\d+']],
        'mid-<mid>$'=>['cms/content/index',['method'=>'get'],['mid' => '\d+']],
        'show'	=> 'cms/content/show',
        'list'	=> 'cms/content/index',
        'index'	=> 'cms/index/index',
]);

Route::group(['name'=>'shop','ext'=>'html'], [
        'show-<id>$'	=>['shop/content/show',['method'=>'get'],['id' => '\d+']],
        'list-<fid>$'=>['shop/content/index',['method'=>'get'],['fid' => '\d+']],
        'mid-<mid>$'=>['shop/content/index',['method'=>'get'],['mid' => '\d+']],
        'show'	=> 'shop/content/show',
        'list'	=> 'shop/content/index',
        'index'	=> 'shop/index/index',
]);

Route::group(['name'=>'bbs','ext'=>'html'], [
        'show-<id>$'	=>['bbs/content/show',['method'=>'get'],['id' => '\d+']],
        'list-<fid>$'=>['bbs/content/index',['method'=>'get'],['fid' => '\d+']],
        'mid-<mid>$'=>['bbs/content/index',['method'=>'get'],['mid' => '\d+']],
        'show'	=> 'bbs/content/show',
        'list'	=> 'bbs/content/index',
        'index'	=> 'bbs/index/index',
]);

Route::group(['name'=>'qun','ext'=>'html'], [
        'show-<id>$'	=>['qun/content/show',['method'=>'get'],['id' => '\d+']],
        'list-<fid>$'=>['qun/content/index',['method'=>'get'],['fid' => '\d+']],
        'mid-<mid>$'=>['qun/content/index',['method'=>'get'],['mid' => '\d+']],
        'show'	=> 'qun/content/show',
        'list'	=> 'qun/content/index',
        'index'	=> 'qun/index/index',
]);


Route::group(['name'=>'p','ext'=>'html'], [        
        '<plugin_name>-<plugin_controller>-<plugin_action>-<id>$'	=>['index/plugin/execute',['method'=>'get'],['plugin_name' => '[a-z_0-9]+','plugin_controller' => 'content','plugin_action' => 'show','id' => '\d+',]],
       '<plugin_name>-<plugin_controller>-<plugin_action>-<mid>$'	=>['index/plugin/execute',['method'=>'get'],['plugin_name' => '[a-z_0-9]+','plugin_controller' => '[a-z_0-9]+','plugin_action' => 'index','mid' => '\d+',]],
       '<plugin_name>-<plugin_controller>-<plugin_action>-<fid>$'	=>['index/plugin/execute',['method'=>'get'],['plugin_name' => '[a-z_0-9]+','plugin_controller' => '[a-z_0-9]+','plugin_action' => 'index','fid' => '\d+',]],
        '<plugin_name>-<plugin_controller>-<plugin_action>$'	=>['index/plugin/execute',['method'=>'get'],['plugin_name' => '[a-z_0-9]+','plugin_controller' => '[a-z_0-9]+','plugin_action' => '[a-z_0-9]+',]],
        '<plugin_name>-<plugin_controller>-<plugin_action>'	=>['index/plugin/execute',['method'=>'get|post'],['plugin_name' => '[a-z_0-9]+','plugin_controller' => '[a-z_0-9]+','plugin_action' => '[a-z_0-9]+',]],
]);

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
