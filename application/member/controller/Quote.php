<?php
namespace app\member\controller;


use app\common\controller\MemberBase;


class Quote extends MemberBase
{
    /**
     * 站内引用的主题列表
     * @param string $type
     * @param number $uid 负数圈子ID,正数用户UID
     * @return mixed|string
     */
    public function index($type='cms',$uid=0){
        $this->assign('type',$type);
        $this->assign('uid',$uid);
        return $this->fetch();
    }
    
    /**
     * 设置推荐主题
     * @param number $aid
     * @param number $ext_id
     * @param string $ext_sys
     * @param string $type
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function live($aid=0,$ext_id=0,$ext_sys='',$type='add'){
        
        $qun = fun("qun@getByid",$aid);
        if (!$qun) {
            return $this->err_js('圈子不存在');
        }elseif($qun['uid']!=$this->user['uid']){
            return $this->err_js('你不是圈主');
        }
        if ($type=='delete') {
            fun('Qun@live',$aid,'vod_topic','');
            $key = "vod_topic-{$aid}*";
            cache2($key,null);
            return $this->ok_js();
        }else{
            if (empty($ext_id)||empty($ext_sys)) {
                return $this->err_js('ID或频道不存在');
            }
            if (empty(modules_config($ext_sys))) {
                return $this->err_js('频道不存在');
            }            
            $data = [
                'id'=>$ext_id,
                'sys'=>$ext_sys,
                'time'=>time(),
            ];
            fun('Qun@live',$aid,'vod_topic',$data);
            return $this->ok_js();
        }
    }
    
    /**
     * 获取圈子里的推荐主题
     * @param number $aid 圈子ID
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function get($aid=0){
        $info = fun('Qun@live',$aid,'vod_topic');
        if (empty($info)) {
            return $this->err_js('没数据');
        }
        $ext_id = $info['id'];
        $ext_sys = $info['sys'];
        $key = "vod_topic-{$aid}-{$ext_sys}-{$ext_id}-".$this->user['uid'];
        if ( cache2($key) ) {            
            return $this->err_js('已阅');
        }
        cache2($key,time(),3600*24*30);
        return $this->ok_js($info);
    }
}
