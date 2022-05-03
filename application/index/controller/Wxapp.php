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
        if (strstr($url,request()->domain())) {
            header('location:'.$url);
            exit;
        }
        header('Content-Type: image/jpeg');
		echo http_curl($url);
        //sockOpenUrl(str_replace('https://','http://',$url));
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
        if(strstr($imgurl,'invalid page rid')||strstr($imgurl,'"errcode"')){
            die($imgurl);
        }
        header("Content-Type: image/png;text/html; charset=utf-8");
        echo file_get_contents($imgurl);
        //header("location:$imgurl");
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
     * @param string $appid 商家小程序APPid
     * @return mixed|string
     */
    public function push($rtmp='',$url='',$roomid=0,$appid=''){
        if (!$this->user) {
            $this->error('请先登录');
        }elseif(!$roomid && !$rtmp){
			 $this->error('推流地址不存在！');
		}
		
		$fromurl = urlencode($url?:$this->request->domain());
		
			
		if ($roomid&&!$rtmp) {
		    $_url = 'plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin?room_id='.$roomid.'&custom_params=';
		}else{
		    $_url = '/pages/push/index?url='.$fromurl.'&rtmp='.urlencode($rtmp).($roomid?'&roomid='.$roomid:'');
		}
		
		if($appid!=get_wxappAppid()){
		    $_url = '/pages/jump/index?appid='.($appid?:$this->webdb['_wxapp_appid']).'&path='.urlencode($_url).'&backurl='.urlencode($fromurl);
		}
		
		$this->assign('url',$_url);
		$this->assign('codeimg', fun('wxapp@wxapp_codeimg',get_url('location'),$this->user['uid']) );
		
		return $this->fetch();
    }
    
    /**
     * 小程序页面跳转
     * @return void|unknown|\think\response\Json|mixed|string
     */
    public function jump($url){
        $this->assign('url',$url);
        return $this->fetch();
    }
    

    
    /**
     * 小程序集群中的小程序同步登录公众号获取用户的openid unionid 实现帐号统一
     * @param string $openid
     * @param string $backurl
     * @param string $jumptype tab 就使用 wx.miniProgram.switchTab 否则就是 wx.miniProgram.navigateTo
     * @return mixed|string
     */
    public function iframe_login($openid='',$backurl='',$jumptype=''){
        if (!get_wxappAppid()) {
            $this->error('并没在第三方小程序中访问');
        }
        if (!$this->user) {
            weixin_login();
        }else{
            list($time,$openID) = explode("\t", mymd5($openid,'DE'));
            if (!$openID) {
                $this->error('openid不存在！');
            }elseif(time()-$time>60){
                $this->error('统一登录超时了');
            }
            $user = get_user($openID,'wxapp_api');
            if (!$user) {
                \app\qun\model\Weixin::add($this->user['uid'],$openID);   //首次绑定某个商家的小程序
                cache('user_'.$this->user['uid'],null);
            }
            $this->assign('url',$backurl);
            $this->assign('jumptype',$jumptype);
            return $this->fetch();
        }
    }
    
    public function iframe_reg($backurl='',$backnum='-1'){
        if (!$this->user) {
            weixin_login();
        }else{
            $this->assign('url',urlencode($backurl));
            $this->assign('back',$backnum);
            return $this->fetch();
        }
    }
    
    /**
     * 小商店中统一帐号
     * @param string $openid
     * @param string $backurl
     * @return void|unknown|\think\response\Json|mixed|string
     */
    public function minishop_login($openid='',$backurl='' ){
        if (!get_wxappAppid()) {
            $this->error('并没在小商店中访问');
        }
        if (!$this->user) {
            weixin_login();
        }else{
            list($time,$openID) = explode("\t", mymd5($openid,'DE'));
            if (!$openID) {
                $this->error('openid不存在！');
            }elseif(time()-$time>60){
                $this->error('统一登录超时了');
            }
            $user = get_user($openID,'wxapp_api');
            if (!$user) {
                \app\qun\model\Weixin::add($this->user['uid'],$openID);   //首次绑定某个商家的小程序
                cache('user_'.$this->user['uid'],null);
            }
            //$this->assign('url','/pages/jump/index?appid='.get_wxappAppid().'&path='.urlencode($backurl));
            return $this->fetch();
        }
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