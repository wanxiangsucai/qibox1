<?php
namespace app\common\fun;

//内容过滤
class Filter{

    /**
     * 过滤字符
     * @param string $content
     * @return void|mixed
     */
    public static function str($content=''){        
        if ($content=='') {
            return ;
        }
        $content = preg_replace_callback('/(<iframe |<script |<\/script>|<\/iframe>)/i',array($this,replace),$content);
        return $content;
    }
    
    private function replace($array=[]){
        return str_replace('<', '&lt;', $array[0]);
    }
    
}