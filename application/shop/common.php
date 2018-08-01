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
        return fun('shop@type_get_title_price',$type,$info,$key,$result_type);
    }    
}