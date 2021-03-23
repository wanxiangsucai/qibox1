<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use app\common\fun\Wxapp AS WxappFun;
use app\common\model\Shorturl AS ShorturlModel;

class Wxapp extends IndexBase
{
    /**
     * 接收小程序识别出来的二维码代码定向到指定网址
     * @param string $scan_str 小程序二维码中包含的特定代码
     * @param string $token 用户登录信息
     */
    public function scan($scan_str='fdsa',$token=''){
        $url = ShorturlModel::getUrl($scan_str);
        header("location:$url");
        exit;
    }
    
    /**
     * 显示腾讯的图片，解决防盗链的问题
     * @param string $url 腾讯的图片地址
     */
    public function wximg($url=''){
        header('Content-Type: image/jpeg');
        echo sockOpenUrl($url);
        exit;
    }
    
    /**
     * 生成小程序二维码
     * @param string $url
     */
    public function img($url=''){
        if ($url=='') {
            $this->error('地址不能为空');
        }
        $imgurl = WxappFun::wxapp_codeimg($url,$this->user['uid']);
        header("location:$imgurl");
        exit;
    }
    
    /**
     * 微信订阅消息
     * @param string $type 指定订阅模板
     * @param string $url 指定订阅消息成功后的,返回网址,留空的话,就返回来源页.不存在来源页的话,就返回主页
     * @return mixed|string
     */
    public function subscribe($type='',$url=''){
        if (!$this->user && in_wap()) {
            $this->error('请先登录');
        }
//         if (!$type && !$this->webdb['mp_subscribe_template_id']) {
//             $this->error('系统还没有设置订阅模板');
//         }
        $this->assign('template',$type);
        if (!$url) {
            $url = $this->fromurl;
            if(strstr($url,'index/login/index')||strstr($url,'index/reg/index')){
                $url = $this->request->domain();
            }
        }elseif(!preg_match("/^http/i", $url)){
            $url = $this->request->domain().$url;
        }
        $this->assign('fromurl',$url?:$this->request->domain());
        return $this->fetch();
    }
	
	/**
     * 进入小程序直播
	 * @param string $rtmp 推流地址 设置为test就是使用默认的测试推流网址
     * @param string $url 返回网址,留空的话,就返回来源页.不存在来源页的话,就返回主页
     * @param string $roomid 直播间ID
     * @return mixed|string
     */
    public function push($rtmp='',$url='',$roomid=0){
        if (!$this->user) {
            $this->error('请先登录');
        }elseif(!$roomid && !$rtmp){
			 $this->error('推流地址不存在！');
		}
		
		$fromurl = urlencode($url?:$this->request->domain());
		
		if ($roomid&&!$rtmp) {
		    $url = 'plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin?room_id='.$roomid.'&custom_params=';	
		}else{
		    $url = '/pages/push/index?url='.$fromurl.'&rtmp='.urlencode($rtmp).($roomid?'&roomid='.$roomid:'');	
		}			
		
		$this->assign('url',$url);
		$this->assign('codeimg', fun('wxapp@wxapp_codeimg',get_url('location'),$this->user['uid']) );
		
		return $this->fetch();
    }
    
    /**
     * 小程序调用订阅消息模板
     * @param string $type
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     
    public function wxapp_get_subscribe_tpl($type=''){
        if (!$type && !$this->webdb['wxapp_subscribe_template_id']) {
            return $this->err_js('系统还没有设置订阅模板');
        }
        return $this->ok_js([
            'template'=>$type?:$this->webdb['wxapp_subscribe_template_id']
        ]);
    }*/
}

?>