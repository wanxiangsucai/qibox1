<?php
namespace app\member\controller\wxapp;


use app\common\controller\MemberBase;
use plugins\weixin\util\Msg;

//小程序 
class User extends MemberBase
{
    /**
     * 获取自己的登录信息
     * @return void|unknown|\think\response\Json
     */
    public function index(){
        $rs = $this->user;
        unset($rs['qq_api'],$rs['weixin_api'],$rs['wxapp_api'],$rs['unionid'],$rs['wxopen_api']);
        return $this->ok_js($rs);
    }
    
    public function edit_map($point='113.30764968,23.1200491',$type=0){
        if ($this->request->isAjax() && ($type==1 || empty($this->user['map_x']))) {
            list($x,$y) = explode(',',$point);
            edit_user([
                    'uid'=>$this->user['uid'],
                    'map_x'=>$x,
                    'map_y'=>$y,
            ]);
            return $this->ok_js();
        }else{
            return $this->err_js();
        }
    }
    
    /**
     * 修改用户的订阅消息状态
     * @param string $type mp wxapp
     * @param number $sub 是否订阅
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function edit_subscribe($type='',$sub=1){
//         if ( ($this->request->isAjax() //公众号中提交
//             ||mymd5(input('uids'),'DE'))    //小程序中提交
//             && $type) {
            $array = [
                'uid'=>$this->user['uid'],
            ];
            $result = false;
            $content = '感谢订阅,<a href="'.get_url(murl('member/index/index')).'">系统后续仅发送与你相关的消息</a>';
            if ($type=='mp') {                
                if ($sub) {
                    $result = Msg::mp_subscribe($this->user['weixin_api'], $content,[],wx_getAccessToken());
                }
                $array['subscribe_mp'] = $result===true ? 1 : 0;
            }else{                
                if ($sub) {
                    if (get_wxappAppid()) {
                        $openid = \app\qun\model\Weixin::get_openid_by_uid($this->user['uid']);
                        $result = Msg::wxapp_subscribe($openid, $content,[],wx_getAccessToken(false,true,get_wxappAppid()));
                        \app\qun\model\Weixin::where('uid',$this->user['uid'])->where('wxapp_api',$openid)->update([
                            'if_dy'=>$result===true ? 1 : 0,
                        ]);
                    }else{
                        $result = Msg::wxapp_subscribe($this->user['wxapp_api'], $content,[],wx_getAccessToken(false,true));
                    }
                }
                if (get_wxappAppid()) {
                    if ($result===true) {
                        $array['subscribe_qun_wxapp'] = 1;
                    }else{
                        $num = \app\qun\model\Weixin::where('uid',$this->user['uid'])->where('if_dy',1)->count('id');
                        $array['subscribe_qun_wxapp'] = $num ? 1 : 0;
                    }
                }else{
                    $array['subscribe_wxapp'] = $result===true ? 1 : 0;
                }                
            }
            edit_user($array);
            
            $msg_array = [
                'type'=>'subscribe',
                'data'=>[
                    'type'=>$type,
                    'sub'=>$sub,
                ],
            ];
            fun("Gatewayclient@send_to_group",0,$this->user['uid'],$msg_array);         
            return $this->ok_js([],'设置成功');
//         }else{
//             return $this->err_js('出错了');
//         }
    }
}
