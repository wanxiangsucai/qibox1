<?php
namespace app\common\util;


class Style{
    
    /**
     * 列出网站所有风格
     * @return unknown[]
     */
    public static function listStyle(){
        return static::get_style('index');
    }
    
    protected static function get_style($type='index'){
        $style_db = [];
        $dir = opendir(TEMPLATE_PATH.$type.'_style');
        while (($file=readdir($dir))!==false) {
            $path = TEMPLATE_PATH.$type.'_style/'.$file.'/info.php';
            if($file!='.'&&$file!='..'&&is_file($path)){
                $ar = include $path;
                $style_db[str_replace('.php', '', basename($file))] = $ar['name'];
            }
        }
        return $style_db;
    }
    
    public static function listMemberStyle(){
        return [
                        'default'=>'会员中心X1.0默认风格',
                ];
    }
	
}