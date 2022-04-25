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
        strstr($content,'<pre ') && $content = preg_replace_callback('/<pre ([^>]+)>(.*?)<\/pre>/is',array(self,'replace_pre'),$content);     //必须排第一位,这是代码段
        
        strstr($content,'<script ') && $content = preg_replace_callback('/<script ([^>]+)>(.*?)<\/script>/is',array(self,'replace'),$content);
        strstr($content,'<iframe ') && $content = preg_replace_callback('/<iframe ([^>]+)>(.*?)<\/iframe>/is',array(self,'replace'),$content);
        strstr($content,'<?php') && $content = preg_replace_callback('/<\?php ([^>]+)>(.*?)\?>/is',array(self,'replace'),$content);
        
        $content = preg_replace_callback('/('.self::$bad_word.')/is',array(self,'replace'),$content);     //过滤漏网之鱼
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
        return str_replace(['<','>','"',"'"], ['&lt;','&gt;','&quot;','&#39;'], $array[0]);
    }

    private function replace_pre($array=[]){
        return str_replace($array[2],str_replace(['<','>','"',"'"], ['&lt;','&gt;','&quot;','&#39;'],$array[2]),$array[0]);
    }
    
    /**
     * 安全检查
     */
    public static function check_safe($data=''){
        $array = is_array($data) ? $data : input();
        foreach($array AS $key=>$value){
            if (is_array($value)) {
                self::check_safe($value);
            }elseif (preg_match('/^([-\w]*)$/', $value)) {
                continue;
            }
            $danger_word = config('webdb.php_danger_word') ? trim(config('webdb.php_danger_word'),'| ') : 'eval|file_put_contents';
            if (preg_match("/([ ;\r\t\n]+)(".$danger_word.")([ \r\t\n]*)\(/is", $value,$ar)) {
                self::err('内容中有非法字符'.$ar[2]);
            }elseif (preg_match("/<\?php([ \r\t\n]+)/is", $value)) {
                self::err('内容中有非法字符?php');
            }
        }
    }
    
    private static function err($msg=''){
        header('content-type:application/json');
        $msg = [
            'code'=>1,
            'msg'=>$msg,
        ];
        die(json_encode($msg,JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * 屏蔽恶意灌水
     */
    public static function attack_visit(){
        if(defined('IN_X1_POST') || !request()->isPost() || (empty($_POST['content'])&&empty($_POST['title']))){
            return ;
        }
        define('IN_X1_POST', true);
        $ip = str_replace('.', '', get_ip());
        $num = cache('IN_X1_POST'.$ip)?:0;
        $num++;        
        cache('IN_X1_POST'.$ip,$num,60);
        if($num>3){
            self::err('请不要频繁提交数据！');
        }
    }
    
    
}