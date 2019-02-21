<?php
namespace app\common\upgrade;
use think\Db;

class U10{
	public function up(){
	    $info = Db::name('config')->where('c_key','money_ratio')->find();
	    if (empty($info)) {
	        into_sql("INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 32, '1块钱兑换多少个积分', 'money_ratio', '10', 'number', '', 0, '', '', 0, -5);");
	    }
	}
}