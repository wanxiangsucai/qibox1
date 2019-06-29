<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

\think\Route::get('captcha/[:id]', "\\think\\captcha\\CaptchaController@index");

\think\Validate::extend('captcha', function ($value, $id = "") {
	$array = (array)\think\Config::get('captcha');
	$array['reset'] = false;	//AJAX验证的时候,不要重置
    return captcha_check($value, $id, $array);
});

\think\Validate::setTypeMsg('captcha', '验证码错误!');


/**
 * @param string $id
 * @param array  $config
 * @return \think\Response
 */
function captcha($id = "", $config = [])
{
    $captcha = new \think\captcha\Captcha($config);
    return $captcha->entry($id);
}


/**
 * @param $id
 * @return string
 */
function captcha_src($id = "")
{
//    return \think\Url::build('/captcha' . ($id ? "/{$id}" : ''));
    $base_file = str_replace(config('admin.filename')?:ADMIN_FILE, 'index.php', request()->baseFile());
    return $base_file.'/captcha'.($id ? "/{$id}" : '').'.html';
}


/**
 * @param $id
 * @return mixed
 */
function captcha_img($id = "")
{
    return '<img src="' . captcha_src($id) . '" alt="captcha" />';
}


/**
 * @param        $value
 * @param string $id
 * @param array  $config
 * @return bool
 */
function captcha_check($value, $id = "", $config = [])
{
    $captcha = new \think\captcha\Captcha($config);
    return $captcha->check($value, $id);
}

