<?php
namespace app\common\fun;

class Bbs{
    
    /**
     * 获取贴子内容
     * @param number $id
     * @param number $leng
     * @return unknown|string
     */
    public static function getContents($id=0,$leng=0){
        $contents = query('bbs_contents')->where('id',$id)->value('content');
        if ($leng>0) {
            $contents = get_word(del_html($contents),$leng);
        }
        return $contents;
    }
    
    /**
     * 获取回复
     * @param number $id
     * @param unknown $rows
     */
    public static function getReply($id=0,$rows=5){
        $listdb = query('bbs_reply')->where('aid',$id)->limit($rows)->order('id desc')->select();
        return $listdb;
    }
    
    
}