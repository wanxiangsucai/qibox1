<?php
namespace app\common\fun;
use think\Db;

/**
 * 考试系统
 */
class Exam{
    
    /**
     * 取出年级,科目,章节的分类
     * @param string $sys 可以为grade kemu step
     * @param string $type
     * @return array
     */
    public function get_sort($sys='',$type='title'){
        static $array = [];
        if (empty($array[$sys])) {
            $array[$sys] = Db::name('exam_'.$sys)->order('list desc , id asc')->column('id,name');
        }
        return $array[$sys];
    }
    
    /**
     * 获取年级,科目,章节的名称
     * @param string $sys 可以为grade kemu step
     * @param string $type
     * @return array
     */
    public function title($sys='',$id=0){
        static $array = [];
        if (empty($array[$id])) {
            $array[$sys] = Db::name('exam_'.$sys)->where('id',$id)->value('name');
        }
        return $array[$sys];
    }
    
    /**
     * 统计试卷的试题数量
     * @param number $id
     */
    public function paper_num($fid=0){
        static $array = [];
        if (empty($array[$fid])) {
            $array[$fid] = Db::name('exam_info')->where('cid',$fid)->count('id');
        }
        return $array[$fid];
    }
    
    /**
     * 已参加考试人数量
     * @param number $fid
     */
    public function test_num($fid=0){
        static $array = [];
        if (empty($array[$fid])) {
            $array[$fid] = Db::name('exam_putin')->where('paperid',$fid)->count('id');
        }
        return $array[$fid];
    }
    
    /**
     * 统计试卷的平均分
     * @param number $fid
     * @return number
     */
    public function average($fid=0){
//         $total_fen = Db::name('exam_putin')->where('paperid',$fid)->sum('fen');
//         return $total_fen/$this->test_num($fid);
        return Db::name('exam_putin')->where('paperid',$fid)->avg('fen');
    }
    
    /**
     * 试卷的第一道题ID
     * @param number $fid
     * @return mixed|PDOStatement|string|boolean|number
     */
    public function paper_first($fid=0){
        return Db::name('exam_info')->where('cid',$fid)->order('list desc,id desc')->limit(1)->value('aid');
    }
    
    
    
    
    
}