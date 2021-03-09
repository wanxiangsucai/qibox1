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
        if (!$this->user) {
            $this->error('请先登录');
        }
//         if (!$type && !$this->webdb['mp_subscribe_template_id']) {
//             $this->error('系统还没有设置订阅模板');
//         }
        $this->assign('template',$type);
        if (!$url) {
            $url = $this->fromurl;
        }elseif(!preg_match("/^http/i", $url)){
            $url = $this->request->domain().$url;
        }
        $this->assign('fromurl',$url?:$this->request->domain());
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