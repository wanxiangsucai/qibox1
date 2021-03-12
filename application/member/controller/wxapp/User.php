<?php
namespace app\member\controller\wxapp;


use app\common\controller\MemberBase;
use plugins\weixin\util\Msg;

//小程序 
class User extends MemberBase
{
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
                    $result = Msg::wxapp_subscribe($this->user['wxapp_api'], $content,[],wx_getAccessToken(false,true));
                }
                $array['subscribe_wxapp'] = $result===true ? 1 : 0;
            }
            edit_user($array);
            return $this->ok_js([],'设置成功');
//         }else{
//             return $this->err_js('出错了');
//         }
    }
}
