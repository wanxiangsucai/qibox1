<?php
namespace app\common\upgrade;


class U30{
	public static function up(){
	   if (is_file(ROOT_PATH.'template/admin_style/default/qun/codeimg/index.htm')) {
	       delete_dir(ROOT_PATH.'template/admin_style/default/qun/');
	   }
	}
}