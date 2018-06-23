<?php
namespace app\common\fun;

/**
 * 内容页用到的相关函数
 */
class Content{
    
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