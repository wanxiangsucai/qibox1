<?php
namespace app\common\fun;

//内容过滤
class Filter{
    
    static $bad_word = '<iframe |<script |<\/script>|<\/iframe>|<\?php ';   //过滤的字符
    
    /**
     * 过滤字符
     * @param string $content
     * @return void|mixed
     */
    public static function str($content=''){        
        if ($content=='') {
            return ;
        }        
        $content = preg_replace_callback('/('.self::$bad_word.')/is',array(self,replace),$content);
        return $content;
    }
    
    /**
     * 全局过滤
     */
    public static function all($data=[]){
        $array = [];
        foreach ($data AS $key=>$content){
            if (preg_match('/('.self::$bad_word.')/is',$content)) {
                $array[$key] = self::str($content);
            }
        }
        return $array;
    }
    
    private function replace($array=[]){
        return str_replace(['<','>'], ['&lt;','&gt;'], $array[0]);
    }
    
}