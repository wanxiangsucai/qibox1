<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use \tncode\Tncode AS TncodeClass;
use \tncode\Appcode;

class Tncode extends IndexBase
{
    public function img(){
        $tn  = new TncodeClass();
        $tn->make();
        exit;
    }
    
    public function check($code=''){
        $tn  = new TncodeClass();
        if ($tn->check($code)) {
            cache('tn_code'.get_cookie('user_sid'),1,180);
            return 'ok';//$this->ok_js();
        }else{
            return 'error';//$this->err_js();
        }
    }
    
    /**
     * APP端初始化刷新图片
     * @return void|unknown|\think\response\Json
     */
    public function app_make($sid=''){  //系统会自动生成sid
        $obj = new Appcode();
        $sid = $obj->refresh();
        return $this->ok_js([
            'sid'=>$sid,
            'bg_img'=>get_url(urls('app_img',['type'=>'bg'])).'?sid='.$sid.'&time='.time(),
            'small_img'=>get_url(urls('app_img',['type'=>'small'])).'?sid='.$sid.'&time='.time(),
        ]);
    }
    
    /**
     * APP滑动验证码校验
     * @param number $x
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function app_check($x=0,$sid=''){ //sid可以为空，因为跟 app_make 中的参数HTTP_USER_AGENT请求一样的
        $obj = new Appcode();
        $result = $obj->check($x);
        if($result===true){
            $rands = rands(10);
            cache('tn_code'.$rands,1,180);
            return $this->ok_js([
                'rand_code'=>$rands,
            ]);
        }else if(strstr($result,'错误次数过多')){
            return $this->err_js($result,[],2);
        }else{
            return $this->err_js($result);
        }
    }
    
    /**
     * APP端获取图片
     * @param string $type bg为背景图 small 为缺口图
     * @param string $sid 图片调用跟AJAX请求的参数HTTP_USER_AGENT有可能不一样
     */
    public function app_img($type='bg',$sid=''){
        $obj = new Appcode($sid);
        $result = $obj->img($type=='bg'?true:false);
        if ($result) {
            return $this->error($result);
        }else{
            exit;
        }
    }
}

