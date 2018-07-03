<?php
namespace app\common\fun;

/**
 * 内容页用到的相关函数
 */
class Content{
    
    /**
     * 获取内容中的附件,比如常用的获取内容中的图片
     * @param string $content
     * @param string $type
     * @return array|unknown[]
     */
    public static function get_file($content='',$type='jpg,zip'){
        preg_match_all("/<img([^>]+)src=\"([^\"]+)\"([^>]+)>/is",$content,$array);
        if (empty($array[2])) {
            return [];
        }
        $data = [];
        $type===true || $type = str_replace(',', '|', $type);
        foreach ($array[2] AS $url){
            if ( preg_match("/^\/public\/uploads\//i", $url) ) {
                if ($type===true || preg_match("/($type)$/i", $url)) {
                    $data[] = $url;
                }
            }            
        }
        return $data;
    }
    
    /**
     * 获取内容中的图片
     * @param string $content
     * @param string $type
     * @return array[][]|\app\common\fun\unknown[][][]
     */
    public static function get_images($content='',$type='jpg,jpeg,png'){
        $array = self::get_file($content,$type);
        $data = [];
        foreach ($array AS $pic){
            $data[] = [
                    'picurl'=>$pic
            ];
        }
        return $data;
    }

    
    /**
     * 获取信息主题内容
     * @param number $aid 信息内容ID,
     * @param string $sysid 频道id,也可以是频道目录名
     * @param string $format 是否对内容进行格式化转义
     * @return void|unknown
     */
    public static function info($aid = 0 , $sysid = '' , $format = true){
        $mods = modules_config($sysid);
        $dirname = $mods['keywords'];
        if (empty($dirname)) {
            return ;
        }
        $class = "app\\$dirname\\model\\Content";
        if(class_exists($class) && method_exists($class,'getInfoByid')){
            $obj = new $class;
            $info = $obj->getInfoByid($aid,$format);
            if(empty($info)){
                return ;
            }
            if ($format===true&&empty($info['picurls'])) {
                $info['picurls'] = self::get_images($info['content']);
            }
            $info['module_dir'] = $mods['keywords'];    //频道目录,生成频道网址要用到
            $info['module_name'] = $mods['name'];     //频道名称
            return $info;
        }
    }
    
    /**
     * 取得论坛某条回复
     * @param number $rid
     * @param string $sysid
     * @return void|mixed|number
     */
    public function reply($rid = 0,$sysid = ''){
        $mods = modules_config($sysid);
        $dirname = $mods['keywords'];
        if (empty($dirname)) {
            return ;
        }
        $map = [
                'where'=>['id'=>$rid],
                'type'=>'one',
        ];
        $rsdb = query($dirname.'_reply',$map);
        $rsdb && $rsdb['content'] = del_html($rsdb['content']);
        return $rsdb;
    }
    
    /**
     * 下一页与下一页的用法 {:fun('content@prev',$info,20)}
     * @param array $info 当前页的内容主题内容,里边必须要包含有id fid mid
     * @param string $title 可以设置下一页也可以设置数字,截取标题数
     * @return string
     */
    public function prev($info=[],$title='下一页'){        
        return $this->prev_next($info,$title,'<');
    }
    
    /**
     * 下一页与下一页的用法 {:fun('content@next',$info,20)}
     * @param array $info 当前页的内容主题内容,里边必须要包含有id fid mid
     * @param string $title 可以设置下一页也可以设置数字,截取标题数
     * @return string
     */
    public function next($info=[],$title='上一页'){
        return $this->prev_next($info,$title,'>');
    }
   
    /**
     * 上一页,下一页
     * @param array $info 当前页的内容主题内容,里边必须要包含有id fid mid
     * @param number $title 可以设置下一页也可以设置数字,截取标题数
     * @param string $type 大于或小于当前页的ID
     * @return string
     */
   private function prev_next($info=[],$title=10,$type='<'){
       $sys = config('system_dirname');
       $map = [
               'where' =>[
                       'fid'=>$info['fid'],
                       'id'=>[$type,$info['id']],
               ],
               'field'=>'id,title',
               'type'=>'one',
       ];
       $rsdb = query($sys.'_content'.$info['mid'],$map);
       if (empty($rsdb)) {
           return '没有了';
       }
       if (is_numeric($title) && $title>2) {
           $title = get_word($rsdb['title'], $title);
       }
       $url = urls('content/show',['id'=>$rsdb['id']]);
       return "<a href='{$url}' title='{$rsdb['title']}'>{$title}</a>";
   }
   
   public function ext_num($info=[],$time=3600){
       return query(config('system_dirname').'_content'.$info['mid'],
               [
                       'where'=>[
                               //'ext_sys'=>$info['ext_sys'],
                               'ext_id'=>$info['ext_id'],
                       ],
                       'count'=>'id',
               ],$time);
   }
   
   
}