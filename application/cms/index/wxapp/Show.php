<?php
namespace app\cms\index\wxapp;

use app\common\controller\index\wxapp\Show AS _Show;
use app\cms\model\Content AS ContentModel;
use app\cms\model\Buyer AS BuyerModel;

//小程序 显示内容
class Show extends _Show
{
    /**
     * 调取显示内容主题
     * @param number $id 内容ID
     * @return \think\response\Json
     */
    public function index($id=0){
        return parent::index($id);
    }
    
    protected function format_data($info=[]){
        if(!in_array($info['mid'], [2,3,4])){
            $info['morePage'] = [];
            $array = get_content_pages($info['id']);
            if($array){
                $info['morePage'][] = [
                    'id'=>0,
                    'money'=>$info['price']>0?$info['price']:'',
                    'title'=>$info['title']
                ];
                foreach ($array AS $rs){
                    if($rs['id']){
                        $info['morePage'][] = [
                            'id'=>$rs['id'],
                            'money'=>$rs['money'],
                            'title'=>$rs['title']
                        ];
                    }                    
                }
            }            
        }
        return parent::format_data($info);
    }
    
    public function buy($id=0){
        $info = ContentModel::getInfoByid($id);
        $data = [
            'aid'=>$id,
            'uid'=>$this->user['uid'],            
        ];
        if(empty($info)){
            return $this->err_js('内容不存在!');
        }elseif (empty($this->user)) {
            $url = weixin_login( urls('content/show',['id'=>$id]) ,false);
            return $this->err_js('你还没登录,请先登录!',['url'=>$url],3);
        }elseif ($info['price']<0.01) {
            return $this->err_js('非收费内容!');
        }elseif (BuyerModel::get($data)) {
            return $this->err_js('已经购买过了,不用重复购买!');
        }elseif($this->user['rmb']<$info['price']){
            $url = post_olpay([
                'money'=>$info['price'] - $this->user['rmb'],
                'return_url'=>urls('content/show',['id'=>$id,'type'=>'pay']),
                'banktype'=> '',//in_weixin() ? 'weixin' : 'alipay' , //在微信端,就用微信支付,否则就用支付宝支付
                'numcode'=>'s'.date('ymdHis').rands(3),
                'callback_class'=>mymd5('app\\'.config('system_dirname').'\\model\\Buyer@pay@'.$id.'|'.$this->user['uid']),   //执行回调方法
            ]);
            $array = [
                'payurl'=>$url,
            ];
            return $this->err_js('你的可用余额不足 '.$info['price'].' 元,请先充值!',$array,2);
        }
        
        $result = BuyerModel::pay($id,$this->user['uid']);
        if ($result===true){
            return $this->ok_js();
        }else{
            return $this->err_js($result);
        }
    }
    
}













