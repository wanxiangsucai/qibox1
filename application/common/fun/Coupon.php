<?php
namespace app\common\fun;
use think\Db;
//use app\quan\model\Content AS ContentModel;

/**
 * 代金券
 *
 */
class Coupon{
    
    /**
     * 获取指定用户的可用代金券
     * @param number $uid 用户UID
     * @param number $money 当前产品金额
     * @param number $shoper 对应商家的UID
     * @return array
     */
    public static function get_list($uid=0,$money=0,$shoper=0){
        if (empty(modules_config('coupon'))) {
            return ;
        }
        $map = [
            'uid'=>$uid,
            'min_money'=>['<=',$money],
            'receive_status'=>0,
            'pay_status'=>1,
            'expiry_date'=>['>',time()],
        ];
        if ($shoper) {
            $map['shop_uid'] = $shoper;
        }
        $listdb = Db::name('coupon_order')->where($map)->order('quan_money','asc')->column(true);
        return $listdb;
    }
    
    /**
     * 获取具体某条
     * @param number $id
     * @return array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function get_info($id=0){
        $map = [
            'id'=>$id,
        ];
        $info = Db::name('coupon_order')->where($map)->find();
        return $info;
    }
    
    /**
     * 消费掉
     * @param number $id
     */
    public static function take_off($id=0){
        $map = [
            'id'=>$id,
        ];
        Db::name('coupon_order')->where($map)->update([
            'receive_status'=>1,
            'receive_time'=>time(),
        ]);
    }
}