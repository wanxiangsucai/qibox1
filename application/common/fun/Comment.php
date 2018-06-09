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
        if ($sys==''){
            $sys = config('system_dirname');
        }
        $sysid = modules_config($sys)['id'];
        if(empty($sysid)){
            return -1;
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
   
   
}