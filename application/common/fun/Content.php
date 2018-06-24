<?php
namespace app\common\fun;

/**
 * 内容页用到的相关函数
 */
class Content{
    
    /**
     * 获取信息主题内容
     * @param number $aid 信息内容ID,
     * @param string $sysid 频道id,也可以是频道目录名
     * @param string $format 是否对内容进行格式化转义
     * @return void|unknown
     */
    public function info($aid = 0 , $sysid = '' , $format = true){
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
   
   
}