<?php

namespace app\common\controller;

use app\common\model\User AS UserModel;
use think\Controller;

/**
 * 公用控制器 前后端继承 共用方法 写在此控制器中
 */
class Base extends Controller
{
    protected $webdb;
    protected $route;
    protected $onlineip;
    protected $user;              //用户登录后的信息
    protected $weburl;          //当前网址
    protected $fromurl;         //来源网址
    protected $guest;            //游客身份标志
    protected $index_style_layout; //可以自由定义前台布局模板
    protected $map = [];
    protected $admin = false;
    
    protected function _initialize()
    {
        parent::_initialize();
        
        $this->onlineip = $this->request->ip();
        $this->timestamp =$this->request->time();
        $this->weburl = filtrate($this->request->url(true));
        $this->fromurl = filtrate($_SERVER["HTTP_REFERER"]);
        $this->guest = md5($_SERVER['HTTP_USER_AGENT'].$this->onlineip);
        $GLOBALS['FROMURL'] = $this->fromurl;
        $GLOBALS['WEBURL'] = $this->weburl;
        
        //路由信息
        $dispatch = $this->request->dispatch();
        $this->route = $dispatch['module'];
        
        $this->webdb = config('webdb');
        
        //用户登录信息，如果没登录的话，就为空值
        $this->user = UserModel::login_info();
        
        //进入或退出标签管理
        if($this->user['groupid']==3){
            $this->admin = true ;   //前台管理员
            if(input('get.label_set')!=''){
                set_cookie('label_set', input('get.label_set'));
            }
            if(input('get.label_set')=='set' || get_cookie('label_set')=='set'){
                define('LABEL_SET', true);
            }
        }
        
        if($this->user['groupid']!=3 && in_array(ENTRANCE, ['member','index'])){
            
            if ($this->webdb['must_yz_phone'] && $this->user && empty($this->user['mob_yz']) ) {
                if( !(($this->route[0]=='member'&&$this->route[1]=='yz') || ($this->route[0]=='index'&&$this->route[1]=='login')) ){
                    $this->error('请先验证手机号，才能进行其它操作!',murl('member/yz/mob'));
                }
            }
            
            if ($this->request->isPost()) {
                $this->request->post(fun('filter@all',$this->request->post())); //安全过滤
            }
            
            fun('filter@check_safe'); //禁止提交eval <?php
            
            fun('filter@attack_visit'); //防灌水发贴
        }
        
        
        if(isset($_GET['hide_footmenu'])){
            set_cookie('hide_footmenu',intval($_GET['hide_footmenu']));    //隐藏底部菜单
        }
        if(isset($_GET['hide_member'])){
            set_cookie('hide_member',filtrate($_GET['hide_member']));    //禁用会员中心
        }
        if(isset($_GET['login_token'])){
            set_cookie('login_token',$_GET['login_token']);            
        }
        if(get_cookie('login_token')){
            cache(get_cookie('login_token'),get_ip()."\t".$this->user['uid']."\t".get_cookie('sockpuppet_uid'),600);    //小程序同步登录
        }
    }
    
    
    
    protected function success($msg = '', $url = null, $data = '', $wait = 1, array $header = [])
    {
        if (!$this->request->isAjax() && (isset($_SERVER['HTTP_TOKEN'])||$this->request->header('token')) ) {
            $this->request->post(['_ajax'=>1]);
            $this->request->get(['_ajax'=>1]);
        }
        if($url!==null && $this->route[1]=='plugin' && $this->route[2]=='execute'){
            if(strpos($url,'/')!==0 && strpos($url,'http')!==0 ){
                $url = purl($url);
            }
        }
        //$template = getTemplate(APP_PATH.'index/view/default/success.' . ltrim(config('template.view_suffix'), '.'));
        $template = getTemplate('index@success');
        if(empty($template)&&config('template.index_style')!='default'){  //寻找默认default模板
            config('template.index_style','default');
            $template = getTemplate('index@success');
        }
        
        if (!empty($template)) {
            config('dispatch_success_tmpl',$template);
        }
//         header('Access-Control-Allow-Origin: *');
//         header("Access-Control-Allow-Credentials: true");
//         header("Access-Control-Allow-Headers: *");
//         header("Access-Control-Expose-Headers:*");
//         header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        parent::success($msg, $url, $data, $wait, $header);
    }
    
    /**
     * 给showerr函数使用
     * @param string $msg
     * @param unknown $url
     * @param string $data
     * @param number $wait
     * @param array $header
     */
    public function showerr($msg = '', $url = null, $data = '', $wait = 60, array $header = [], $code = 1){
        $this->error($msg, $url, $data, $wait , $header, $code);
    }
    
    protected function error($msg = '', $url = null, $data = '', $wait = 60, array $header = [], $code = 1)
    {
        if (!$this->request->isAjax() && (isset($_SERVER['HTTP_TOKEN'])||$this->request->header('token')) ) {
            $this->request->post(['_ajax'=>1]);
            $this->request->get(['_ajax'=>1]);
        }
        if ($url==404) {
            $this->assign('msg', $msg);
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            echo $this->fetch('index@404');
            //echo '404 Not Found';
            exit;
        }
        if (strstr($msg,'没登录') ||strstr($msg,'没有登录') || strstr($msg,'先登录')) {
            if(!$this->request->isAjax() ){
                if (config('webdb.autologin_in_weixin')==1 && in_weixin()) {  //在微信端,就强制自动登录!
                    if( config('webdb.weixin_type')==3 || (in_wxapp()&&config('webdb.wxapp_appid')&&config('webdb.wxapp_appsecret')) ){
                        weixin_login();
                    }
                }
                $url = get_url('login');// . '?fromurl=' . urlencode($this->weburl);
                $this->success($msg,$url,[],1);
            }
            $code = 500;
        }
        
        
        //         $template = getTemplate(APP_PATH.'index/view/default/error.' . ltrim(config('template.view_suffix'), '.'));
        $template = getTemplate('index@error');
        if(empty($template)&&config('template.index_style')!='default'){  //寻找默认default模板
            config('template.index_style','default');
            $template = getTemplate('index@error');
        }
        if (!empty($template)) {
            config('dispatch_error_tmpl',$template);
        }
        $this->assign('userdb', $this->user);
        $this->assign('webdb', $this->webdb);
//         header('Access-Control-Allow-Origin: *');
//         header("Access-Control-Allow-Credentials: true");
//         header("Access-Control-Allow-Headers: *");
//         header("Access-Control-Expose-Headers:*");
//         header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        parent::error($msg, $url, $data, $wait, $header, $code);
    }
    
    protected function ok_js($data=[],$msg='操作成功',$code=0,$must_json=null){
//         header('Access-Control-Allow-Origin: *');
//         header("Access-Control-Allow-Credentials: true");
//         header("Access-Control-Allow-Headers: *");
//         header("Access-Control-Expose-Headers:*");
//         header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        if (defined('JSON_ARRAY')&&!$must_json) {
            return $data;
        }
        if(is_string($data)||is_numeric($data)){
            if(input('debug')){ //调试,查看原始数据
                return $data;
            }
            return json(['code'=>0,
                'msg'=>$msg,
                'data'=>$data,
            ]);
        }
        $array = $data = getArray($data);
        if(isset($array['data']) && isset($array['total'])){
            $data = $array['data'];
        }elseif( isset($array['data']) && empty($array['data'])){
            $data = '';
        }else{
            $array = ['current_page'=>1,'last_page'=>1,'current_page'=>1,'current_page'=>1];
            $array['total'] = $array['per_page'] = count($data);
        }
        
        $hasNext = false;
        $next = $array['current_page'];
        if($array['current_page']<$array['last_page']){
            $hasNext = true;
            $next++;
        }
        $_array = [];
        if (is_array($array) ) { //主要是服务于标签.因为标签中data全是html网页代码字符串,导致不能传递更多的数据
            $_array = $array;
            unset($_array['data'],$_array['page'],$_array['pages'],$_array['perPage'],$_array['total'],$_array['prev'],$_array['next'],$_array['hasNext'],$_array['hasPrev'],$_array['per_page'],$_array['current_page'],$_array['last_page']);
        }
        $array = [
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data,
            'ext'=>$_array,
            'paginate'=>[
                'page'=> empty($array['data']) ? input('page') : $array['current_page'],         //当前页码, 要特别注意,系统分页函数,当数据不存在的时候,不会显示真实页码
                'pages' => $array['last_page'],           //总页数
                'perPage' => $array['per_page'],        //每页几条
                'total' => $array['total'],                     //总共几条
                'prev' => $array['current_page'],        //上一页的页码
                'next' => $next,                                 //下一页的页码
                'hasNext' =>$hasNext,                       //下一页是否存在
                'hasPrev' =>$array['current_page']>1?true:false,     //上一页是否存在
            ],
        ];
        if(input('debug')){ //调试,查看原始数据
            print_r($array) ;
            return ;
        }
        return json($array);
    }
    
    protected function err_js($msg='操作失败',$data=[],$code=1){
//         header('Access-Control-Allow-Origin: *');
//         header("Access-Control-Allow-Credentials: true");
//         header("Access-Control-Allow-Headers: *");
//         header("Access-Control-Expose-Headers:*");
//         header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        if (strstr($msg,'没登录') ||strstr($msg,'没有登录') || strstr($msg,'先登录')) {
            $code = 500;
        }
        $array = [
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data,
        ];
        if(input('debug')){ //调试,查看原始数据
            print_r($array) ;
            return ;
        }
        return json($array);
    }
    
    /**
     * 模板变量赋值
     * @access protected
     * @param  mixed $name  要显示的模板变量
     * @param  mixed $value 变量的值
     * @return $this
     */
    protected function assign($name, $value = '')
    {
        if (function_exists('val')) {
            //碎片模板要用到
            $array = val('','template')?:[];
            $array = array_merge($array,is_array($name)?$name:[$name=>$value]);
            val($array,'template');
        }
        
        return parent::assign($name, $value);
    }
    
    protected function format_json_data($array = []){
        if(is_object($array)){
            $array = getArray($array);
        }
        foreach($array AS $key=>$rs){
            if(is_object($rs)){
                $rs = $key=='listdb'?getArray($rs):'';
            }
            
            if(is_array($rs)){
                $rs = $this->format_json_data($rs);
            }elseif( isset($array['uid']) && $array['uid']!=$this->user['uid'] ){
                if($key!==0&&in_array($key, ['sncode','password'])){
                    $rs = '';
                }elseif(isset($array['password_rand'])){
                    $rs = '';
                }
                
            }
            $array[$key] = $rs;
        }
        return $array;
    }
    
    /**
     * APP接口
     * @return array[]|NULL[][]|unknown[]|NULL[]|boolean
     */
    protected function fetch_begin(){
        if ( ENTRANCE!='index' && ($this->request->header('token') || $this->request->isAjax()) ) {
            $array = val('','template');
            if($array['listdb'] || $array['info']){
                return $this->format_json_data($array['listdb']?:$array['info']);
            }
        }
        return true;
    }
    
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        if (!defined('IN_TEMPLATE')) {
            define('IN_TEMPLATE',true);
        }
        if (function_exists('val')) {
            //碎片模板要用到
            $array = val('','template')?:[];
            $array = array_merge($array,$vars);
            val($array,'template');
            $result = $this->fetch_begin();
            if($result!==true){
                return $this->ok_js($result);
            }
        }
        
        if(input('route.token')||input('get.token')){    //避免用户分享链接的时候。把token一起分享出去
            $url = preg_replace_callback('/(\?|&|\/)token(=|\/)([\w]+)/i', function($array){
                if($array[1]=='?'){
                    return $array[1];
                }
            }, $this->weburl);
            header("location:".$url);
            exit;
        }
        
        //自定义模板
        if ( !strstr($template,substr(ROOT_PATH,0,-2))  ) {
            if ($this->route[1]=='plugin' && $this->route[2]=='execute') {
                $tp_module = input('param.plugin_name');
                $tp_controller = input('param.plugin_controller');
                $tp_action = input('param.plugin_action');
            }else{
                $tp_module = $this->route[0];
                $tp_controller = $this->route[1]?:'index';
                $tp_action = $this->route[2]?:'index';
            }
            $tp_name = (defined('IN_WAP')&&IN_WAP===true)?'wap_':'pc_';
            $tp_name .= defined('ENTRANCE') ? ENTRANCE.'_' : 'other_';
            $tp_name .= $tp_module.'_'.$tp_controller.'_'.$tp_action;
            if (($this->admin||strstr($tp_controller,'login')||strstr($tp_controller,'reg')) && input('get.get_template_name')==1) {
                die('当前页面的自定义模板变量名如下<br>'.$tp_name.'<br>'.str_replace('pc_', 'wap_', $tp_name));
            }
            if ($this->webdb[$tp_name]) {
                if (is_file(TEMPLATE_PATH.$this->webdb[$tp_name])) {
                    $template = TEMPLATE_PATH.$this->webdb[$tp_name];
                }elseif(is_file(ROOT_PATH.$this->webdb[$tp_name])){
                    $template = ROOT_PATH.$this->webdb[$tp_name];
                }
            }
        }
        
        if($this->route[1]=='plugin' && $this->route[2]=='execute' && !strstr($template,substr(ROOT_PATH,0,-2))){
            $plugin_name = input('param.plugin_name');
            $plugin_controller = input('param.plugin_controller');
            $plugin_action = input('param.plugin_action');
            $template = $template == '' ? $plugin_action : $template;
            if (config('template.view_base')) {
            }else{
                if(!is_file($template)){
                    if(ENTRANCE === 'admin') {
                        $template = ROOT_PATH. "plugins/{$plugin_name}/view/admin/{$plugin_controller}/{$template}.".config('template.view_suffix');
                    }elseif(ENTRANCE === 'member') {
                        $template = ROOT_PATH. "plugins/{$plugin_name}/view/member/default/{$plugin_controller}/{$template}.".config('template.view_suffix');
                    }elseif(ENTRANCE === 'index') {
                        $template = ROOT_PATH. "plugins/{$plugin_name}/view/index/default/{$plugin_controller}/{$template}.".config('template.view_suffix');
                    }
                }
            }
        }
        
        ENTRANCE=='index' && $template = $this->get_module_style_template($template);   //频道个性模板的查找
        
        if( defined('ENTRANCE')&&in_array(ENTRANCE,['index','member','admin']) ) {
            if($template=='' && $this->route[2]==''){
                $template='index';
            }
            $template = getTemplate($_template=$template);  //自动识别PC或WAP模板
            if(ENTRANCE=='member'&&input('uid')==''){
                $this->assign('uid',$this->user['uid']);    //默认把当前用户的UID放进模板,方便标签调用
            }
            if (empty($template)) {
                $template = getTemplate($_template,false);
                if (!$this->admin) {
                    $template = str_replace(ROOT_PATH, '/', $template);
                }
                header('HTTP/1.1 404 Not Found');
                header("status: 404 Not Found");
                die('严重错误提示!!<br><br>当前模板文件不存在:<br><br>'.$template);
            }
        }
        if (empty($this->index_style_layout)) {
            $this->get_module_layout('default');   //频道默认个性布局模板,优化级低于指定的index show list布局模板
        }
        
        $_vars = [
            'admin'=>$this->admin,
            'userdb'=>$this->user,
            'timestamp'=>$this->timestamp,
            'webdb'=>$this->webdb,
            'index_style_layout'=>$this->index_style_layout?:getTemplate('index@layout'),
            'member_style_layout'=>getTemplate('member@layout'),
            'site_defalut_html'=>getTemplate(config('site_defalut_html')),
            'simple_layout'=>getTemplate(config('simple_layout')),  //mui简单模板
        ];
        $vars = array_merge($_vars,$vars);
        return parent::fetch($template, $vars, $replace, $config);
    }
    
    /**
     * 如果频道设置了个性风格布局模板后，自动去查找此模板里对应的内容主体模板
     * @param string $template
     */
    protected function get_module_style_template($template=''){
        if (empty(config('system_dirname')) || ($template && is_file($template)) ) {
            return $template;
        }
        $layout = $this->get_module_layout('default');
        if ($layout){
            $tpl = $template?:$this->request->action();
            $tpl_name = basename($tpl).'.'.config('template.view_suffix');
            $tpl_path = dirname($tpl);
            $base_path = dirname(dirname($layout)) . '/' . config('system_dirname') . '/' . strtolower($this->request->controller()) . '/' . ( $tpl_path=='.' ? '' : $tpl_path . '/' );
            if(IN_WAP===true){
                if(is_file($base_path.'wap_'.$tpl_name)){
                    return $base_path.'wap_'.$tpl_name;
                }
            }else{
                if(is_file($base_path.'pc_'.$tpl_name)){
                    return $base_path.'pc_'.$tpl_name;
                }
            }
            if(is_file($base_path.$tpl_name)){
                return $base_path.$tpl_name;
            }
        }
        return $template;
    }
    
    /**
     * 设置频道的布局模板
     * @param string $type 主要是4个参数 show list index default 分别代表: 内容页 列表页 频道主页 频道默认个性
     * default频道默认个性布局模板,优化级低于指定的index show list布局模板
     * @return string
     */
    protected function get_module_layout($type='show'){
        if(IN_WAP===true){
            if( $this->webdb['module_wap_'.$type.'_layout']!='' ){
                $template = TEMPLATE_PATH.'index_style/'.$this->webdb['module_wap_'.$type.'_layout'];
            }
        }else{
            if( $this->webdb['module_pc_'.$type.'_layout']!='' ){
                $template = TEMPLATE_PATH.'index_style/'.$this->webdb['module_pc_'.$type.'_layout'];
            }
        }
        if($template!=''&&is_file($template)){
            $this->index_style_layout = $template;
            return $template;
        }
    }
    
    
    /**
     * 插件模板
     * @param string $template
     * @param array $vars
     * @param array $replace
     * @param array $config
     * @return mixed|string
     */
    protected function pfetch($template = '',  $vars = [], $replace = [], $config = [])
    {
        $plugin_name = input('param.plugin_name');
        $plugin_controller = input('param.plugin_controller');
        $plugin_action = input('param.plugin_action');
        
        $template = $template == '' ? $plugin_action : $template;
        //         if(!is_file($template)){
        //             if(ENTRANCE === 'admin') {
        //                 $template_path = ROOT_PATH. "plugins/{$plugin_name}/view/admin/{$plugin_controller}/{$template}.".config('template.view_suffix');
        //             }elseif(ENTRANCE === 'member') {
        //                 $template_path = ROOT_PATH. "plugins/{$plugin_name}/view/member/default/{$plugin_controller}/{$template}.".config('template.view_suffix');
        //             }
        //         }
        //         return self::fetch($template_path, $vars, $replace, $config);
        return self::fetch($template, $vars, $replace, $config);
    }
    
    protected function getMap()
    {
        if(ENTRANCE!='admin'){
            return [];
        }
        $search_fields    = input()['search_fields'];
        $search_field     = input('param.search_field/s', '');
        $keyword          = input('param.keyword/s', '');
        $search_status    = input('param.search_status/s', '');
        $search_fid       = input('param.search_fid/s', '');
        $timefield        = input('param.search_timefield/s', 'create_time');
        $search_begintime = input('param.search_begintime/s', '');
        $search_endtime   = input('param.search_endtime/s', '');
        $select_field     = input('param._select_field/s', '');
        $select_value     = input('param._select_value/s', '');
        
        $map = [];
        
        // 搜索框搜索
        if ($search_field !== '' && $keyword !== '') {
            if (in_array($search_field, ['id','uid']) || (is_numeric($keyword)&&$keyword<999999)) {
                $map[$search_field] = ['=', $keyword];
            }else{
                $keyword = str_replace("\\", "\\\\", $keyword);
                $map[$search_field] = ['like', "%$keyword%"];
            }
        }
        
        if (is_array($search_fields)) { //列表筛选
            foreach($search_fields AS $key=>$value){
                if ($value===''||$value===null) {
                    continue;
                }
                //if (in_array($key, ['id','uid']) || (is_numeric($value)&&$value<999999)) {
                    $map[$key] = ['=', $value];
                //}else{
                //    $map[$key] = ['like', "%$value%"];
                //}
            }
        }
        
        //状态搜索
        if ($search_status!=='') {
            $map['status'] = $search_status;
        }
        if ($search_fid) {
            $map['fid'] = config('post_need_sort')?['in',array_values(get_sort($search_fid,'sons'))]:$search_fid;
        }
        
        //时间段搜索
        if ($search_begintime != '' && $search_endtime != '') {
            $map[$timefield] = [
                ['>',strtotime($search_begintime)],
                ['<',strtotime($search_endtime)],
                'and'
            ];
        }elseif ($search_begintime != '') {
            $map[$timefield] = ['>',strtotime($search_begintime)];
        }elseif ($search_endtime != '') {
            $map[$timefield] = ['<',strtotime($search_endtime)];
        }
        
        // 下拉筛选
        //         if ($select_field != '') {
        //             $select_field = array_filter(explode('|', $select_field), 'strlen');
        //             $select_value = array_filter(explode('|', $select_value), 'strlen');
        //             foreach ($select_field as $key => $item) {
        //                 if ($select_value[$key] != '_all') {
        //                     $map[$item] = $select_value[$key];
        //                 }
        //             }
        //         }
        
        
        
        // 表头筛选
        //         if ($filter != '') {
        //             $filter         = array_filter(explode('|', $filter), 'strlen');
        //             $filter_content = array_filter(explode('|', $filter_content), 'strlen');
        //             foreach ($filter as $key => $item) {
        //                 if (isset($filter_content[$key])) {
        //                     $map[$item] = ['in', $filter_content[$key]];
        //                 }
        //             }
        //         }
        return  array_merge( $map , $this->map);
    }
        
        /**
         * 获取字段排序
         * @param string $extra_order 默认排序
         * @return string
         */
        protected function getOrder($extra_order = '')
        {
            $order = input('param._order/s', '');
            $by    = input('param._by/s', '');
            if(ENTRANCE!='admin'&&$order!='id'&&!in_array($by, ['asc','desc'])){
                return '';
            }
            if ($order != '' && $by != '') {
                return $order . ' ' . $by;
            }elseif ($extra_order != '') {
                return $extra_order;
            }
        }
        
        /**
         * 齐博首创 钩子文件扩展接口
         * 详细使用教程 https://www.kancloud.cn/php168/x1_of_qibo/1010065
         * @param string $type 钩子标志,不能重复
         * @param array $data POST表单数据 可以改变其值
         * @param array $info 数据库资料
         * @param array $array 其它参数
         * @param string $use_common 默认同时调用全站通用的
         * @return unknown|NULL
         */
        protected function get_hook($type='',&$data=[],$info=[],$array=[],$use_common=true){
            $path_array = [];
            preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$carray);
            $dirname = $carray[0][1];
            $path_array[] = (defined('IN_PLUGIN')?PLUGINS_PATH:APP_PATH).$dirname.DS.'ext'.DS.$type.DS;
            if ($use_common===true) {
                $path_array[] = APP_PATH.'common'.DS.'ext'.DS.$type.DS;
            }
            $file_array = [];
            foreach ($path_array AS $path){
                if (is_dir($path)) {
                    $sarray = [];
                    $dir = opendir($path);
                    while($file = readdir($dir)){
                        if(preg_match("/^([\w\.-]*)\.php$/i", $file,$sar)){
                            if (in_array($sar[1], $file_array)) {
                                continue ; //出现同名,就跳过
                            }
                            $sarray[$path.DS.$file] = $sar[1];
                        }
                    }
                    asort($sarray);
                    $file_array = array_merge($file_array,$sarray);
                }
            }
            
            if ($file_array) {
                foreach($file_array AS $file=>$v){
                    $result = include($file);
                    if ($result===true||$result===false) {
                        return $result;
                    }elseif(is_string($result) || is_array($result)){
                        return $result;
                    }
                }
            }
            
            return NULL;
        }
        
        
        /**
         * 导出excel数据
         * 具体使用教程，请参考示例 \plugins\comment\admin\Content.php 及 \application\common\controller\admin\Order.php 及 \application\admin\controller\Member.php中的方法excel()
         * @param array|object $data 
         * @param array $field_array
         * @param number $rows
         */
        protected function bak_excel($data,$field_array,$rows=0){
            $totalpage = '';
            if(is_array($data)){
                $listdb = $data;
            }else{
                $array = getArray($data);
                $listdb = $array['data'];
                $totalpage = $array['last_page'];
                $rows = $array['per_page'];
            }
            $path = RUNTIME_PATH.'mysql_bak/'.substr(md5($this->user['password'].$this->user['uid']), 0,10).'/';
            $page = input('page')?:1;
            if ($page==1) {
                delete_dir($path);
            }
            makepath($path);
            if(!$listdb){
                if ($page==1) {
                    $this->error('没有数据可导出！');
                }elseif($page==2){
                    ob_end_clean();
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s',time()).' GMT');
                    header('Pragma: no-cache');
                    header('Content-Encoding: none');
                    header('Content-Disposition: attachment; filename=MicrosoftExce.xls');
                    header('Content-type: text/csv');
                    echo file_get_contents($path.'1.xls');
                    exit;
                }else{
                    $this->php_zip = new \app\common\util\Zip;
                    $tempzip = RUNTIME_PATH."MicrosoftExcel.zip";      //临时文件
                    
                    if($this->php_zip->run($tempzip,dirname($path).'/',basename($path))){
                        //ob_end_clean();
                        set_time_limit(0);
                        header('Last-Modified: '.gmdate('D, d M Y H:i:s',time()).' GMT');
                        header('Pragma: no-cache');
                        header('Content-Encoding: none');
                        header('Content-Disposition: attachment; filename='.basename($tempzip));
                        header('Content-type: .zip');
                        header('Content-Length: '.filesize($tempzip));
                        readfile($tempzip);
                        unlink($tempzip);
                        delete_dir($path);
                        exit;
                    }else{
                        $this->error('打包失败') ;
                    }
                }
            }
            $outstr="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"5\"><tr>";
            $field_array = ['i'=>'序号']+$field_array;
            foreach($field_array AS $rs){
                $outstr.="<th bgcolor=\"#A5A0DE\">".(is_array($rs)?$rs['title']:$rs)."</th>";
            }
            $outstr.="</tr>";
            
            $min = ($page-1)*$rows;
            $i = $min;
            foreach($listdb AS $rs){
                $i++;
                $outstr.="<tr>";
                foreach($field_array AS $k=>$ar){
                    $value = $rs[$k];
                    if (is_array($ar)) {
                        if($ar['key']){
                            $value = $rs[$ar['key']];
                        }
                        if(is_array($ar['opt'])){
                            $value = $ar['opt'][$value];
                        }elseif($ar['callback']){
                            $value = $ar['callback']($value,$rs);
                        }elseif($ar['type']=='username'){
                            $value = get_user_name($value);
                        }elseif($ar['type']=='time'){
                            $value = $value?format_time($value,'Y-m-d H:i'):'';
                        }
                    }elseif($k=='i'){
                        $value = $i;
                    }elseif(strstr($value,'<')||strstr($value,'>')){
                        $value = filtrate(del_html($value));
                    }
                    if( preg_match('/^[\d]{10,}$/', $value) ){
                        $value = '&nbsp;'.$value.'&nbsp;';
                    }
                    $outstr.="<td align=\"center\">{$value}</td>";
                }
                $outstr.="</tr>\n";
            }
            $outstr.="</table>";
            if ($totalpage==1) {
                ob_end_clean();
                header('Last-Modified: '.gmdate('D, d M Y H:i:s',time()).' GMT');
                header('Pragma: no-cache');
                header('Content-Encoding: none');
                header('Content-Disposition: attachment; filename=MicrosoftExcel.xls');
                header('Content-type: text/csv');
                echo $outstr;
                exit;
            }
            $filename = $path.$page.'.xls';
            file_put_contents($filename, '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>'.$outstr.'</body></html>');
            $page++;
            $url = preg_replace("/(\?|&)page=([\d]+)/", '', $this->weburl);
            $url .= (strstr($url,'?')?'&':'?') . 'page='.$page;
            echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL={$url}'>正在备份第 {$page} 卷,总共 {$totalpage} 卷";
            exit;
        }
        
}
    