<?php
namespace app\member\controller;


use app\common\controller\MemberBase;
use app\cms\model\Content AS ContentModel;

class Vod extends MemberBase
{
    public function post_voice($id=0,$aid=0){
        $info = ContentModel::getInfoByid($id,false);
        if (empty($info)) {
            return $this->err_js('内容不存在');
        }
        if ($info['uid']!=$this->user['uid']){
            return $this->err_js('不是你的内容');
        }
        $qun = fun("qun@getByid",$aid);
        if (!$qun) {
            return $this->err_js('圈子不存在');
        }elseif($qun['uid']!=$this->user['uid']){
            return $this->err_js('你不是圈主');
        }
        $array = json_decode($info['voice_url'],true);
        foreach ($array AS $key=>$rs){
            $rs['url'] = tempdir($rs['url']);
            $array[$key] = $rs;
        }
        
        $data = [
            'id'=>$id,
            'time'=>time(),
            'urls'=>$array,
        ];
        fun('Qun@live',$aid,'vod_voice',$data);
        return $this->ok_js($array);
    }
    
    /**
     * 结束直播
     * @param number $aid
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function stop_voice($aid=0){
        $qun = fun("qun@getByid",$aid);
        if (!$qun) {
            return $this->err_js('圈子不存在');
        }elseif($qun['uid']!=$this->user['uid']){
            return $this->err_js('你不是圈主');
        }
        fun('Qun@live',$aid,'vod_voice','');
        return $this->ok_js();
    }
    
    /**
     * 圈子引用点播转直播 只能是增强版CMS的内容
     * @param string $type voice mv
     * @param number $aid 圈子ID
     * @return mixed|string
     */
    public function index($type='voice',$aid=0){
        if($type=='mv'){
            $mid = 3;
        }else{
            $mid = 4;
        }
        $this->assign('mid',$mid);
        $this->assign('aid',$aid);
        return $this->fetch();
    }
}
