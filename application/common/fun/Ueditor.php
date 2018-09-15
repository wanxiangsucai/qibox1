<?php
namespace app\common\fun;

//对百度编辑器的一些数据处理
class Ueditor{

    public static function show($content='',$pagetype=''){
        
        $content = Content::bbscode($content);
        
        if ($pagetype!='show') {
            return $content;
        }
        
        if(strstr($content,'<embed type=')){
            $content = preg_replace_callback('/<embed type="application\/x-shockwave-flash" class="edui-faked-video"([^>]+)src="([^"]+)"([^>]+)>/is',array(self,'get_embed_url'),$content);
        }
        
        if(strstr($content,'<video class="edui-upload-video')){
            $content = preg_replace_callback('/<video class="edui-upload-video([^>]+)>([^<]*)<source src="([^"]+)" type="video\/([^"]+)"\/>([^<]*)<\/video>/is',array(self,'get_video_url'),$content);
        }
        
        return $content;
    }
    
    private static function get_video_url($array=[]){
        $url = $array[3];
        preg_match('/width="([\d]+)" height="([\d]+)"/is', $array[1],$ar);
        $width = $ar[1];
        $height = $ar[2];
        return fun('player@play',$url,$width,$height);
    }
    
    private static function get_embed_url($array=[]){
        $url = $array[2];
        preg_match('/width="([\d]+)" height="([\d]+)"/is', $array[3],$ar);
        $width = $ar[1];
        $height = $ar[2];
        return fun('player@play',$url,$width,$height);
    }
     
}