<?php 

if(!function_exists('get_shop_type')){
    /**
     * 取得商品属性 , $key 为 null 的话,商品内容页使用,全部列出给用户选择
     * @param string $type              属性类型,可以是 type1 type2 type3 分别可以为尺寸\颜色\长短
     * @param array $info               商品主表的内容信息
     * @param unknown $key          为null的话,商品详情页使用,所有各项参数展示出来,为数值的话,就是用户选中购买的具体类型
     * @param string $result_type   要取得属性的名称,还是价格,一般都是名称.
     * @return void|array|unknown[]|array[]
     */
    function get_shop_type($type='type1',$info=[],$key=null,$result_type='title'){
        if (empty($info[$type])) {
            return ;
        }
        $array = json_decode($info[$type],true);  //数据库存放的格式是 ["红","黄","蓝"]
        foreach ($array AS $_key=>$_value){
            list($title,$price,$num) = explode('|',$_value);
            if($key!==null){
                if($key==$_key){
                    if($result_type=='title'){
                        return $title;
                    }elseif($result_type=='price'){
                        return $price;
                    }
                }
            }else{
                $listdb[] = [
                        'title'=>$title,
                        'price'=>$price,
                        'num'=>$num,
                ];
            }
        }
        if($key===null){
            return $listdb;
        }
    }
    
    /**
     * 取得商品的实际价格,商品属性1可以自定商品价格
     * 第二项,取得商品的实际价格,因为属性1可以定义价格,数组下标要减1,因为购物车入库时加了1
     * @param array $info 商品信息
     * @param number $key 用户选中属性1的具体某项
     */
    function get_shop_price($info=[],$key=0){
        $value = get_shop_type('type1',$info,$key,'price');
        if($value>0){
            return $value;
        }else{
            return $info['price'];
        }
    }
    
    
    function get_shop_price_type(&$shop=[],$rs=['num'=>1,'type1'=>0,'type2'=>0,'type3'=>0]){
        static $field_array = [];
        //这里用数组是考虑有可能会存在不同的模型
        if(empty($field_array[$shop['mid']])){
            $field_array[$shop['mid']] = get_field($shop['mid']);     //取得模型的所有字段的数据
        }
        $field = $field_array[$shop['mid']];
        $shop['_price'] = get_shop_price($shop,$rs['type1']-1);     //取得商品的实际价格,因为属性1可以定义价格,数组下标要减1,因为购物车入库时加了1
        $shop['_num'] = intval($rs['num']); //购买数量
        //得到用户购买的是什么颜色型号 , 用户选中的类型key要减1,因为入库前加过1 数组下标从0开始的,
        $rs['type1'] && $shop['_type1'] = get_shop_type('type1',$shop,$rs['type1']-1,'title');
        $rs['type2'] && $shop['_type2'] = get_shop_type('type2',$shop,$rs['type2']-1,'title');
        $rs['type3'] && $shop['_type3'] = get_shop_type('type3',$shop,$rs['type3']-1,'title');
        //得到颜色型号的真实叫法 如果用户没选择,或者是商品都不存在这项信息,就没必要去取出来
        $shop['_type1'] && $shop['_type1_title'] = $field['type1']['title'];
        $shop['_type2'] && $shop['_type2_title'] = $field['type2']['title'];
        $shop['_type3'] && $shop['_type3_title'] = $field['type3']['title'];
        $shop['_num'] = $rs['num']; //购买了多少件
    }
}