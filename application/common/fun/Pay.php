<?php
namespace app\common\fun;

use think\Db;

require_once(ROOT_PATH.'plugins/weixin/api/lib/WxPay.Api.php');

/**
 * 支付相关
 *
 */
class Pay{
    
    /**
     * 退款
     * @param number $uid
     * @param number $money
     * @param string $about
     * @return boolean
     */
    public function refund($uid=0,$money=0,$about=''){
        $user = get_user($uid);
        if ($user['rmb']<$money) {
            return '余额不足，无法退款';
        }
        if (!table_field('rmb_infull','refund_money')) {
            into_sql("ALTER TABLE `qb_rmb_infull` ADD `refund_money` DECIMAL( 8, 2 ) NOT NULL COMMENT '退款金额',ADD `transaction_id` VARCHAR( 32 ) NOT NULL COMMENT '微信或支付宝的交易单号，退款时是原来的订单号';");
            into_sql("ALTER TABLE `qb_rmb_consume` ADD `refund` TINYINT NOT NULL COMMENT '1支持退款，2已退款';");
        }
        $map = [
            'uid'=>$uid,
            'money'=>['>=',$money],
            'posttime'=>['>',time()-3600*24*30],
            'refund_money'=>0,
            'ifpay'=>1,
            'banktype'=>['<>','支付宝'],
        ];
        $log = Db::name('rmb_infull')->where($map)->order('money asc,id desc')->find();
        if (!$log) {
            return '没有微信支付记录，无法退款';
        }
        $wxapp_info = [];
        $cache_cfg = [];
        if ($log['wxapp_appid']) {
            $wxapp_info = get_wxappinfo($log['wxapp_appid']);
            if(!$wxapp_info['payid']||!$wxapp_info['paykey']||!$wxapp_info['weixin_apiclient_cert']||!$wxapp_info['weixin_apiclient_key']){
                return '商家小程序支付，商家资料不全，目前无法直接退款';
            }
            $cache_cfg = [
                'wxapp_appid'=>config('webdb.wxapp_appid'),
                'wxapp_payid'=>config('webdb.wxapp_payid'),
                
                'weixin_payid'=>config('webdb.weixin_payid'),
                'weixin_paykey'=>config('webdb.weixin_paykey'),
                
                'weixin_apiclient_cert'=>config('webdb.weixin_apiclient_cert'),
                'weixin_apiclient_key'=>config('webdb.weixin_apiclient_key'),
            ];
            config('webdb.wxapp_appid',$wxapp_info['appid']);
            config('webdb.wxapp_payid',$wxapp_info['payid']);
            
            config('webdb.weixin_payid',$wxapp_info['payid']);
            config('webdb.weixin_paykey',$wxapp_info['paykey']);
            
            config('webdb.weixin_apiclient_cert',$wxapp_info['weixin_apiclient_cert']);
            config('webdb.weixin_apiclient_key',$wxapp_info['weixin_apiclient_key']);
        }
        
        try{
            $numcode = 'refund'.date("YmdHis").rands(5);
            $input = new \WxPayRefund();
            if($log['transaction_id']){
                $input->SetTransaction_id($log['transaction_id']);
            }else{
                //小程序订单中 \application\index\controller\wxapp\Pay.php 订单号多了三个000
                $input->SetOut_trade_no( ($log['banktype']=='wxapp'?'000':'').$log['numcode'] );
            }
            if ($about!='') {
                $input->SetDesc($about);
            }
            $input->SetTotal_fee($log['money']*100);
            $input->SetRefund_fee($money*100);
            
            $input->SetOut_refund_no($numcode);
            $input->SetOp_user_id( $log['banktype']=='wxapp'?config('webdb.wxapp_appid'):config('webdb.weixin_appid') ); //公众账号ID
            $input->SetAppid( $log['banktype']=='wxapp'?config('webdb.wxapp_appid'):config('webdb.weixin_appid') ); //公众账号ID
            $input->SetMch_id( $log['banktype']=='wxapp'?config('webdb.wxapp_payid'):config('webdb.weixin_payid') );//商户号
            
            $array = \WxPayApi::refund($input);
            if ($cache_cfg) {
                foreach($cache_cfg AS $key=>$value){
                    config('webdb.'.$key,$value);
                }
            }
            if($array['result_code']!='SUCCESS'){
                return $array['err_code_des']?:$array['return_msg'];
            }
            $result = Db::name('rmb_infull')->insert([
                'numcode'=>$numcode,
                'money'=>-$money,
                'ifpay'=>1,
                'posttime'=>time(),
                'uid'=>$uid,
                'banktype'=>$log['banktype'],
                'paytime'=>time(),
                'wxapp_appid'=>'',
                'transaction_id'=>$log['numcode'],
            ]);
            if($result){
                Db::name('rmb_infull')->where('id',$log['id'])->update([
                    'refund_money'=>$money,
                ]);
                add_rmb($uid,-$money,0,$about?:'退款提现',2);
            }
        }catch(\Exception $e){
            return $e->getMessage();
        }
        return true;
    }
     
}