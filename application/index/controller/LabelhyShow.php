<?php
namespace app\index\controller;

use app\index\model\Labelhy AS LabelModel;

class LabelhyShow extends LabelShow
{
    public static $pri_hy_js=null;
    
    /**
     * 得到圈子黄页的ID,为的是给分页URL使用
     * @param number $id
     * @return number
     */
    protected function get_hy_id($id=0){
        static $hy_id;
        if ($id) {
            $hy_id = $id;
        }
        return $hy_id;
    }
    
    
    
    /**
     * 通用标签AJAX获取分页数据
     * @param string $name 标签变量名
     * @param string $page 第几页
     * @param string $pagename 标签所在哪个页面
     */
    public function ajax_get($name='' , $page='' , $pagename='' , $hy_id=0){
        
        //对应fetch方法,传入一些常用的参数
        $admin = $this->admin;
        $userdb = $this->user;
        $timestamp = $this->timestamp;
        $webdb = $this->webdb;
        
        $parameter =get_post(); //这里不能用input 因为GET的优先级更高
        foreach ($parameter AS $key=>$value){
            if($value===''){
                unset($parameter[$key]);    //避免空值也执行where语句
            }else{
                //$value = urldecode(urldecode($value));
                //$value = urldecode($value);
                if( strstr($value,"'") ){
                    continue;
                }
                if (strstr($key,'?')) {
                    $parameter[$key] = $value;
                    $key = str_replace('?','',$key);
                    $this->request->get([$key=>$value]);
                    $$key=$value;
                }
                $parameter[$key] = $value;
            }
        }
        
        $_cfg = cache('tag_default_'.$name);    //主要为的是传递where参数
        $parameter = array_merge($_cfg,$parameter);
        
        $live_cfg = self::union_live_parameter($parameter);
        
        $tag_array = LabelModel::get_tag_data_cfg($name , $pagename , $page, $live_cfg, $hy_id);
        //$view_tpl = cache('tags_tpl_code_'.$name);      //原始模板缓存，非数据库的
        $_array = cache('tags_page_demo_tpl_'.$pagename);      //原始模板缓存，非数据库的
        if(!empty($_array)){
            $view_tpl = $_array[$name]['tpl'];
        }
        
        if(!empty($tag_array['view_tpl'])){         //数据库设定的模板优先
            $view_tpl = $tag_array['view_tpl'];
        }
        
        $__LIST__ = $tag_array['data'];
        $array = $tag_array['pages'];       //分页数据
        
        
        if(empty($tag_array)){    //未入库前,随便给些演示数据
            $live_cfg && $_cfg = array_merge($_cfg,$live_cfg) ;
            $_cfg['sys_type'] && $_cfg['systype'] = $_cfg['sys_type'];      //重新定义了调取数据的类型, 也即动态变换
            $array = self::get_default_data($_cfg['systype']?$_cfg['systype']:'cms',$_cfg,$page,false);
            $__LIST__ = is_array($array['data']) ? $array['data'] : $array; //不是数组的时候,就是单张图片,或纯HTML代码
        }
        
        //用户自定义了循环变量,比如listdb
        $val = $_cfg['val'];
        if(!empty($val)){
            $$val = $__LIST__;
        }
        
        if(empty($view_tpl)){
            //die('tpl not exists !');
            return $this->err_js('not_tpl');
        }
        
        if(empty($__LIST__)){
            //die('null');
            $content = '';
        }else{
            @ob_end_clean();ob_start();
            eval('?>'.$view_tpl);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $array['data'] = $content;
        return $this->ok_js($array);
    }
    
    
    /**
     * 生成通用标签的AJAX地址
     * @param array $array
     * @return mixed
     */
    protected function build_tag_ajax_url($array=[]){
        $array['hy_id'] = $this->get_hy_id();   //不同于系统标签,这里必须要传递一下圈子黄页的ID
        $array['sys_type'] = $this->get_sys_type();   //同一个标签,动态更换系统 type 参数
//         foreach($array AS $key=>$value){
//             $array[$key] = urlencode($value);
//         }
//         return iurl('index/labelhy_show/ajax_get',$array);
        return iurl('index/labelhy_show/ajax_get').'?'.http_build_query($array).'&';
    }
    
    public function get_label($tag_name='',$cfg=[]){
        $hy_id = $cfg['hy_id'];                                          //店铺ID
        if (empty($hy_id)) {
            die('------店铺ID不存在------------');
        }
        $this->get_hy_id($hy_id);
        $this->get_sys_type($cfg['sys_type']);
        $filtrate_field = $cfg['field'];                                 //循环字段指定不显示哪些
        $val = $cfg['val'];                                                 //取得数据后，赋值到这个变量名, 分页的话,没做处理会得不到
        $list = $cfg['list'];                                                //foreach输出 AS 后面的变量名
        $cfg['sys_type'] && $cfg['systype'] = $cfg['sys_type'];     //重新定义了调取数据的类型, 也即动态变换 type
        $type = $cfg['systype']?$cfg['systype']:'choose';            //选择哪种标签，图片或代码等等
        //         $pagename = md5( basename($cfg['dirname']) );       //模板目录名
        //if(empty($cfg['mid']))unset($cfg['mid']);       //避免影响到union那里动态调用mid
        if($cfg['mid']==-1){    // mid=-1 时 , 标志取所有模型的数据, 一般不建议这么做,效率非常低
            unset($cfg['mid']);
            $get_all_model = true;
        }else{
            $get_all_model = false;
        }
        static $pagename = null;    //避免重复执行
        $pagename===null && $pagename = md5( $cfg['dirname'] );       //模板目录名
        $ifdata = intval($cfg['ifdata']);                            //是否只要原始数据
        $dirname = $cfg['dirname'];
        static $filemtime = null;   //避免重复执行
        $filemtime===null && $filemtime = filemtime($cfg['dirname']);      //记录模板文件的修改时间，模板修改后，就取消缓存
        //某个页面所有标签的模板代码与演示数据
        static $page_demo_tpl_tags = null; //避免重复执行
        $page_demo_tpl_tags === null && $page_demo_tpl_tags = cache('tags_page_demo_tpl_'.$pagename);
        static $tpl_have_edit = null;   //做个记录,因为多次执行本函数后,这个值会变的.让他不要再变
        if($filemtime!=$page_demo_tpl_tags['_filemtime_']){  //模板被修改过
            $tpl_have_edit = true;
        }
        
        $tag_array = cache($hy_id.'qb_tag_'.$tag_name);        //取得具体某个标签的数据库配置参数，对于取文章列表的，也会同时得到相应的数据
        if(empty($tag_array)||$tpl_have_edit){
            $tag_array = LabelModel::get_tag_data_cfg($tag_name , $pagename , 1 , self::union_live_parameter($cfg) , $hy_id );
            $tag_array['cache_time']>0 && cache($hy_id.'qb_tag_'.$tag_name,$tag_array,$tag_array['cache_time']);
        }
        
        if(!empty($tag_array) && !empty($tag_array['type'])){
            $type = $tag_array['type'];
        }
        
        //$rows = $tag_array['rows']?$tag_array['rows']:$cfg['rows'];     //分页可能会用到
        
        if($tpl_have_edit){
            static $have_get_tpl = null;
            if($have_get_tpl === null){ //避免重复执行
                $have_get_tpl = true;
                $page_demo_tpl_tags = self::get_page_demo_tpl($cfg['dirname']);
                $page_demo_tpl_tags['_filemtime_'] = $filemtime;
                cache('tags_page_demo_tpl_'.$pagename,$page_demo_tpl_tags,3600);
            }
        }
        
        echo self::pri_jsfile($pagename, $hy_id);                                   //输出JS文件
        echo  self::pri_tag_div($tag_name,$type,$tag_array,$cfg['class']);    //输出标签的操作层
        
        if(empty($tag_array)){     //新标签还没有入库就输出演示数据
            if($cfg['sql']){    //SQL原生查询语句
                $cfg['class'] = "app\\index\\controller\\LabelShow@labelGetSql";
            }
            //未入库前,标签默认指定的频道数据作为演示用
            if(empty($cfg['mid']) && !$get_all_model && !in_array('mid',explode(',',$cfg['union']))){
                $cfg['mid'] = 1; //指定模型效率会高点,但前提是模型1必须要存在,不然就会报错
            }
            if($type=='member'&&empty($cfg['class'])){
                $cfg['class'] = "app\\common\\model\\User@labelGet";
            }
            if(    ($type&&( modules_config($type)||plugins_config($type) ))    ||    $cfg['class']    ){
                if($tpl_have_edit || empty( cache('tag_default_'.$tag_name) )){   //没入库前,也方便AJAX获取更多分页使用
                    cache('tag_default_'.$tag_name,$cfg,3600);
                }
            }
        }
        
        self::tag_cfg_parameter($tag_name,$cfg);  //把$cfg存放起来,给get_ajax_url使用
        
        //对应fetch方法,传入一些常用的参数
        $admin = $this->admin;
        $userdb = $this->user;
        $timestamp = $this->timestamp;
        $webdb = $this->webdb;
        
        //指定了过滤字段,代表想要取某些字段的数值,一般用在列表页,不适合聚合信息页多个频道混调
        $fields = ($filtrate_field && $cfg['mid']) ? $this->list_show_field( get_field($cfg['mid']) , $filtrate_field ) : [];
        
        if($cfg['js']){ //ajax显示数据,可以加快页面的打开速度
            $ajaxurl = $this->build_tag_ajax_url( array_merge(
                    [
                            'name'=>$tag_name,
                            'pagename'=>$pagename,
                            'page'=>1,
                    ],
                    self::union_live_parameter($cfg)
                    ));
            print<<<EOT
<script type="text/javascript">
//对标签进行特殊处理
var code{$tag_name} = \$(".{$cfg['js']} .p8label");
code{$tag_name} = code{$tag_name}.length>0 ? code{$tag_name}.prop("outerHTML") : '';
\$(".{$cfg['js']}").html('内容加载中...');
\$(document).ready(function(){
	\$.get("{$ajaxurl}",function(res){
        if(res.code==0){
             \$(".{$cfg['js']}").html(code{$tag_name}+res.data);
        }else{
            layer.msg(res.msg,{time:500});
        }
       if(typeof({$cfg['js']})=='function'){ {$cfg['js']}(res); }
	});
               
});
</script>
EOT;
            return ;
        }
        
        if(empty($tag_array)){     //新标签还没有入库就输出演示数据
            if( ($type&&( modules_config($type)||plugins_config($type) ))  ||  $cfg['class']){
                $default_data = self::get_default_data($type,$cfg);
                if(!empty($val)){
                    $$val = $default_data;
                }else{
                    $__LIST__ = $default_data;
                }
                eval('?>'.$page_demo_tpl_tags[$tag_name]['tpl']);
                return ;
            }
            eval('?>'.$page_demo_tpl_tags[$tag_name]['demo']);
            
            return ;
            
        }else{
            
            //纯文本就直接输出
            if($type=='text'||$type=='txt'||$type=='textarea'||$type=='ueditor'){
                /*eval('?>'.$tag_array['data']);*/
                echo $tag_array['data'];
                return ;
            }elseif($type=='image'){    //单张图片,特别处理                
                $_tpl = $page_demo_tpl_tags[$tag_name]['tpl'];
                if(strstr($_tpl,'<?php')){	//单张图,有模板的情况
                    $picurl = $tag_array['data']['picurl'];
                    $url = $tag_array['data']['url'];
                    eval('?>'.$_tpl);
                }else{	//单张图,没有模板就直接输出图片
                    echo $tag_array['format_data'];
                }
                return $tag_array['data'];
            }elseif($type=='link'){     //菜单链接
                $_tpl = $page_demo_tpl_tags[$tag_name]['tpl'];
                $url = $tag_array['data']['url'];
                $title = $tag_array['data']['title'];
                $logo = $tag_array['data']['logo'];
                eval('?>'.$_tpl);
                return $tag_array['data'];
            }
            //针对图片处理
            $_cfg = unserialize($tag_array['cfg']);
            $_cfg['pic_width'] && $pic_width = $_cfg['pic_width'];
            $_cfg['pic_height'] && $pic_height = $_cfg['pic_height'];
            
            $__LIST__ = $tag_array['data'];
            if(!empty($val)){
                $$val = $__LIST__ ;
            }
            
            //什么都没有设置的时候，就直接输出
            if(empty($val)&&empty($page_demo_tpl_tags[$tag_name]['demo'])){
                $_tpl = trim(preg_replace('/<\?php(.*?)\?>/is','',$page_demo_tpl_tags[$tag_name]['tpl']));
                if(empty($_tpl)){
                    echo $tag_array['format_data']?$tag_array['format_data']:$tag_array['data'];
                    reutrn ;
                }
            }
            if( $tag_array && trim($tag_array['view_tpl'])!='' ){         //数据库设定的模板优先
                $tpl = $tag_array['view_tpl'];
            }else{
                $tpl = $page_demo_tpl_tags[$tag_name]['tpl'];
                if($type=='images'&&trim(preg_replace('/<\?php (.*?)\?>/is','',$tpl))==''){	        //对于组图,没有默认模板的情况
                    echo $page_demo_tpl_tags[$tag_name]['demo'];
                    return ;
                }
            }
            eval('?>'.$tpl);
            return unserialize($tag_array['cfg']);   //显示更多分页可能会用到,比如可以判断数据少于rows的话,是否有需要显示更多按钮
        }
    }
    
    
    protected  function pri_jsfile($pagename='', $hy_id=0){
        
        if(self::$pri_hy_js === null){
            self::$pri_hy_js = true;
            
            if( input('get.labelhy_set')!='' ) {
                set_cookie('labelhy_set',input('get.labelhy_set'));
            }
            
            $this->weburl = str_replace(array('labelhy_set=quit','labelhy_set=set','&&'), '', $this->weburl);
            $weburl = strpos($this->weburl,'?') ? ($this->weburl.'&') : ($this->weburl.'?') ;

            if(input('get.labelhy_set')=='set' || get_cookie('labelhy_set')=='set'){
                if(in_wap()){
                    $label_iframe_width = '95%';
                    $label_iframe_height = '80%';
                }else{
                    $label_iframe_width = '60%';
                    $label_iframe_height = '80%';
                }
                
                $admin_url = iurl('index/labelhy/index',"pagename=$pagename&hy_id=$hy_id");
                echo   "
                <SCRIPT LANGUAGE='JavaScript' src='/public/static/js/label.js'></SCRIPT>
                <SCRIPT LANGUAGE='JavaScript'>
                var admin_url='$admin_url',fromurl='/',label_iframe_width='$label_iframe_width',label_iframe_height='$label_iframe_height';
                </SCRIPT>";
            }
        }
    }
    
    /**
     * 标签上面显示的遮蔽层
     * @param string $tag_name
     * @param string $type
     * @param array $tag_array
     * @param string $class_name
     */
    protected  function pri_tag_div($tag_name='',$type='',$tag_array=[],$class_name=''){
        if(input('get.labelhy_set')=='set' || get_cookie('labelhy_set')=='set'){
            $tag_array['cfg'] = unserialize($tag_array['cfg']);
            $div_w = $tag_array['cfg']['div_width']>10?$tag_array['cfg']['div_width']:100;
            $div_h = $tag_array['cfg']['div_height']>10?$tag_array['cfg']['div_height']:30;
            $div_bgcolor = 'orange';
            if (($type=='choose'||$type=='classname') && $class_name!='') {
                $type = str_replace('\\', '--', $class_name);
            }
            
            echo "<div  class=\"p8label\" id=\"$tag_name\" style=\"filter:alpha(opacity=50);position: absolute; border: 1px solid #ff0000; z-index: 9999; color: rgb(0, 0, 0); text-align: left; opacity: 0.4; width: {$div_w}px; height:{$div_h}px; background-color:$div_bgcolor;\" onmouseover=\"showlabel_(this,'over','$type','');\" onmouseout=\"showlabel_(this,'out','$type','');\" onclick=\"showlabel_(this,'click','$type','$tag_name');\"><div onmouseover=\"ckjump_(0);\" onmouseout=\"ckjump_(1);\" style=\"position: absolute; width: 15px; height: 15px; background: url(/public/static/js/se-resize.png) no-repeat scroll -8px -8px transparent; right: 0px; bottom: 0px; clear: both; cursor: se-resize; font-size: 1px; line-height: 0%;\"></div></div>
            ";
        }        
    }
    
    
}
