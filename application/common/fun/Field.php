<?php
namespace app\common\fun;
class Field{
    
    /**
     * 获取树状分类的数值ID与名称标题的对应数组
     * @param string $str
     * @return array|unknown[]
     */
    public static function get_tree_title($str=''){
        $array = is_array($str) ? $str : json_decode($str,true);
        $data = [];
        foreach ($array AS $rs){
            $data[$rs['id']] = $rs['title'];
            if ($rs['children']) {
                $data = $data+self::get_tree_title($rs['children']);
            }
        }
        return $data;
    }
    
    /**
    * 用户要填写的表单字段
    * @param array $info 信息内容
    * @param array $f_array 程序中定义的字段数组
    * @param number $mid 模型ID
    * @param string $mod 频道目录名
    * @param string $field 过滤的字段
    * @return string|mixed
    */
    public static function get_form($info=[],$f_array=[],$mid=0,$mod='',$field=''){
        $filtrate_field = explode(',',$field);  //过滤的字段
        if(is_array($f_array)&&!empty($f_array)){
            $array = isset($f_array[0][0])?\app\common\field\Format::form_fields($f_array):$f_array;  //把程序中定义的表单字段 转成跟数据库取出的格式一样
        }else{
            $array = get_field($mid,$mod);
        }
        $obj = new \app\common\field\Form;      //目标是把字段转成对应的各种输入样式
        $data = [];
        foreach ($array AS $rs){
            if(in_array($rs['name'], $filtrate_field)){ //过滤的字段
                continue;
            }elseif($rs['group_post']!='' && !in_array(login_user('groupid'),explode(',',$rs['group_post']))){ //指定用户组才能使用的字段
                continue;
            }
            $data[] = array_merge($rs,$obj->get_field($rs,$info));      //取得每一项表单的最终转义后的效果
        }
        return $data;
    }
    
    /**
     * 前台自定义表单的填写字段
     * @param string $order_filed
     * @return array|string[][]|unknown[][]|mixed[][]
     */
    public static function order_field_post($order_filed=''){
        if ($order_filed=='') {
            return [];
        }
        $array = json_decode($order_filed,true);
        if (empty($array)){
            return [];
        }
        $data = [];
        $i=0;
        foreach($array AS $key=>$rs){
            $i++;
            if ($rs['type']=='select' || $rs['type']=='checkbox' || $rs['type']=='radio') {
                $detail = explode("\n",$rs['options']);
                $opt = [];
                foreach($detail AS $value){
                    $opt[$value] = $value;
                }
            }else{
                $opt=$rs['options'];
            }
            $data['order_field_'.$i] = array_merge($rs,[
                'type'=>$rs['type'],
                'name'=>'order_field_'.$i,
                'title'=>$rs['title'],
                'about'=>'',
                'options'=>$opt,
                'ifmust'=>$rs['must'],
                'customize'=>'customize',  //模板中的自定义字段的标志符
            ]);
        }
        return $data;
    }
    
    /**
     * 转义前台自定义表单的可显示的数据
     * @param string $value 用户提交的原始数据
     * @param array $f_array 通过order_field_post转义出来的字段格式
     * @return array|string|\app\common\field\string[]|\app\common\field\unknown[]|\app\common\Field\mixed[]
     */
    public static function order_field_format($value='',$f_array=[]){
        if ($value=='') {
            return [];
        }
        $array = json_decode($value,true);
        if (empty($array)){
            return [];
        }
        $info = [];
        $data = [];
        foreach($array AS $key=>$rs){
            $data[$rs['title']] = $rs['value'];
        }
        foreach($f_array AS $key=>$rs){
            if (isset($data[$rs['title']])) {
                $info[$key] = $data[$rs['title']];
            }            
        }
        $info = self::format($info,$field='',$pagetype='show',$sysname='',$f_array);
        return $info;
    }
    
    /**
     * 设置触发表单
     * @param array $array
     * @return void|string
     */
    public function setTrigger($array=[]){
        return \app\common\field\Form::setTrigger($array);
    }
    
    /**
     * 列出自字段字段的URL参数,指定$field某个字段的话,则过滤此字段
     * @param number $mid
     * @param string $field
     * @param string $dirname
     * @return string
     */
    public function get_filter_url($mid=0,$field='',$dirname=''){
        return \app\common\util\Field_filter::make_url($field,$mid,$dirname);
    }
    
    /**
     * 获取列表页的筛选字段
     * @param number $mid 模型ID
     */
    public function list_filter($mid=0){
        $array = \app\common\util\Field_filter::get_field($mid);
        foreach ($array AS $name=>$rs){
            $url = \app\common\util\Field_filter::make_url($name,$mid);  //其它字段的网址检测生成,如果值存在就生成,不存在值,就不生成
            $_ar = [];
            foreach($rs['options'] AS $k=>$v){
                if (is_array($v)) {
                    $_ar[] = [
                            'key'=>$v[0],
                            'title'=>$k,
                            'url'=>  $url . $name . '_1=' . $v[0] . '&'  . $name . '_2=' . $v[1] . '&'  . $name . '=' . $v[0],
                    ];
                }else{
                    $_ar[] = [
                            'key'=>$k,
                            'title'=>$v,
                            'url'=>  $url . $name . '=' . $k,
                    ];
                }                
            }
            $rs['opt'] = $_ar;
            $rs['opt_url'] = $url;
            $array[$name] = $rs;
        }
        return $array;
    }
    
    /**
     * 自定义字段前台转义后显示
     * @param array $info 信息原始内容
     * @param string $field 是否只转义某个字段
     * @param string $pagetype 参数主要是show 或 list 哪个页面使用,主要是针对显示的时候,用在列表页或者是内容页 , 内容页会完全转义,列表页的话,可能只转义部分,或者干脆不转义
     * @param string $sysname 频道目录名,默认为空,即当前频道
     * @param array $f_array 程序中定义的字段
     * @return string|\app\common\field\string[]|\app\common\field\unknown[]|\app\common\Field\mixed[]
     */
    public function format($info=[],$field='',$pagetype='list',$sysname='',$f_array=[]){
        if(is_array($f_array)&&!empty($f_array)){
            $field_array = \app\common\field\Format::form_fields($f_array);  //把程序中定义的表单字段 转成跟数据库取出的格式一样
        }else{
            $field_array = get_field($info['mid'],$sysname);
        }
        
        $value = '';
        if($field){
            $value = \app\common\field\Index::get_field($field_array[$field],$info,$pagetype);
        }else{
            foreach($field_array AS $name=>$rs){
                if($rs['group_view']!=''&&!in_array(login_user('groupid'), explode(',', $rs['group_view']))){  //指定用户组才能看
                    unset($info[$name]);
                    continue;
                }
                if (isset($info[$name]) && $rs['index_hide']!=1) {    //满足二开要求,前台不做转义
                    $info[$name] = \app\common\field\Index::get_field($rs,$info,$pagetype);
                }                
            }
        }
        return $field ? $value : $info;
    }
    
    /**
     * 生成筛选项的网址参数
     * @param string $type 过滤哪项参数,多个用逗号隔开
     * @return string
     */
    public function make_filter_url($type='zone_id'){
        $url = '';
        $type_array = explode(',',$type);
        $array = input();
        foreach ($array AS $key=>$value){
            if(!in_array($key, $type_array)){
                $url .= $key.'='.urlencode($value) . '&' ;
            }
        }
        return $url;
    }
    
    /**
     * 生成后台筛选URL
     */
    public function make_admin_filter_url($order='',$by='',$search_field='',$keyword=''){
        
        $array = [
                'search_field'=>$search_field,
                'keyword'=>$keyword,
                '_order'=>$order,
                '_by'=>$by,
        ];
        if (input('route.')) {
            $array = array_merge(input('route.'),$array);
        }
        return auto_url(request()->action(),$array);
    }
    
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
	 * @param array $array 所有字段配置数据
	 * @param array $info
	 * @return array[]
	 */
	public function fields_to_form($array=[],$info=[]){
	    $obj = new \app\common\field\Form;
	    $data = [];
	    foreach ($array AS $rs){
	        if($rs['group_post']!='' && !in_array(login_user('groupid'),explode(',',$rs['group_post']))){ //指定用户组才能使用的字段
	            continue;
	        }
	        $data[] = array_merge($rs,$obj->get_field($rs,$info));      //取得每一项表单的最终转义后的效果
	    }
	    return $data;
	}
	
	/**
	 * 把所有字段转义给列表显示
	 * @param array $array 所有字段配置数据
	 * @param array $info
	 * @return array[]
	 */
	public function fields_to_table($array=[],$info=[]){
	    $data = [];
	    foreach ($array AS $rs){
	        $ar = \app\common\field\Table::get_tab_field($rs,$info);
// 	        $data[] = [
// 	                'name'=>$ar['name'],
// 	                'title'=>$ar['title'],
// 	                'value'=>$ar['value'],
// 	        ];
	        $data[] = $ar;
	    }
	    return $data;
	}
	
	/**
	 * 取得后台列表页的右边菜单
	 * @param array $array
	 * @param array $info
	 * @return array
	 */
	public function get_rbtn($array=[],$info=[],$show_title=false){
	    return \app\common\field\Table::get_rbtn($array,$info,$show_title);
	}
	
	
}