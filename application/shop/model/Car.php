<?php

namespace app\shop\model;
use think\Model;



class Car extends Model
{
    // 设置当前模型对应的完整数据表名称
    public $table; // '__FORM_FIELD__';
    
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = true;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';

	//主键不是ID,要单独指定
	//protected $pk = 'id';
	
    protected static $model_key;
    protected static $base_table;
    protected static $table_pre;

    protected function initialize()
    {
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        self::$model_key = $array[0][1];
        self::$base_table = $array[0][1].'_content';
        self::$table_pre = config('database.prefix');
        //字段表，带数据表前缀如qb_form_field
        $this->table = self::$table_pre.self::$model_key.'_car';
    }
    
    //为了调用initialize初始化,生成数据表前缀$model_key
    protected static function scopeInitKey(){}
    
    /**
     * 获取用户的购物车数据 , 商家UID是一维数组下标,购物车及商品在二维数组那里
     * @param number $uid 购买者的UID
     * @param unknown $choose_type 是否只获取选中要购买的商品
     * @return array
     */
    public static function getList($uid=0,$choose_type=null){
        empty(self::$model_key) && self::InitKey();
        $map = [
                'uid'=>$uid,
        ];
        if($choose_type!==null){    //获取购物车中 选中或者是全部 商品
            $map['ifchoose'] = intval($choose_type);
        }
        
        $content_model = new Content();     //商品内容,在终极模型中才能这样使用
        
        $list_data = self::where($map)->order('update_time','desc')->column(true);  //用户的购物车数据
        //$field = [];
        foreach ($list_data AS $rs){
            $shop = $content_model->getInfoByid($rs['shopid'],true);    //取得商品的详细数据
            if(empty($shop)){
                self::destroy($rs['id']);   //商品若不存在,就把购物车记录删除
                continue ;
            }
            unset($shop['content'],$shop['full_content']);
            $shop['picurl'] && $shop['picurl'] = tempdir($shop['picurl']);            
            
//             $field || $field = get_field($shop['mid']);     //取得模型的所有字段的数据
//             $shop['_price'] = get_shop_price($shop,$rs['type1']-1);  //取得商品的实际价格,因为属性1可以定义价格
//             $shop['_num'] = intval($rs['num']); //购买数量
//             //得到用户购买的是什么颜色型号 , 用户选中的类型key要减1,因为入库前加过1 数组下标从0开始的,
//             $rs['type1'] && $shop['_type1'] = get_shop_type('type1',$shop,$rs['type1']-1,'title');
//             $rs['type2'] && $shop['_type2'] = get_shop_type('type2',$shop,$rs['type2']-1,'title');
//             $rs['type3'] && $shop['_type3'] = get_shop_type('type3',$shop,$rs['type3']-1,'title');
//             //得到颜色型号的真实叫法 如果用户没选择,或者是商品都不存在这项信息,就没必要去取出来
//             $shop['_type1'] && $shop['_type1_title'] = $field['type1']['title'];
//             $shop['_type2'] && $shop['_type2_title'] = $field['type2']['title'];
//             $shop['_type3'] && $shop['_type3_title'] = $field['type3']['title'];

            get_shop_price_type($shop,$rs);     //对价格与商品属性进行处理,得到实际商品属性的价格
            
            //为了后续方便扩展多商家店铺,把商家的UID做为一维数组下标
            $listdb[$shop['uid']][$rs['id']] = array_merge($shop,['_car_'=>$rs]);
        }
        return $listdb;
    }

	
}