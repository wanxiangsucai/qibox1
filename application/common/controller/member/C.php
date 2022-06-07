<?php
namespace app\common\controller\member;

use app\common\controller\MemberBase;
use app\common\traits\ModuleContent;
use think\Db;

/**
 * 
 * 会员中心用到的内容管理相关功能
 *
 */
abstract class C extends MemberBase
{
    use ModuleContent;
    
    protected $validate = 'Content';
    protected $model;
    protected $m_model;
    protected $f_model;
    protected $s_model;
    protected $form_items;
    protected $list_items;
    protected $tab_ext;
    protected $mid;
    protected $qun_power_list = [
        ['add','仅限哪些用户组有权发布','不设置都有权限'],
        ['edit','修改它人权限的用户组','不设置都没权限'],
        ['delete','删除它人权限的用户组','不设置都没权限'],
        ['order','管理订单权限的用户组','不设置都没权限'],
    ];
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'content');
        $this->s_model     = get_model_class($dirname,'sort');
        $this->m_model   = get_model_class($dirname,'module');
        $this->category_model     = get_model_class($dirname,'category');
        $this->info_model     = get_model_class($dirname,'info');
        $this->f_model     = get_model_class($dirname,'field');
        if(config('qun_power_list')){
            $this->qun_power_list = config('qun_power_list');
        }
    }
    
    /**
     * 把所有模型的一起列出来
     * @return mixed|string
     */
    public function listall($rows=0){
        if (!$rows) {
            $rows = input('rows')?:10;
        }
        $this->tab_ext['top_button'] = $this->page_top_botton();
        $this->tab_ext['page_title'] || $this->tab_ext['page_title'] = M('name').' 我发布的内容';
        $map = $this->get_map();
        foreach ($map AS $key=>$rs){
            if(!in_array($key, ['id','uid','mid','fid','status','view','list','create_time','ext_id','ext_sys','title'])){
                unset($map[$key]);
            }
        }
        $listdb = $this->model->getListByUid($this->user['uid'],$rows,$pages=[],$map);
        $pages = $listdb->render();
        $this->assign('listdb',$listdb);
        $this->assign('pages',$pages);
        $this->assign('tab_ext',$this->tab_ext);
        return $this->fetch();
    }
    
    /**
     * 设置圈子会员组在当前圈子发布内容的权限，及修改与删除的管理权限
     * @param number $qid 指定圈子ID
     * @return mixed|string
     */
    public function setpower($qid=0){
        if(!$qid && get_wxappAppid()){
            $app_cfg = wxapp_open_cfg( get_wxappAppid() ) ?: [];
            $qid = $app_cfg['aid'];
        }
        if (!$qid) {
            $quns = fun('qun@getByuid',$this->user['uid']);
            if (count($quns)>1) {
                $url = url('qun/choose/index').'?url='.urlencode( urls('setpower').'?qid=' );
                $this->redirect($url);
            }
        }
        if (!$qid) {
            showerr('圈子ID不存在！');
        }
        if ($this->user['qun_group'][$qid]['type']!=3) {
            showerr('你无权设置！');
        }
        $info = [];
        $array = Db::name('qun_power')->where([
            'qid'=>$qid,
            'sysname'=>config('system_dirname')
        ])->column('type,id,groups');
        foreach ($array AS $rs){
            $info[$rs['type']] = $rs['groups'];
        }
        if ($this->request->isPost()) {
            $data = $this->request->post();
            for($i=0;$i<count($this->qun_power_list);$i++){
                $key = $this->qun_power_list[$i][0];
                $groups = $data[$key] ? implode(',', $data[$key]) : '';
                if(!$array[$key]){
                    Db::name('qun_power')->insert([
                        'type'=>$key,
                        'qid'=>$qid,
                        'sysname'=>config('system_dirname'),
                        'groups'=>$groups
                    ]);
                }else{
                    Db::name('qun_power')->update([
                        'id'=>$array[$key]['id'],
                        'groups'=>$groups,                        
                    ]);
                }
            }
            $this->success('设置成功');
        }
        $qungroup = fun('qun@get_group','name',$qid)?:[];
        foreach ($this->qun_power_list AS $key=>$rs){
            $this->form_items[] = ['checkbox', $rs[0], $rs[1],$rs[2],$qungroup];
        }
        
        $this->tab_ext['page_title'] || $this->tab_ext['page_title'] = modules_config(config('system_dirname'))['name'].' 权限设置';
        return $this->editContent($info);        	    
    }
    
    /**
     * 生成模型分类菜单
     * @return unknown|array
     */
    protected function page_top_botton($pagetype='index',$qid='',$mid=''){
        if ($this->tab_ext['top_button']) {
            return $this->tab_ext['top_button'];
        }
        $tab_list = [];
        $mlist = model_config();
        if (count($mlist)>1) {
            foreach ( $mlist AS $rs) {
                $tab_list[$rs['id']] = [
                    'title'=>$rs['title'],
                    'url'=>auto_url($pagetype, ['mid' => $rs['id'],'qid'=>$qid]),
                ];
            }
        }
        $tab_list[] = [
            'type'=>'add',
            'url'=>auto_url('add', ['mid' => $mid ?: $this->mid,'ext_id'=>$qid]).'?fromurl='.urlencode(get_url('location')),
            'title'=>'发布',
        ];
        $this->tab_ext['top_button'] = $tab_list;
        return $tab_list;
    }
    
    protected function get_map(){
        return [];
    }
    
    /**
     * 纯属是为了兼容其它模块index方法没有升级的情况使用。
     * @param number $fid
     * @param number $mid
     * @param number $rows
     * @return unknown
     */
//     public function index_temp($fid=0,$mid=0,$rows=10)
//     {        
//         if(count(model_config())>1&&!$mid&&!$fid){
//             return $this->listall($rows);
//         }else{
//             return $this->index($fid,$mid,$rows);
//         }
//     }
    
    /**
     * 按模型或栏目列出自己发布的信息
     * @param number $fid
     * @param number $mid
     * @return mixed|string
     */
    public function index($fid=0,$mid=0,$rows=0)
    {
        if (!$rows) {
            $rows = input('rows')?:10;
        }
        
        if(!$mid && !$fid){
            //没有指定栏目或模型的话， 就显示默认模型的内容
            //$mid = $this->m_model->getId();
            $mid = model_config('default_id');
        }elseif($fid){
            $mid = $this->model->getMidByFid($fid);
        }
        
        $this->mid = $mid;
        $map = $fid ? ['fid'=>$fid] : ['mid'=>$mid];
        $map['uid'] = $this->user['uid'];
        
//      $data_list = $this->getListData($map,'',0,[],true);    //获取列表数据 true转义
// 	    $vars = [
// 	            'listdb'=>$data_list,
// 	            'field_db'=> $this->getEasyIndexItems(),   //列表显示哪些自定义字段
// 	            'pages'=> $data_list->render(),    //分页
// 	            'mid'=>$mid,
// 	            'fid'=>$fid,
// 	            'model_list' => $this->m_model->getTitleList(),
// 	    ];
// 	    //如果某个模型有个性模板的话，就不调用母模板
// 	    $template = getTemplate('index'.$mid)?:getTemplate('index');
// 	    return $this->fetch($template,$vars);
        
        $f_array = $this->getEasyIndexItems();
        if(empty($this->list_items)){
            $this->list_items = array_merge($f_array,
                    [
                            ['create_time', '日期', 'date'],
                            //['view', '浏览量', 'text'],
                            ['status', '审核', 'select2',['未审','已审','已推荐']],
                    ]);
        }
        
        $this->tab_ext['right_button'] || $this->tab_ext['right_button'] = [
                ['type'=>'delete'],
                ['type'=>'edit'],
                [
                        'url'=>iurl('show','id=__id__'),
                        'icon'=>'glyphicon glyphicon-eye-open',
                        'title'=>'浏览',
                        'target'=>'_blank',
                ],
        ];
        $this->tab_ext['page_title'] || $this->tab_ext['page_title'] = M('name').' 我发布的内容';
        $this->tab_ext['top_button'] = $this->page_top_botton();

        $this->assign('field_db',$f_array);
        $this->assign('model_list',$this->m_model->getTitleList());
        $this->assign('fid',$fid);
        $data_list = $this->getListData(array_merge($map,$this->get_map()),'',$rows,[],true);
        return $this->getMemberTable($data_list);

    }
    
    /**
     * 管理内容列表
     * @param number $qid 圈子ID
     * @param number $mid 内容模型ID
     * @param number $fid 栏目ID
     * @param number $status 主题状态
     * @param number $rows
     * @return void|unknown|\think\response\Json|mixed|string
     */
    public function manage($qid=0,$mid='',$fid='',$rows=10,$status=''){
        $map = $this->get_map();
        foreach ($map AS $key=>$rs){
            if(!in_array($key, ['id','uid','mid','fid','status','view','list','create_time','ext_id','ext_sys','title'])){
                unset($map[$key]);
            }
        }
        
        $this->tab_ext['page_title'] || $this->tab_ext['page_title'] = M('name').' 内容列表';
        
        if($qid){
            $this->tab_ext['top_button'] = $this->page_top_botton('manage',$qid,$mid);
            $map['ext_id'] = $qid;
        }else{
            $status_array = [];
            $this->tab_ext['top_button'][] = [
                'title'=>'全部',
                'url'=>url('manage',['mid'=>$mid,'fid'=>$fid]),
            ];
            foreach (fun("Content@status") AS $key=>$title){
                $menu_ck = false;
                if($key==-1){
                    continue ;
                //}elseif(fun('admin@sort',$fid)!==true && !fun('sort@admin')){ //审核员，不是管理员的情况
                }elseif(fun('admin@sort')!==true){  //不是超管及不是频道管理员的时候，但有可能是栏目管理员
                    
                    if(fun('sort@admin')){  //栏目管理员，要保留所有菜单
                        $this->tab_ext['top_button'][] = [
                            'title'=>$title,
                            'url'=>url('manage',['mid'=>$mid,'fid'=>$fid,'status'=>$key]),
                            'checked'=>is_numeric($status)?($status==$key?'1':''):'',
                        ];
                        $menu_ck = true;
                    }
                    
                    if(fun('admin@status_check',$key)!==true){                        
                        continue ;
                    }
                    $status_array[] = $key;
                }
                if(!$menu_ck){  //栏目管理员上面执行过了，不要重复
                    $this->tab_ext['top_button'][] = [
                        'title'=>$title,
                        'url'=>url('manage',['mid'=>$mid,'fid'=>$fid,'status'=>$key]),
                        'checked'=>is_numeric($status)?($status==$key?'1':''):'',
                    ];
                }                
            }
            //if ( fun('admin@sort',$fid)!==true ) {
            if ( fun('admin@sort')!==true ) {   //不是超管及不是频道管理员的时候
                if(fun('sort@admin')){
                    if ($status_array) {
                        $map['OR'] = [
                            'status'=>['in',$status_array],
                            'fid'=>['in',fun('sort@admin')]
                        ];
                    }else{
                        $map['fid'] = ['in',fun('sort@admin')];
                    }                    
                }elseif ($status_array) {
                    $map['status'] = ['in',$status_array];
                }else{
                    $this->showerr('你没权限！');
                }
            }
        }
        if (is_numeric($status)) {
            $map['status'] = $status;
        }
        
        if(!$this->tab_ext['right_button']){
            $this->tab_ext['right_button'] = [
                [
                    'type'=>'delete',
                    'url'=>url('delete','ids=__id__').'?fromurl='.urlencode(get_url('location')),
                ],
                [
                    'type'=>'edit',
                    'url'=>url('edit','id=__id__').'?fromurl='.urlencode(get_url('location')),
                ],
                [
                    'url'=>iurl('show','id=__id__'),
                    'icon'=>'glyphicon glyphicon-eye-open',
                    'title'=>'浏览',
                    'target'=>'_blank',
                ],     
            ];
            if(!$qid){  //不是圈子管理的话，就显示审核操作功能                
                if ( fun('admin@sort',$fid)!==true && !fun('sort@admin')) {
                    unset($this->tab_ext['right_button'][0]); //审核员，不显示删除操作
                    if(!$this->webdb['shenhe_edit']){
                        unset($this->tab_ext['right_button'][1]); //审核员没开通修改权限
                    }                    
                }else{
                    unset($this->tab_ext['right_button'][2]);
                }
                $this->tab_ext['right_button'][] = [
                    'title'=>'审核操作',
                    'type'=>'callback',
                    'fun'=>function($info){
                        $array = [];
                        $fid_array = fun('sort@admin')?:['NO']; //避免栏目不存在的时候，所以要设置一个 NO 即不存在的栏目ID
                        foreach(fun('Content@status') AS $key=>$title){
                            if(fun('admin@sort',$info['fid'])!==true && !in_array($info['fid'], $fid_array) && fun('admin@status_check',$key)!==true){
                                continue ;
                            }
                            $array[$title] = [
                                'url'=>iurl('wxapp.api/change_status',['id'=>$info['id'],'status'=>$key]),
                                'target'=>'ajax',
                            ];
                        }
                        return fun('link@more',"<i class='glyphicon glyphicon-star-empty'></i>审核操作",$array);
                    }
                ];
            }
        }
        
        
        
//         if(get_wxappAppid()){
//             $app_cfg = wxapp_open_cfg( get_wxappAppid() ) ?: [];
//             $qid = $app_cfg['aid'];
//         }
//         if (!$qid) {
//             $quns = fun('qun@getByuid',$this->user['uid']);
//             if (count($quns)>1) {
//                 $url = url('qun/choose/index').'?url='.urlencode( urls('manage').'?qid=' );
//                 $this->redirect($url);
//             }
//         }

        if($mid){
            $map['mid'] = $mid;
        }
        if($fid){
            $map['fid'] = $fid;
        }
        
        $this->list_items = [
            ['title', '标题', 'text'],
            ['create_time', '日期', 'date'],
            ['uid', '发布者', 'username'],
            ['status', '状态', 'select2',fun('Content@status')],
        ];
        $listdb = $this->model->getAll($map,$order="id desc",$rows,[],$format=FALSE);
        $pages = $listdb->render();
        $this->assign('listdb',$listdb);
        $this->assign('pages',$pages);
        $this->assign('qid',$qid);
        $this->assign('fid',$fid);
        $this->assign('mid',$mid);
        $this->assign('tab_ext',$this->tab_ext);
        return $this->getMemberTable($listdb);
        //return $this->fetch();
        
    }
    
    /**
     * 发布页，可以根据栏目ID或者模型ID，但不能为空，不然不知道调用什么字段
     * @param number $fid
     * @param number $mid
     * @return mixed|string
     */
    public function add($fid=0,$mid=0)
    {
        $data = $this->request->post();
        isset($data['fid']) && $fid = $data['fid'];
        
        if(!$mid && !$fid){
            if ($this->request->isAjax()) {
                return $this->error('栏目或模型不能同时为空!');
            }
            return $this->postnew();
            //$this->error('参数有误！');
        }elseif($fid && !$mid){ //根据栏目选择发表内容
            $mid = $this->model->getMidByFid($fid);
        }        
        $this->mid = $mid;

        //接口
        hook_listen('cms_add_begin',$data);        
        if (($result=$this->add_check($mid,$fid,$data))!==true) {
            if ($this -> request -> isPost()) {
                $this->error($result);
            }else{
                $this->assign('nopower',$result);   //在页面中处理提示更好些
            }
        }
        
        // 保存数据
        if ($this -> request -> isPost()) {
            //input('?get.ext_id') && $this->request->post(['ext_id'=>input('get.ext_id')]);
            return $this->saveAdd($mid,$fid,$data,get_cookie('fromurl'));
        }
        
        set_cookie('fromurl', input('fromurl')?:null );
        
        //发表时可选择的栏目
        $sort_array = $this->s_model->getTreeTitle(0,$mid);
        foreach($sort_array AS $key=>$title){
            $allowpost = get_sort($key,'allowpost');
            if($allowpost&&!in_array($this->user['groupid'],explode(',',$allowpost))){  //设置了用户组权限.
                unset($sort_array[$key]);
            }
        }
        //发布页要填写的字段
        $this->form_items = $this->getEasyFormItems();     //发布表单里的自定义字段
        //如果栏目存在才显示栏目选择项
        if(count($sort_array)>0){
            $this->form_items = array_merge(
                [
                    [ 'select','fid','所属栏目','',$sort_array,$fid],
                ],
                $this->get_category_select(),   //辅栏目
                input('ext_id')?[]:$this->get_my_qun(),
                $this->form_items
            );
        }else{
            $this->form_items = array_merge(
                input('ext_id')?[]:$this->get_my_qun(),
                $this->form_items
                );
        }
        
        //联动字段
       $this->tab_ext['trigger'] = $this->getEasyFieldTrigger();
       
       $this->tab_ext['area'] = config('use_area') || config('webdb.use_area'); //是否启用地区
       
        //分组显示处理
        $this->tab_ext['group'] = $this->get_group_form($this->form_items,'add');
        
//         $result = $this->post_begin([
//             'fid'=>$fid,
//             'mid'=>$mid
//         ]);
//         if ($result!==true) {
//             return $this->ok_js($result);
//         }
        
//         if( $this->tab_ext['group'] ){
//             unset($this->form_items);
//         }
        
        $this->tab_ext['page_title'] = $this->tab_ext['page_title']?: '发布 '.$this->m_model->getNameById($this->mid);
        $this->assign('fid',$fid);
        $this->assign('mid',$mid);
        $this->assign('info',get_post());  //方便地址栏赋值
        $this->assign('need_tncode',in_array($this->user['groupid'], $this->webdb['group_postnew_need_tncode'])?true:false);
        return $this->addContent();
    }
    

    
    /**
     * 修改内容
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id)
    {
        $info = $this->getInfoData($id);
        if(empty($info)){
            $this->error('内容不存在!');
        }
        
		//表单数据
	    $data = $this->request->post();

        //接口
	    hook_listen('cms_edit_begin',$data);
        if (($result=$this->edit_check($id,$info,$data))!==true) {
            $this->error($result);
        }
        
        $this->mid = $info['mid'];
        // 保存数据
        if ($this -> request -> isPost()) {
            return $this->saveEdit($this->mid,$data,input('fromurl')?:get_cookie('fromurl'));
        }
        
        set_cookie('fromurl', input('fromurl')?:null );
        
        //发表时可选择的栏目
        $sort_array = $this->s_model->getTreeTitle(0,$this->mid);
        foreach($sort_array AS $key=>$title){
            $allowpost = get_sort($key,'allowpost');
            if($key!=$info['fid'] && $allowpost && !in_array($this->user['groupid'],explode(',',$allowpost))){  //设置了用户组权限.
                unset($sort_array[$key]);
            }
        }
        //发布页要填写的字段
        $this->form_items = $this->getEasyFormItems();     //发布表单里的自定义字段
        //如果栏目存在才显示栏目选择项
        if(count($sort_array)>0){
            $this->form_items = array_merge(
                [
                    [ 'select','fid','所属栏目','',$sort_array],
                ],
                $this->get_category_select($id),   //辅栏目
                $this->get_my_qun($info),   //归属圈子专题或归属圈子
                $this->form_items
            );
        }else{
            $this->form_items = array_merge(
                input('ext_id')?[]:$this->get_my_qun(),
                $this->form_items
                );
        }
        
        //联动字段
        $this->tab_ext['trigger'] = $this->getEasyFieldTrigger();
        
        $this->tab_ext['page_title'] = $this->m_model->getNameById($this->mid);
        
        $this->tab_ext['area'] = config('use_area') || config('webdb.use_area'); //是否启用地区
        
        //分组显示
        $this->tab_ext['group'] = $this->get_group_form($this->form_items);
        
//         $result = $this->post_begin([
//             'info'=>$info
//         ]);
//         if ($result!==true) {
//             return $this->ok_js($result);
//         }
        
//         if( $this->tab_ext['group'] ){
//             unset($this->form_items);
//         }
        $this->assign('fid',$info['fid']);
        //修改内容后，最好返回到模型列表页，因为有可能修改了栏目
        return $this->editContent($info , '' ,'member');
    }
    
    /**
     * 删除内容
     * @param unknown $ids
     */
    public function delete($ids=null)
    {
        if(empty($ids)){
            $this->error('ID有误');
        }
        $ids = is_array($ids) ? $ids : [$ids];
        $num = 0;
        foreach ($ids AS $id) {
            $info = $this->getInfoData($id);
            
            //接口
            hook_listen('cms_delete_begin',$id);            
            if (($result=$this->delete_check($id,$info))!==true) {
                $this->error($result);
            }
            
            $this->deleteOne($id,$info['mid']) && $num++;
            
        }        
        if( $num>0 ){
            $msg = "成功删除 {$num} 条记录";
            if(defined('SHOW_RUBBISH') && SHOW_RUBBISH===true && (!defined('FORCE_DELETE') || FORCE_DELETE!==true) ){
                $msg = '下架成功';
            }
            $this->success($msg, input('fromurl') ?: auto_url('index',['mid'=>$this->mid]));
        }else{
            $this->error('删除失败');
        }
    }
}