<?php
namespace app\common\model;

use think\Model;
use app\common\model\User AS UserModel;
use app\common\util\Shop AS ShopFun;

class Order extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table;// = '__FORM_MODULE__';
    
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = true;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';
    
    protected static $base_table;
    protected static $model_key;
    protected static $table_pre;
    protected static $content_model; //内容模型
    
    //为了调用initialize初始化,生成数据表前缀$model_key
    protected static function scopeInitKey(){}
    protected function initialize()
    {
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        self::$model_key = $array[0][1];
        self::$base_table = $array[0][1].'_content';
        self::$table_pre = config('database.prefix');
        //字段表，带数据表前缀如qb_form_field
        $this->table = self::$table_pre.self::$model_key.'_order';
        self::$content_model = get_model_class(self::$model_key,'content');
    }
    
    /**
     * 某条订单里的商品信息
     * @param unknown $shops
     * @return void[]|array[]|\think\db\false[]|\app\common\model\PDOStatement[]|string[]|\think\Model[]
     */
    protected static function getshop($shops){
        $listdb = [];
        $detail = explode(',', $shops);
        foreach ($detail AS $value){
            if (empty($value)) {
                continue;
            }
            list($shpid,$num,$type1,$type2,$type3) = explode('-', $value);
            $shopdb = self::$content_model->getInfoByid($shpid,true);
            unset($shopdb['content'],$shopdb['full_content']);
            //对价格与商品属性进行处理
            ShopFun::car_get_price_type($shopdb,[
                    'num'=>$num,
                    'type1'=>$type1,
                    'type2'=>$type2,
                    'type3'=>$type3,
            ]);
            $listdb[] = $shopdb;
        }
        return $listdb;
    }
    
    /**
     * 只获取一条订单信息,一般用在查看详情使用
     * @param unknown $id
     * @return void[]|array[]|\think\db\false[]|\app\common\model\PDOStatement[]|string[]|\think\Model[]
     */
    public function getInfo($id){
        $info = getArray( $this->find($id) );
        if ($info){
            $info['shop_db'] = $this->getshop($info['shop']);
            return $info;
        }
    }
    
    //订单列表,带分页
    public  function getList($map=[],$rows=20){
        $data_list = self::where($map)->order('id','desc')->paginate($rows);
        $data_list->each(function($rs,$key){
            $rs['shop_db'] = [];
            if($rs['shop']!=''){
                $rs['shop_db'] = self::getshop($rs['shop']);
            }
            return $rs;
        });
        return $data_list;
    }
    

    /**
     * 后台支付后,进行确认付款审核处理
     * @param string $ids 多个订单的话,每个订单ID用逗号隔开,不同的商家会生成不同的订单
     * @return boolean
     */
    public static function pay($ids=''){
        $array = explode(',',$ids);
        $check = 0;
        foreach ($array AS $id){
            $info = self::get($id);
            if (empty($info)) {
                continue;
            }
            if ($info['pay_status']) {  //已支付
                $check++;
                continue;   //不要再执行下面的
            }
            $user = UserModel::get_info($info['uid']);
            if($user['rmb']<$info['pay_money']){    //钱不够扣,终止以下所有操作
                return false;
            }
            //执行扣款
            add_rmb($info['uid'],-abs($info['pay_money']),0,'购物消费');
            
            self::update([
                    'id'=>$id,
                    'pay_status'=>1,
                    'pay_time'=>time(),
            ]);
            $check++;
        }
        
        if ($check) {
            return true;
        }        
    }
	
}