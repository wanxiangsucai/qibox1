<?php
namespace app\common\upgrade;


class U31{
	public static function up(){
	   delete_dir(UPLOAD_PATH.'qun_code');
	}
}