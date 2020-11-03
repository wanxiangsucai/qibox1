<?php
namespace app\common\upgrade;
use think\Db;

class U29{
	public static function up(){
	    $listdb = Db::name('market')->where('type','')->column(true);
	    foreach ($listdb AS $rs){
	        if(is_dir(TEMPLATE_PATH.'qun_style/'.$rs['keywords'])){
	            Db::name('market')->where('id',$rs['id'])->update([
	                'type'=>'qun_style',
	            ]);
	        }elseif(is_dir(TEMPLATE_PATH.'model_style/'.$rs['keywords'])){
	            Db::name('market')->where('id',$rs['id'])->update([
	                'type'=>'model_style',
	            ]);
	        }elseif(is_dir(TEMPLATE_PATH.'haibao_style/'.$rs['keywords'])){
	            Db::name('market')->where('id',$rs['id'])->update([
	                'type'=>'haibao_style',
	            ]);
	        }elseif(is_dir(TEMPLATE_PATH.'index_style/'.$rs['keywords'])){
	            Db::name('market')->where('id',$rs['id'])->update([
	                'type'=>'index_style',
	            ]);
	        }elseif(is_dir(TEMPLATE_PATH.'member_style/'.$rs['keywords'])){
	            Db::name('market')->where('id',$rs['id'])->update([
	                'type'=>'member_style',
	            ]);
	        }elseif(is_dir(TEMPLATE_PATH.'admin_style/'.$rs['keywords'])){
	            Db::name('market')->where('id',$rs['id'])->update([
	                'type'=>'admin_style',
	            ]);
	        }
	    }
	}
}