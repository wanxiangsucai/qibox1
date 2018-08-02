<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use app\index\model\Label AS LabelModel;

class LabelShow extends IndexBase
{
    public static $pri_js=null;
    public static $list_page_cfg = [];   //列表页的标签参数，主要是给AJAX传输数据用
    
    /**
     * 站外调用标签数据,比如APP或小程序,又或者站外JS
     * @param string $name 关键字
     * @param string $page
     * @return \think\response\Json
     */
    public function app_get($name='' , $page='' ){
        $page<1 && $page=1;
        $name && $array = LabelModel::app_get_data($name , $page);
        
        if(empty($array)){
            if(empty($this->webdb['open_app_get'])){
                return $this->err_js('为安全考虑,系统默认没有开放随意调用数据功能,要启用,请在开发者中心添加参数open_app_get为1');
            }
            //return $this->err_js('标签不存在!');
            //数据库标签不存在的时候,调用指定数据,需要做进一步的安全过滤
            $_array = input();
            $type = $_array['type'];
            if($type=='member'){
                $cfg['class'] = "app\\common\\model\\User@labelGet";
            }
            $cfg['order'] = $_array['order'];
            $cfg['by'] = $_array['by'];
            $cfg['where'] = $_array['where'];
            $cfg['mid'] = $_array['mid'];
            $cfg['fid'] = $_array['fid'];
            $cfg['rows'] = $_array['rows'];
            $array = self::get_default_data($type,$cfg,$page,false);            
            return $this->ok_js($array);
        }
        
        //这里需要对内容数据做清除,不然有点占用带宽        
        if(empty($array['data'])){
            return $this->err_js('数据不存在!');
        }else{
            if($array['pages']){
                $data = $array['pages'];
                $data['data'] = $array['data'];
            }else{
                $data = $array['data'];
            }
            return $this->ok_js($data);
        }
    }
    
    
    /**
     * 通用标签AJAX获取分页数据
     * @param string $name 标签变量名
     * @param string $page 第几页
     * @param string $pagename 标签所在哪个页面
     */
    public function ajax_get($name='' , $page='' , $pagename=''){
        
        //对应fetch方法,传入一些常用的参数
        $admin = $this->admin;
        $userdb = $this->user;
        $timestamp = $this->timestamp;
        $webdb = $this->webdb;
        
        $parameter =get_post(); //这里不能用input 因为GET的优先级更高      

        $live_cfg = self::union_live_parameter($parameter);
        
        $tag_array = LabelModel::get_tag_data_cfg($name , $pagename , $page, $live_cfg);
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
        
        $_cfg = cache('tag_default_'.$name);
        if(empty($tag_array)){    //未入库前,随便给些演示数据            
            $live_cfg && $_cfg = array_merge($_cfg,$live_cfg) ;
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
    
    
    //多行文本
    public function labelGetTextarea($tag_array=[]){
        return $tag_array['extend_cfg'];
//         return [
//                 'data'=>$extend_cfg,
//                 'format_data'=>str_replace(array("\r\n",' '),array('<br>','&nbsp;'),$extend_cfg),
//         ];
    }
    
    //在线编辑器
    public function labelGetUeditor($tag_array=[]){
        return $tag_array['extend_cfg'];
    }
    
    //组图
    public function labelGetImages($tag_array=[]){
        $array = json_decode($tag_array['extend_cfg'],true);
        foreach($array AS $key=>$rs){
            if(empty($rs['picurl'])){
                unset($array[$key]);
            }
            $array[$key]['picurl'] = tempdir($rs['picurl']);
        }
        return $array;

    }
    
    //单张图片
    public function labelGetImage($tag_array=[]){
        list($picurl,$url) = explode(',',$tag_array['extend_cfg']);
        $picurl = tempdir($picurl);
        return [
                'data'=>['picurl'=>$picurl,'url'=>$url],
                'format_data'=>$url?"<a href='$url' target='_blank'><img src='$picurl'></a>":'<img src="'.$picurl.'">',
        ];
    }
    
    //SQL高级查询
    public function labelGetSql($tag_array=[]){
        $cfg = unserialize($tag_array['cfg']);
        if($cfg['sql']){
            $cfg['sql'] = str_replace('{pre}', config('database.prefix'), $cfg['sql']);
            $array = query($cfg['sql']);
            return $array;
        }
    }
    
    
    private  function pri_jsfile($pagename=''){

        if(self::$pri_js==null){
            self::$pri_js=true;
            
            $this->weburl = str_replace(array('label_set=quit','label_set=set','&&'), '', $this->weburl);
            $weburl = strpos($this->weburl,'?') ? ($this->weburl.'&') : ($this->weburl.'?') ;
            
            if(LABEL_SET!==true){
                if($this->user['groupid']==3){
                    echo   "
                    <SCRIPT LANGUAGE='JavaScript'>
                    $('body').dblclick(function(){var msg=confirm('你确认要进入标签管理吗?');if(msg==true){window.location.href='{$weburl}label_set=set';}});
                    </SCRIPT>";
                }
                return ;
            }
            
            
            $admin_url = iurl('index/label/index',"pagename=$pagename");
            echo   "
            <SCRIPT LANGUAGE='JavaScript' src='/public/static/js/label.js'></SCRIPT>
            <SCRIPT LANGUAGE='JavaScript'>
            var admin_url='$admin_url';
            var fromurl='/';
            $('body').dblclick(function(){var msg=confirm('你确认要退出标签管理吗?');if(msg==true){window.location.href='{$weburl}label_set=quit';}});
        </SCRIPT>";
        }
    }
    
    private  function pri_tag_div($tag_name='',$type='',$tag_array=[],$class_name=''){
        if(LABEL_SET!==true){
            return ;
        }
        $tag_array['cfg'] = unserialize($tag_array['cfg']);
        $div_w = $tag_array['cfg']['div_width']>10?$tag_array['cfg']['div_width']:100;
        $div_h = $tag_array['cfg']['div_height']>10?$tag_array['cfg']['div_height']:30;
        $div_bgcolor = '#A6A6FF';
        if (($type=='choose'||$type=='classname') && $class_name!='') {
            $type = str_replace('\\', '--', $class_name);
        }
        
        echo "<div  class=\"p8label\" id=\"$tag_name\" style=\"filter:alpha(opacity=50);position: absolute; border: 1px solid #ff0000; z-index: 9999; color: rgb(0, 0, 0); text-align: left; opacity: 0.4; width: {$div_w}px; height:{$div_h}px; background-color:$div_bgcolor;\" onmouseover=\"showlabel_(this,'over','$type','');\" onmouseout=\"showlabel_(this,'out','$type','');\" onclick=\"showlabel_(this,'click','$type','$tag_name');\"><div onmouseover=\"ckjump_(0);\" onmouseout=\"ckjump_(1);\" style=\"position: absolute; width: 15px; height: 15px; background: url(/public/static/js/se-resize.png) no-repeat scroll -8px -8px transparent; right: 0px; bottom: 0px; clear: both; cursor: se-resize; font-size: 1px; line-height: 0%;\"></div></div>
        ";
    }
    
    /**
     * 评论标签模板
     * @param string $tag_name 标签名字
     * @param array $cfg 配置参数
     */
    public function comment_label($tag_name='',$info=[],$cfg=[]){
//         $pagename = md5( basename($cfg['dirname']) );
        $pagename = md5( $cfg['dirname'] );
        $cache_time = $cfg['cache_time'];
        $aid = empty($info['id'])?$cfg['aid']:$info['id'];  //频道内容ID
        $sysid = $cfg['sysid'];  //频道系统ID
        $rows = $cfg['rows'];
        $order = $cfg['order'];
        $by = $cfg['by'];
        $status = $cfg['status'];
        $val = $cfg['val'];
        $cfg_array =  [
                'pagename'=>$pagename,
                'name'=>$tag_name,
                'rows'=>$cfg['rows'],
                'status'=>$cfg['status'],
                'order'=>$cfg['order'],
                'by'=>$cfg['by'],
        ];
        
        $filemtime = filemtime($cfg['dirname']);      //记录模板文件的修改时间，模板修改后，就取消缓存
        //获取标签模板
        $label_tags_tpl = cache('tags_comment_tpl_'.$pagename);
        
        $tag_array = cache('qb_tag_'.$tag_name);    //数据库参数配置文件
        //如果模板文件修改过，也要重新生成缓存
        if(empty($tag_array) || $filemtime!=$label_tags_tpl['_filemtime_']){
            $tag_array = LabelModel::get_tag_data_cfg($tag_name , $pagename);
            $tag_array['cache_time']>0 && cache('qb_tag_'.$tag_name,$tag_array,$tag_array['cache_time']);
        }
        
        if(!empty($tag_array)){
            $_cfg = unserialize($tag_array['cfg']);
            is_array($_cfg) && $cfg_array = array_merge($cfg_array,$_cfg);    //数据库的参数配置优先级更高
        }
        
        $list_data = cache('qb_tag_data_'.$tag_name.$aid);    //首页缓存文件        
        if($cfg['page']>1 || empty($list_data)){     //第2页以上不用缓存
            $list_data = getArray( fun('label@comment_api','list',$aid,$sysid,$cfg_array) );
            //cache('qb_tag_data_'.$tag_name.$aid,$list_data,$tag_array['cache_time']);
        }
        $tag_data = $list_data['data'];     //评论内容
        $total = $list_data['total'];           //评论总数,不包括引用评论

        if($filemtime!=$label_tags_tpl['_filemtime_']){
            $label_tags_tpl = self::get_comment_tpl($cfg['dirname'],$aid,$sysid,$cfg_array);
            $label_tags_tpl['_filemtime_'] = $filemtime;
            //标签模板缓存起来，提高效率
            cache('tags_comment_tpl_'.$pagename,$label_tags_tpl,3600);
        }
        
        $rows = $tag_array['rows']?$tag_array['rows']:$cfg['rows'];     //分页可能会用到
        
        $type = config('system_dirname');   //系统模块目录名
        
        
        self::pri_jsfile($pagename);                                                              //输出JS文件
        self::pri_tag_div($tag_name,'comment_set_'.$type,$tag_array);             //输出标签的操作层
        
        if($tag_array && trim($tag_array['view_tpl']!='')){         //数据库设定的模板优先
            $tpl = $tag_array['view_tpl'];
        }else{
            $tpl = $label_tags_tpl[$tag_name];
        }
        $listdb = $tag_data;
        eval('?>'.$tpl);
    }
    
    /**
     * 回复标签模板,跟上面的插件评论标签类似.
     * @param string $tag_name 标签名字
     * @param array $cfg 配置参数
     */
    public function reply_label($tag_name='',$info=[],$cfg=[]){
        //         $pagename = md5( basename($cfg['dirname']) );
        $pagename = md5( $cfg['dirname'] );
        $cache_time = $cfg['cache_time'];
        $aid = empty($info['id'])?$cfg['aid']:$info['id'];  //频道内容ID
        $sysid = $cfg['sysid'];  //频道系统ID
        $rows = $cfg['rows'];
        $order = $cfg['order'];
        $by = $cfg['by'];
        $status = $cfg['status'];
        $val = $cfg['val'];
        $cfg_array =  [
                'pagename'=>$pagename,
                'name'=>$tag_name,
                'rows'=>$cfg['rows'],
                'status'=>$cfg['status'],
                'order'=>$cfg['order'],
                'by'=>$cfg['by'],
        ];
        
        $filemtime = filemtime($cfg['dirname']);      //记录模板文件的修改时间，模板修改后，就取消缓存
        //获取标签模板
        $label_tags_tpl = cache('tags_reply_tpl_'.$pagename);
        
        $tag_array = cache('qb_tag_'.$tag_name);    //数据库参数配置文件
        //如果模板文件修改过，也要重新生成缓存
        if(empty($tag_array) || $filemtime!=$label_tags_tpl['_filemtime_']){
            $tag_array = LabelModel::get_tag_data_cfg($tag_name , $pagename);
            $tag_array['cache_time']>0 && cache('qb_tag_'.$tag_name,$tag_array,$tag_array['cache_time']);
        }
        
        if(!empty($tag_array)){
            $_cfg = unserialize($tag_array['cfg']);
            is_array($_cfg) && $cfg_array = array_merge($cfg_array,$_cfg);    //数据库的参数配置优先级更高
        }
        
        $tag_data = cache('qb_tag_data_'.$tag_name.$aid);    //首页缓存文件
        if($cfg['page']>1 || empty($tag_data)){     //第2页以上不用缓存
            $tag_data = fun('label@reply_api','list',$aid,$cfg_array);
            //$tag_data = controller("plugins\\comment\\index\\Api")->get_list($sysid,$aid,$rows,$_type,$order,$by,$page);
            //cache('qb_tag_data_'.$tag_name.$aid,$tag_array,$tag_array['cache_time']);
        }
        
        if($filemtime!=$label_tags_tpl['_filemtime_']){
            $label_tags_tpl = self::get_reply_tpl($cfg['dirname'],$aid,$sysid,$cfg_array);
            $label_tags_tpl['_filemtime_'] = $filemtime;
            //标签模板缓存起来，提高效率
            cache('tags_reply_tpl_'.$pagename,$label_tags_tpl,3600);
        }
        
        $rows = $tag_array['rows']?$tag_array['rows']:$cfg['rows'];     //分页可能会用到
        
        $type = config('system_dirname');   //系统模块目录名
        
        
        self::pri_jsfile($pagename);                                                              //输出JS文件
        self::pri_tag_div($tag_name,'reply_set_'.$type,$tag_array);             //输出标签的操作层
        
        if($tag_array && trim($tag_array['view_tpl']!='')){         //数据库设定的模板优先
            $tpl = $tag_array['view_tpl'];
        }else{
            $tpl = $label_tags_tpl[$tag_name];
        }
        $listdb = $tag_data;
        eval('?>'.$tpl);
    }
    
    /**
     * 内容页标签模板
     * @param string $tag_name 标签名字
     * @param array $cfg 配置参数
     */
    public function showpage_label($tag_name='',$info=[],$cfg=[]){
//         $pagename = md5( basename($cfg['dirname']) );
        $f_array = $cfg['f_array'];     //程序中定义的字段
        $pagename = md5( $cfg['dirname'] );
        $cache_time = $cfg['cache_time'];
        $filtrate_field = $cfg['field'];         //循环中过滤不显示的字段
        $cfg_array =  [
                'pagename'=>$pagename,
                'tpl'=>$cfg['tpl'],
                'cache_time'=>$cache_time,
        ];
        
        $filemtime = filemtime($cfg['dirname']);      //记录模板文件的修改时间，模板修改后，就取消缓存
        //获取页面的所有标签模板
        $label_tags_tpl = cache('tags_showpage_tpl_'.$pagename);
        
        $tag_array = cache('qb_tag_'.$tag_name);    //数据库参数配置文件
        //如果模板文件修改过，也要重新生成缓存
        if(empty($tag_array) || $filemtime!=$label_tags_tpl['_filemtime_']){
            $tag_array = LabelModel::get_tag_data_cfg($tag_name , $pagename);
            $tag_array['cache_time']>0 && cache('qb_tag_'.$tag_name,$tag_array,$tag_array['cache_time']);
        }
        
        if(!empty($tag_array)){
            $_cfg = unserialize($tag_array['cfg']);
            is_array($_cfg) && $cfg_array = array_merge($cfg_array,$_cfg);    //数据库的参数配置优先级更高
        }
        
//         $tag_data = cache('qb_tag_data_'.$tag_name);    //首页缓存文件
//         if($cfg['page']>1 || empty($tag_data)){     //第2页以上不用缓存
//             $tag_data = controller('content','index')->label_list_data($cfg_array);
//             //cache('qb_tag_data_'.$tag_name,$tag_array,$tag_array['cache_time']);
//         }

        if($filemtime!=$label_tags_tpl['_filemtime_']){
            $label_tags_tpl = self::get_showpage_tpl($cfg['dirname']);
            $label_tags_tpl['_filemtime_'] = $filemtime;
            //标签模板缓存起来，提高效率
            cache('tags_showpage_tpl_'.$pagename,$label_tags_tpl,3600);
        }

        
        $type = config('system_dirname');   //系统模块目录名
        
        echo self::pri_jsfile($pagename);                                                               //输出JS文件
        echo  self::pri_tag_div($tag_name,'showpage_set_'.$type,$tag_array);             //输出标签的操作层
        
        if($tag_array && trim($tag_array['view_tpl']!='')){         //数据库设定的模板优先
            $tpl = $tag_array['view_tpl'];
        }else{
            $tpl = $label_tags_tpl[$tag_name];
        }
        
        if($filtrate_field || $f_array){    //设置了循环不显示哪些字段  或者是指定了哪些字段
            $this->showpage_field($tpl , $info , $filtrate_field,$cfg['val'],$f_array);
        }else{
            $listdb = $info['picurls'];     //这样就可以调用通用标签的幻灯片模板了
            eval('?>'.$tpl);
        }
    }
    
    
    /**
     * 解释内容页的自定义字段
     * @param unknown $tplcode 原始模板
     * @param array $info 数据库取出的内容信息
     * @param string $field 过滤的字段
     * @param string $val 用户定义的循环变量名
     * @param array $f_array 指定了用户自定义的字段
     * @return string|mixed
     */
    private function showpage_field($tplcode,$_info=[],$field='',$val='info',$f_array=[]){
        $tplcode = $this->replace_field($tplcode);
        $filtrate_field = explode(',',$field);  //过滤的字段
        
        if(is_array($f_array)&&!empty($f_array)){
            $array = \app\common\field\Format::form_fields($f_array);  //把程序中定义的表单字段 转成跟数据库取出的格式一样
        }else{
            $array = get_field($_info['mid']);
        }
        
        $_val = [];
        foreach ($array AS $rs){
            if(in_array($rs['name'], $filtrate_field)){
                continue;
            }
            if($_info[$rs['name']]===''||$_info[$rs['name']]===null){
                continue;
            }
            $_val[] = array_merge($rs,
                    [
                            'title' => $rs['title'],
                            'value' => $_info[$rs['name']],
                    ]) ;
        }
        $$val = $_val;
        eval('?>'.$tplcode);
    }
    
    /**
     * 转义表单与内容页用户定义的字段
     * @param string $tplcode
     * @return mixed
     */
    protected function replace_field($tplcode=''){
        return str_replace(['{title}','{value}','{about}','{need}'], ['<?php echo $rs["title"]; ?>','<?php echo $rs["value"]; ?>','<?php echo $rs["about"]; ?>','<?php echo $rs["need"]; ?>'], $tplcode);
    }
    
    /**
     * 表单标签
     * @param string $tag_name
     * @param array $cfg
     */
    public function get_form_label($tag_name='',$cfg=[]){
        $f_array = $cfg['f_array'];     //程序中定义的字段
        $mod = $cfg['mod'];     //指定频道
        $field = $cfg['field'];     //过滤的字段
        $mid = $cfg['mid'] ? $cfg['mid'] : 1;       //哪个模型
        $info = $cfg['info'] ? $cfg['info'] : [];       //内容信息
        $page_demo_tpl_tags = self::get_page_demo_tpl($cfg['dirname']);
        $tplcode = $page_demo_tpl_tags[$tag_name]['tpl'] ;
        $tplcode = $this->replace_field($tplcode);
        $__LIST__ = $this->get_form_field($info,$mid,$field,$mod,$f_array);
        eval('?>'.$tplcode);
    }
    
    /**
     * 表单字段
     * @param array $info 信息内容
     * @param number $mid 模型ID
     * @param string $field 过滤的字段
     * @param string $mod 频道目录名
     * @param array $f_array 程序中定义的字段数组
     * @return string|mixed
     */
    private function get_form_field($info=[],$mid=0,$field='',$mod='',$f_array=[]){
        $filtrate_field = explode(',',$field);  //过滤的字段
        if(is_array($f_array)&&!empty($f_array)){
            $array = \app\common\field\Format::form_fields($f_array);  //把程序中定义的表单字段 转成跟数据库取出的格式一样
        }else{
            $array = get_field($mid,$mod);
        }
        $obj = new \app\common\field\Form;      //目标是把字段转成对应的各种输入样式
        $data = [];
        foreach ($array AS $rs){
            if(in_array($rs['name'], $filtrate_field)){
                continue;
            }
            $data[] = array_merge($rs,$obj->get_field($rs,$info));      //取得每一项表单的最终转义后的效果
        }
        return $data;
    }
    

    
    /**
     * 列表显示的字段,要过滤一些指定的字段
     * @param array $fields 某个模型的所有字段
     * @param string $filtrate 过滤循环显示的字段
     * @return unknown[]
     */
    private function list_show_field($fields=[],$filtrate=''){
        $detail = explode(',',$filtrate);
        $array = [];
        foreach($fields AS $key=>$rs){
            if(empty($rs['listshow'])){
                continue;   //后台没设置在列表显示
            }
            if(in_array($rs['name'], $detail)){
                continue;
            }
            $array[$rs['name']] = $rs;
        }
        return $array;
    }
    
    /**
     * 列表页标签模板
     * @param string $tag_name 标签名字
     * @param array $cfg 配置参数
     */
    public function listpage_label($tag_name='',$cfg=[]){
//         $pagename = md5( basename($cfg['dirname']) );
        $pagename = md5( $cfg['dirname'] );
        $cache_time = $cfg['cache_time'];
        $filtrate_field = $cfg['field'];    //要过滤循环显示的字段
        $cfg['page'] || $cfg['page']=input('get.page');
        //fid mid 不能用缓存
        $cfg_array =  [
                'name'=>$tag_name,
                'fid'=>$cfg['fid'],
                'mid'=>$cfg['mid'],
                'rows'=>$cfg['rows'],
                'order'=>$cfg['order'],
                'by'=>$cfg['by'],
                'where'=>$cfg['where'],
                'status'=>$cfg['status'],
                'pagename'=>$pagename,
                'cache_time'=>$cache_time,
        ];
        
        $filemtime = filemtime($cfg['dirname']);      //记录模板文件的修改时间，模板修改后，就取消缓存
        //获取页面的所有标签模板，一般只有一个。但也有可能会出现两个以上
        $label_tags_tpl = cache('tags_listpage_tpl_'.$pagename);

        $tag_array = cache('qb_tag_'.$tag_name);    //数据库参数配置文件，不包含有列表数据
        if(empty($tag_array) || $filemtime!=$label_tags_tpl['_filemtime_']){
            $tag_array = LabelModel::get_tag_data_cfg($tag_name , $pagename);
            $tag_array['cache_time']>0 && cache('qb_tag_'.$tag_name,$tag_array,$tag_array['cache_time']);
        }
        
        if(!empty($tag_array)){
            $_cfg = unserialize($tag_array['cfg']);
            is_array($_cfg) && $cfg_array = array_merge($cfg_array,$_cfg);    //数据库的参数配置优先级更高            
        }
        $cfg_array = array_merge($cfg_array, self::union_live_parameter($cfg));     //动态变量参数
        
        //列表页,给AJAX传输数据用
        self::$list_page_cfg = [$tag_name=> $cfg_array];    //列表页标签也可以存在多个的
        
        $tag_data = cache('qb_tag_data_'.$tag_name);    //首页列表数据缓存
        if($cfg['page']>1 || empty($tag_data) || $filemtime!=$label_tags_tpl['_filemtime_']){     //第2页以上不用缓存
            $tag_data = controller('content','index')->label_list_data($cfg_array);
            $tag_array['cache_time']>0 && cache('qb_tag_data_'.$tag_name,$tag_array,$tag_array['cache_time']);
        }
        
        if($filemtime!=$label_tags_tpl['_filemtime_']){
            $label_tags_tpl = self::get_listpage_tpl($cfg['dirname']);
            $label_tags_tpl['_filemtime_'] = $filemtime;
            //标签模板必须要缓存起来，提高效率，同时更为了方便AJAX或者后台调用，没入库前，AJAX必须要使用缓存模板
            cache('tags_listpage_tpl_'.$pagename,$label_tags_tpl,3600);
        }
        
        //$rows = $tag_array['rows']?$tag_array['rows']:$cfg['rows'];     //分页可能会用到
        
        $type = config('system_dirname');   //系统模块目录名

        echo self::pri_jsfile($pagename);                                                               //输出JS文件
        echo  self::pri_tag_div($tag_name,'listpage_set_'.$type,$tag_array);             //输出标签的操作层
        
        //指定了过滤字段,代表想要取某些字段的数值
        $fields = $filtrate_field ? $this->list_show_field( get_field($cfg['mid'],$type) , $filtrate_field ) : [];
        
        $__LIST__= $tag_data ? getArray($tag_data)['data'] : [];
        if(empty($__LIST__)){     //没数据
            return ;
        }else{
            if($tag_array && trim($tag_array['view_tpl']!='')){         //数据库设定的模板优先
                $tpl = $tag_array['view_tpl'];
            }else{
                $tpl = $label_tags_tpl[$tag_name];
            }            
            eval('?>'.$tpl);
            return [
                    'pages'=>$tag_data->render(),   //分页代码
                    'cfg'=>$cfg_array,  //显示更多分页可能会用到
            ];   
        }
    }
    
    /**
     * 通用标签的AJAX地址,给显示多页用
     * @param string $tag_name 标签名
     * @param string $dirname 目录名
     */
    public function get_ajax_url($tag_name='',$dirname=''){
//         $pagename = md5( basename($dirname) );
        $pagename = md5( $dirname );
        $array = self::tag_cfg_parameter($tag_name);
        echo  $this->build_tag_ajax_url(array_merge(
                [
                        'name'=>$tag_name,
                        'pagename'=>$pagename,
                ],
                self::union_live_parameter($array)
                )).'?page=';
    }
    
    /**
     * 生成通用标签的AJAX地址
     * @param array $array
     * @return mixed
     */
    private function build_tag_ajax_url($array=[]){
        return iurl('index/label_show/ajax_get',$array);
    }
    
    /**
     * 列表页的AJAX地址
     * @param string $tag_name 标签名
     */
    public function get_listpage_ajax_url($tag_name=''){
        $cfg = self::$list_page_cfg[$tag_name];
        if ($cfg['where']) {
            $cfg['where'] = mymd5($cfg['where']);   //避免用户恶意修改
        }
        $_getA = [];
        foreach($cfg AS $key=>$value){
            if ($value && !preg_match("/^([-\w]+)$/i", $value)) {
                $_getA[$key] = $value;
                unset($cfg[$key]);
            }
        }
        $_get = http_build_query($_getA);    //路由无法正确解释带有空格之类的内容,所以要改用GET
        echo  iurl(  config('system_dirname').'/content/ajax_get',  $cfg )."?$_get&page=";
    }

    /**
     * 获取列表页的标签页的模板
     * @param string $dirname 列表页绝对路径
     * @return mixed[]
     */
    private function get_listpage_tpl($dirname=''){
        $label_tags_tpl = [];
        preg_match_all("/<!--LISTPAGE(.*?)LISTPAGE-->/is",file_get_contents($dirname),$array);  //取得每一块标签数据
        foreach($array[1] AS $key=>$tag_code){
            preg_match_all("/<!--(.*?)-->/is",$tag_code,$array2);   //取得每一块标签数据的标签名与模板
            //$_label_tag_name = $array2[1][0];
            
            list($_label_tag_name,$type,$tpl) = explode("\t",$array2[1][0]);
            
            if(!empty($tpl)){   //指定模板
                $path = APP_PATH.'common/view/'.$tpl.'.htm';
                if(is_file($path)){
                    $_label_tag_tpl = read_file($path);
                }else{
                    $_label_tag_tpl = "标签名为：{$_label_tag_name} 指定的模板文件 {$tpl} 不存在";
                }
            }else{
                $_label_tag_tpl= str_replace(array('<!--LISTPAGE','LISTPAGE-->'),'',$array[0][$key]);
                $_label_tag_tpl= preg_replace("/<!--(.*?)-->/is",'',$_label_tag_tpl);  //把标签名的注释符去掉
            }
            
            $label_tags_tpl[$_label_tag_name] = $this->replace_str($_label_tag_tpl);
        }
        return $label_tags_tpl;
    }

    /**
     * 获取内容页的标签页的模板
     * @param string $dirname 列表页绝对路径
     * @return mixed[]
     */
    private function get_showpage_tpl($dirname=''){
        $label_tags_tpl = [];
        preg_match_all("/<!--SHOWPAGE(.*?)SHOWPAGE-->/is",file_get_contents($dirname),$array);  //取得每一块标签数据
        foreach($array[1] AS $key=>$tag_code){
            preg_match_all("/<!--(.*?)-->/is",$tag_code,$array2);   //取得每一块标签数据的标签名与模板
            //$_label_tag_name = $array2[1][0];
            
            list($_label_tag_name,$type,$tpl) = explode("\t",$array2[1][0]);
            
            if(!empty($tpl)){   //指定模板
                $path = APP_PATH.'common/view/'.$tpl.'.htm';
                if(is_file($path)){
                    $_label_tag_tpl = read_file($path);
                }else{
                    $_label_tag_tpl = "标签名为：{$_label_tag_name} 指定的模板文件 {$tpl} 不存在";
                }
            }else{
                $_label_tag_tpl= str_replace(array('<!--SHOWPAGE','SHOWPAGE-->'),'',$array[0][$key]);
                $_label_tag_tpl= preg_replace("/<!--(.*?)-->/is",'',$_label_tag_tpl);  //把标签名的注释符去掉
            }
            
            $label_tags_tpl[$_label_tag_name] = $this->replace_str($_label_tag_tpl);
        }
        return $label_tags_tpl;
    }
    
    /**
     * 获取评论的标签模板
     * @param string $dirname 页面的绝对路径
     * @return mixed[]
     */
    private function get_comment_tpl($dirname='',$aid,$sysid=0,$cfg=[]){
        $label_tags_tpl = [];
        preg_match_all("/<!--COMMENT(.*?)COMMENT-->/is",file_get_contents($dirname),$array);  //取得每一块标签数据
        foreach($array[1] AS $key=>$tag_code){
            preg_match_all("/<!--(.*?)-->/is",$tag_code,$array2);   //取得每一块标签数据的标签名与模板
            //$_label_tag_name = $array2[1][0];
            list($_label_tag_name,$type,$tpl) = explode("\t",$array2[1][0]);
            
            if(!empty($tpl)){   //指定模板
                $path = APP_PATH.'common/view/'.$tpl.'.htm';
                if(is_file($path)){
                    $_label_tag_tpl = read_file($path);
                }else{
                    $_label_tag_tpl = "标签名为：{$_label_tag_name} 指定的模板文件 {$tpl} 不存在";
                }
            }else{
                $_label_tag_tpl= str_replace(array('<!--COMMENT','COMMENT-->'),'',$array[0][$key]);
                $_label_tag_tpl= preg_replace("/<!--(.*?)-->/is",'',$_label_tag_tpl);  //把标签名的注释符去掉
                $_label_tag_tpl= str_replace(array('{posturl}','{pageurl}','{apiurl}'),array(
                        //fun('label@comment_api','posturl',$aid,$sysid,$cfg),
                        //fun('label@comment_api','pageurl',$aid,$sysid,$cfg),
                        '<?php echo fun("label@comment_api","posturl",$aid,$sysid,$cfg_array); ?>',
                        '<?php echo fun("label@comment_api","pageurl",$aid,$sysid,$cfg_array); ?>',
                        '<?php echo fun("label@comment_api","apiurl",$aid,$sysid,$cfg_array); ?>',
                         ),
                        $_label_tag_tpl);
            }

            $label_tags_tpl[$_label_tag_name] = $this->replace_str($_label_tag_tpl);
        }
        return $label_tags_tpl;
    }
    
    /**
     * 获取论坛回复的标签模板,跟上面的评论类似
     * @param string $dirname 页面的绝对路径
     * @return mixed[]
     */
    private function get_reply_tpl($dirname='',$aid,$sysid=0,$cfg=[]){
        $label_tags_tpl = [];
        preg_match_all("/<!--REPLY(.*?)REPLY-->/is",file_get_contents($dirname),$array);  //取得每一块标签数据
        foreach($array[1] AS $key=>$tag_code){
            preg_match_all("/<!--(.*?)-->/is",$tag_code,$array2);   //取得每一块标签数据的标签名与模板
            //$_label_tag_name = $array2[1][0];
            list($_label_tag_name,$type,$tpl) = explode("\t",$array2[1][0]);
            
            if(!empty($tpl)){   //指定模板
                $path = APP_PATH.'common/view/'.$tpl.'.htm';
                if(is_file($path)){
                    $_label_tag_tpl = read_file($path);
                }else{
                    $_label_tag_tpl = "标签名为：{$_label_tag_name} 指定的模板文件 {$tpl} 不存在";
                }
            }else{
                $_label_tag_tpl= str_replace(array('<!--REPLY','REPLY-->'),'',$array[0][$key]);
                $_label_tag_tpl= preg_replace("/<!--(.*?)-->/is",'',$_label_tag_tpl);  //把标签名的注释符去掉
                $_label_tag_tpl= str_replace(array('{posturl}','{pageurl}'),array(
                        //fun('label@comment_api','posturl',$aid,$sysid,$cfg),
                        //fun('label@comment_api','pageurl',$aid,$sysid,$cfg),
                        '<?php echo fun("label@reply_api","posturl",$aid,$sysid,$cfg_array); ?>',
                        '<?php echo fun("label@reply_api","pageurl",$aid,$sysid,$cfg_array); ?>',
                ),
                        $_label_tag_tpl);
            }
            
            $label_tags_tpl[$_label_tag_name] = $this->replace_str($_label_tag_tpl);
        }
        return $label_tags_tpl;
    }
    
    /**
     * 获取页面通用标签的模板及演示数据
     * @param string $dirname 列表页绝对路径
     * @return mixed[]
     */
    private function get_page_demo_tpl($dirname=''){
        static $template_codes = [];
        $template_code = $template_codes[$dirname];     //模板源代码
        if($template_code==''){
            $template_code = $template_codes[$dirname] = file_get_contents($dirname);
        }
        preg_match_all("/<!--QB(.*?)QB-->/is",$template_code,$array);  //取得每一块标签数据
        foreach($array[1] AS $key=>$tag_code){
            preg_match_all("/<!--(.*?)-->/is",$tag_code,$array2);   //取得每一块标签数据的标签名与模板代码
            list($_label_tag_name,$type,$tpl) = explode("\t",$array2[1][0]);    //标签变量名,取哪个类型的数据,指定公共模板文件路径
            $_label_tag_demo = $array2[1][1];
            
            if(!empty($tpl)){   //指定模板
                $path = APP_PATH.'common/view/'.$tpl.'.htm';
                if(is_file($path)){
                    $_label_tag_tpl = $_label_tag_demo = read_file($path);
                }else{
                    $_label_tag_tpl = $_label_tag_demo = "标签名为：{$_label_tag_name} 指定的模板文件 {$tpl} 不存在";
                }
            }else{      //把标签里边包含的代码当作模板处理
                $_label_tag_tpl = str_replace(array('<!--QB','QB-->'),'',$array[0][$key]);
                $_label_tag_tpl = preg_replace("/<!--(.*?)-->/is",'',$_label_tag_tpl);               //把标签名的注释符去掉
            }

            //文本域或单张图片特殊处理
            if($type=='text'||$type=='image'||$type=='txt'||$type=='textarea'||$type=='ueditor'){
                if($_label_tag_demo==''){
                    $_label_tag_demo=$_label_tag_tpl;
                }
            }elseif($type==''){     //针对懒人没有设置标签显示类型的情况，也把标签里的内容显示出来，但PHP代码就不要显示了
                if($_label_tag_demo==''){
                    $_label_tag_demo=preg_replace('/<\?php (.*?)\?>/is','',$_label_tag_tpl);
                }
            }else{
                //if(empty($val) && $_label_tag_tpl){    //标签模板缓存起来，方便AJAX或者后台调用
                    //cache('tags_tpl_code_'.$_label_tag_name,$_label_tag_tpl,3600);
                //}
            }
            $label_tags[$_label_tag_name]=[
                    'demo'=>$this->replace_str($_label_tag_demo),
                    'tpl'=>$this->replace_str($_label_tag_tpl),
            ];
        } 
        return $label_tags;
    }
    
    /**
     * 临时存放标签的各项配置参数,给更多分页get_ajax_url使用
     * @param string $tag_name 标签名
     * @param unknown $cfg 模板里边设置好的,各项配置参数
     * @return string
     */
    private static function tag_cfg_parameter($tag_name='',$cfg=null){
        static $tags_cfg = null;
        if($cfg!==null){
            $tags_cfg[$tag_name] = $cfg;
        }else{
            return $tags_cfg[$tag_name];
        }
    }
    
    /**
     * 关联动态变量
     * @param unknown $str
     * @return void|string 
     */
    private static function union_live_parameter($cfg=[]){
        $array = [];
        if($cfg['union']){
            $detail = explode(',',$cfg['union']);
            foreach($detail AS $v){
                $array[$v] = $cfg[$v];
            }
            $array['union'] = $cfg['union'];
        }
        return $array;
    }

    
    /**
     * 获取通用标签
     * @param unknown $tag_name
     * @param unknown $cfg
     */
    public function get_label($tag_name='',$cfg=[]){
        $filtrate_field = $cfg['field'];                                 //循环字段指定不显示哪些
        $val = $cfg['val'];                                                 //取得数据后，赋值到这个变量名, 分页的话,没做处理会得不到
        $list = $cfg['list'];                                                //foreach输出 AS 后面的变量名
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
        
        $tag_array = cache('qb_tag_'.$tag_name);        //取得具体某个标签的数据库配置参数，对于取文章列表的，也会同时得到相应的数据
        if(empty($tag_array)||$tpl_have_edit){
            $tag_array = LabelModel::get_tag_data_cfg($tag_name , $pagename , 1 , self::union_live_parameter($cfg) );
            $tag_array['cache_time']>0 && cache('qb_tag_'.$tag_name,$tag_array,$tag_array['cache_time']);
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
        
        echo self::pri_jsfile($pagename);                                   //输出JS文件
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
                eval('?>'.$tag_array['data']);
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
                return ;
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
    
    /**
     * 未入库前,标签默认指定的频道数据
     * @param string $type
     * @param unknown $cfg
     * @param unknown $onlyData 是否仅要内容数据,不要额外的
     * @return void|unknown
     */
    private function get_default_data($type='',$cfg,$page_num=1,$onlyData=true){
        static $class_array = [];   //同一个类就没必要重复实例化
        $action = '';
        if($cfg['class']){
            list($class_name,$_action) = explode('@',$cfg['class']);
            if (class_exists($class_name) && method_exists($class_name,$_action) ){
                $action = $_action;
            }
        }else{
            if( modules_config($type) ){
                $class_name = "app\\$type\\model\\Content";
            }else{
                $class_name = "plugins\\$type\\model\\Content";
            }
           
            if (class_exists($class_name) && method_exists($class_name,'labelGetList') ){
                $action = 'labelGetList';
            }
        }
        if ( empty($action) ){
            return ;
        }
        if(empty( $class_array[$class_name] )){
            $obj = new $class_name;
        }else{
            $obj = $class_array[$class_name];
        }
        $page_num<1 && $page_num = 1;
//         $config['cfg'] = serialize( ['rows'=>$cfg['rows']] );
        $config['cfg'] = serialize( $cfg );        
        $data = $obj->$action($config,$page_num);
        $data = getArray($data);
        if($onlyData){
            return is_array($data['data']) ? $data['data'] : $data; //不是数组的时候,就是那些单张图或HTML代码
        }else{
            return $data;
        }       
    }
    
    /**
     * 把模板中一些类似这样__STATIC__ 替换掉
     * @param unknown $string
     * @return mixed
     */
    private function replace_str($string){
        foreach (config('view_replace_str') AS $key=>$value){
            $string = str_replace($key, $value, $string);
        }
        return $string;
    }

    
    
    
    
}
