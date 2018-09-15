<?php
namespace app\common\fun;

/**
 * 评论用到的相关函数
 */
class Comment{
    
    /**
     * 评论总数
     * 模板中使用方法 内容页中的使用方法 {:fun('comment@total',$id,'cms')}  列表页中的使用方法 {:fun('comment@total',$rs['id'],'cms')} 
     * 如果是在当前频道调用的话, 后面的cms可以不写 比如  {:fun('comment@total',$id)}  {:fun('comment@total',$rs['id'])} 
     * @param number $aid 内容ID
     * @param string $sys 指定系统比如cms shop
     * @return void|number|number[]|array|unknown[]|unknown
     */
    public function total($aid=0,$sys=''){
        if (is_array($aid)) {   //$aid为$rs的情况
            $sys = $aid['DIR'];
            $aid = $aid['id'];
        }
        if ($sys==''){
            $sys = config('system_dirname');
        }
        $sysid = modules_config($sys)['id'];
        if(empty($sysid)){
            return 0;
        }
        $map = [
                'where' =>['sysid'=>$sysid,
                        'aid'=>$aid,
                ],
                'count'=>'id'
        ];
        $num = query('comment_content',$map);
        return intval($num);
   }
   
   /**
    * 取得某条评论
    * @param number $id
    * @return mixed|number
    */
   public function info($id = 0){
       $map = [
               'where'=>['id'=>$id],
               'type'=>'one',
       ];
       $rsdb = query('comment_content',$map);
       $rsdb && $rsdb['content'] = del_html($rsdb['content']);
       return $rsdb;
   }
   
   
}