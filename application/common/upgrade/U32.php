<?php
namespace app\common\upgrade;


class U32{
	public static function up(){
	   $m = modules_config();
	   $string = file_get_contents(APP_PATH.'cms/index/wxapp/Post.php');
	   foreach ($m AS $rs){
	       $path = APP_PATH.$rs['keywords'].'/index/wxapp/Post.php';
	       if(!is_file($path)||strstr(file_get_contents($path),'\\cms\\')){
	           $data = str_replace("\\cms\\", "\\".$rs['keywords']."\\", $string);
	           file_put_contents($path, $data);
	       }
	   }
	}
}