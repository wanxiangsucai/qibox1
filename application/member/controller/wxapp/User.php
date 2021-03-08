<?php
namespace app\member\controller\wxapp;


use app\common\controller\MemberBase;

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
            if ($type=='mp') {
                $array['subscribe_mp'] = $sub;
            }else{
                $array['subscribe_wxapp'] = $sub;
            }
            edit_user($array);
            return $this->ok_js([],'设置成功');
//         }else{
//             return $this->err_js('出错了');
//         }
    }
}
