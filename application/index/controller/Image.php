<?php
namespace app\index\controller;

use app\common\controller\IndexBase;


class Image extends IndexBase
{
    public function cutimg($picurl,$opt){
        $this->assign('picurl',$picurl);
        list($w,$h) = explode(':', $opt);
        $Ratio = '';
        if($w&&$h){
            $Ratio = number_format($w/$h,2);
        }
        $this->assign('opt',"{\"aspectRatio\":\"$Ratio\"}");
        return $this->fetch();
	}
	
	public function face(){
		return $this->fetch();
	}

	public function headers($url=''){
		if($url==''){
			return ;
		}
		$img = http_curl($url);
		if($img==''){
			return ;
		}
		if(strstr($img,'<title>302')){	//设置了302跳转
			preg_match('/href="([^"]+)"/',$img,$array);
			$img = http_curl($array[1]);
		}
		header("Content-Type: image/jpeg;text/html; charset=utf-8");
		echo $img;
		exit;
	}
}