<?php

namespace app\common\controller;

use app\common\model\User AS UserModel;
use app\common\model\Config AS ConfigModel;
use think\Controller;

/**
 * 公用控制器 前后端继承 共用方法 写在此控制器中
 */
class Base extends Controller
{
    protected $webdb;
    protected $route;
    protected $onlineip;
	protected $user;   //用户登录后的信息
	protected $weburl; //当前网址
	protected $fromurl; //来源网址
	protected $map = [];
	protected $admin = false;
	
	protected function _initialize()
    {
        parent::_initialize();
        
        $this->onlineip = get_ip();
        $this->timestamp = time();
        $this->weburl = $this->request->url(true);
        $this->fromurl = $_SERVER["HTTP_REFERER"];
		$GLOBALS['FROMURL'] = $this->fromurl;
        $GLOBALS['WEBURL'] = $this->weburl;        
        
        //路由信息
        $dispatch = $this->request->dispatch();
        $this->route = $dispatch['module'];
        
        $this->webdb = config('webdb');
//         $this->webdb = cache('webdb');
//         if(empty($this->webdb)){
//             $this->webdb = ConfigModel::getConfig();
//             cache('webdb',$this->webdb);
//         }
//         $this->webdb['QB_VERSION'] = 'X1.0 Beta';
                
//         //把相应的插件或频道模块的二维数组插入到一维数组去使用
//         if($array['module'][1]=='plugin' && $array['module'][1]=='execute'){
//             $plugin_name = input('plugin_name');
//             if( $plugin_name && is_array( $this->webdb['P__'.$plugin_name] ) ){
//                 $this->webdb = array_merge(
//                         $this->webdb,
//                         $this->webdb['P__'.$plugin_name]
//                         );
//             }
//         }elseif( $array['module'][0] && $this->webdb['M__'.$array['module'][0]] ){
//             $this->webdb = array_merge(
//                     $this->webdb,
//                     $this->webdb['M__'.$array['module'][0]]
//                     );
//         }
//         config('webdb',$this->webdb);
        
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
            if ($this->request->isPost()) {
                $this->request->post(fun('filter@all',$this->request->post())); //安全过滤
            }
        }
        
    }


    
    protected function success($msg = '', $url = null, $data = '', $wait = 1, array $header = [])
    {
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
    public function showerr($msg = '', $url = null, $data = '', $wait = 60, array $header = []){
        $this->error($msg, $url, $data, $wait , $header);
    }
    
    protected function error($msg = '', $url = null, $data = '', $wait = 60, array $header = [])
    {
        if(strstr($msg,'没登录')||strstr($msg,'先登录')){
            $url = get_url('login') . '?fromurl=' . urlencode($this->weburl);
            $this->success($msg,$url,[],1);
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
        parent::error($msg, $url, $data, $wait, $header);
    }
    
    protected function ok_js($data=[],$msg='操作成功',$page_rows=0){
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
            $array = ['current_page'=>1,'last_page'=>1,'per_page'=>$page_rows,'current_page'=>1,'current_page'=>1];
            $array['total'] = $array['per_page'] = count($data);
        }
        
        $hasNext = false;
        $next = $array['current_page'];
        if($array['current_page']<$array['last_page']){
            $hasNext = true;
            $next++;
        }
        $array = [
                'code'=>0,
                'msg'=>$msg,
                'data'=>$data,
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

	protected function fetch($template = '', $vars = [], $replace = [], $config = [])
	{
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

	    if( defined('ENTRANCE')&&in_array(ENTRANCE,['index','member','admin']) ) {
            if($template=='' && $this->route[2]==''){
                $template='index';
            }
            $template = getTemplate($_template=$template);  //自动识别PC或WAP模板
            if(ENTRANCE=='member'&&input('uid')==''){
                $this->assign('uid',$this->user['uid']);    //默认把当前用户的UID放进模板,方便标签调用
            }
            if (empty($template)) {
                die('严重警告!!<br><br>当前模板文件不存在:<br><br>'.getTemplate($_template,false));
            }
	    }
        
        $_vars = [
                'admin'=>$this->admin,
                'userdb'=>$this->user,
                'timestamp'=>$this->timestamp,
                'webdb'=>$this->webdb,
                'index_style_layout'=>getTemplate('index@layout'),
                'member_style_layout'=>getTemplate('member@layout'),
                'site_defalut_html'=>getTemplate(config('site_defalut_html')),
                'simple_layout'=>getTemplate(config('simple_layout')),  //mui简单模板
        ];
        $vars = array_merge($_vars,$vars);
        return parent::fetch($template, $vars, $replace, $config);
    }

    //插件模板
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
        $search_field     = input('param.search_field/s', '');
        $keyword          = input('param.keyword/s', '');
        $filter           = input('param._filter/s', '');
        $filter_content   = input('param._filter_content/s', '');
        $filter_time      = input('param._filter_time/s', '');
        $filter_time_from = input('param._filter_time_from/s', '');
        $filter_time_to   = input('param._filter_time_to/s', '');
        $select_field     = input('param._select_field/s', '');
        $select_value     = input('param._select_value/s', '');

        $map = [];

        // 搜索框搜索
        if ($search_field != '' && $keyword !== '') {
            $map[$search_field] = in_array($search_field, ['id','uid']) ? ['=', $keyword] : ['like', "%$keyword%"];
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

        // 时间段搜索
//         if ($filter_time != '' && $filter_time_from != '' && $filter_time_to != '') {
//             $map[$filter_time] = ['between time', [$filter_time_from.' 00:00:00', $filter_time_to.' 23:59:59']];
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
        if(ENTRANCE!='admin'){
            //return '';
        }
        $order = input('param._order/s', '');
        $by    = input('param._by/s', '');        
        if ($order != '' && $by != '') {
            return $order . ' ' . $by;
        }elseif ($extra_order != '') {
            return $extra_order;
        }
    }
}
