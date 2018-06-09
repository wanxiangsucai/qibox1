<?php
namespace app\common\fun;
class Field{
    
    /**
     * 判断是否加载过某个JS 避免重复加载,一般用在生成表单页
     * @param string $type
     * @return boolean
     */
    public function load_js($type=''){
		static $ifload = [];
		if($ifload[$type]!==true){
			$ifload[$type] = true;
			return true;
		}		
	}
	
	/**
	 * 把程序中定义的字段,转成跟数据库中的格式类似,程序中定义的是以数组下标为数字开始的 比如 [ ['text','title','标题'] ]
	 * @param array $f_array
	 * @return unknown[][]|array[][]
	 */
	public function field_to_table($f_array=[]){
	    return  \app\common\field\Format::form_fields($f_array);
	}
	
	/**
	 * 把所有字段转出最终用户可以输入的表单格式 
	 * @param array $array
	 * @param array $info
	 * @return array[]
	 */
	public function fields_to_form($array=[],$info=[]){
	    $obj = new \app\common\field\Form;
	    $data = [];
	    foreach ($array AS $rs){
	        $data[] = array_merge($rs,$obj->get_field($rs,$info));      //取得每一项表单的最终转义后的效果
	    }
	    return $data;
	}
	
	/**
	 * 把所有字段转义给列表显示
	 * @param array $array
	 * @param array $info
	 * @return array[]
	 */
	public function fields_to_table($array=[],$info=[]){
	    $data = [];
	    foreach ($array AS $rs){
	        $ar = \app\common\field\Table::get_tab_field($rs,$info);
	        $data[] = [
	                'title'=>$ar['title'],
	                'value'=>$ar['value'],
	        ];
	    }
	    return $data;
	}
	
	/**
	 * 取得后台列表页的右边菜单
	 * @param array $array
	 * @param array $info
	 * @return array
	 */
	public function get_rbtn($array=[],$info=[]){
	    return \app\common\field\Table::get_rbtn($array,$info);
	}
	
	
}