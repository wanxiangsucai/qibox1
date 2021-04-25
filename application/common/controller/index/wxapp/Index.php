<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
//use app\common\traits\ModuleContent;


//小程序或APP调用的列表数据
abstract class Index extends IndexBase
{
    //use ModuleContent;
    protected $model;                  //内容
    protected $mid;                      //模型ID
    
    
    public function add(){
        die('出错了!');
    }
    public function edit(){
        die('出错了!');
    }
    public function delete(){
        die('出错了!');
    }
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'content');
        $this->mid = 1;
    }
    
    /**
     * 获取列表数据
     * @param number $fid 指定获取哪个栏目，会把子栏目的数据一起列出来
     * @param string $type star推荐数据，hot热门数据 ， new最新发表 ， reply最新回复
     * @param number $rows 每次获取几条
     * @param string $notfid 不包括哪些栏目的内容，多个栏目用逗号隔开
     * @param number $mid 模型ID，指定获取哪个模型的数据
     * @param number $ext_id 关联圈子的ID，不一定是圈子
     * @param string $ext_sys 圈子目录名或频道ID
     */
    public function index($fid=0,$type='',$rows=10,$notfid='',$mid=1,$ext_id=0,$ext_sys=''){
        $map = [
            'status'=>['>',0],
        ];
        if (input('notfid')) {
            $notfid = input('notfid');
        }
        if (input('mid')) {
            $mid = input('mid');
        }
        
        if (input('ext_id')) {
            $ext_id = input('ext_id');
        }
        if (input('ext_sys')) {
            $ext_sys = input('ext_sys');
        }
        if ($ext_id) {
            $map['ext_id'] = $ext_id;
        }
        if ($ext_sys && !is_numeric($ext_sys)) {
            $module = modules_config($ext_sys);
            if (!$module) {
                return $this->error('频道不存在！');
            }
            $ext_sys = $module['id'];
        }
        if ($ext_sys) {
            $map['ext_sys'] = $ext_sys;
        }
        
        if ($notfid!='') {
            $detail = explode(',', $notfid);
            foreach ($detail AS $key=>$value){
                if (!is_numeric($value)) {
                    unset($detail[$key]);
                }
            }
            if ($detail) {
                $map['fid'] = ['not in',implode(',', $detail)];
            }
        }
        $fid && $map['fid'] = ['in',get_sort($fid,'','sons')];
        //$map['ispic'] = 1;
        $order = 'list desc,id desc';
        if($type=='star'){
            $map['status'] = ['>',1];
        }elseif($type=='hot'){
            $order = 'view desc';
        }elseif($type=='new'){
            $order = 'id desc';
        }elseif($type=='reply'){
            $order = 'list desc,id desc';
        }
        if ($mid==-1) {
            $array = getArray( $this->model->getAll($map,$order,$rows) );
        }else{
            $mid = $fid ? (get_sort($fid,'mid')?:$mid) : $mid;
            if (!model_config($mid)) {
                return $this->err_js('你指定的模型并不存在！');
            }
            $array = getArray( $this->model->getListByMid($mid,$map,$order,$rows) );
        }
        
        foreach($array['data'] AS $key => $rs){
            $rs['create_time'] = date('Y-m-d H:i',$rs['create_time']);
            $rs['picurl'] = tempdir($rs['picurl']);
            $rs['content'] = get_word(del_html($rs['content']), 100);
            unset($rs['_content'],$rs['sncode'],$rs['password']);
            $array['data'][$key] = $rs;
        }
        
        return $this->ok_js($array);        
    }
    
    /**
     * 根据用户UID获取其相应的数据
     * @param number $uid
     * @param number $mid
     * @param number $rows
     * @param string $keyword
     * @param string $quote 默认都是站内引用调用
     * @return void|unknown|\think\response\Json
     */
    public function listbyuid($uid=0,$mid=-1,$rows=20,$keyword='',$quote=true){
        if ($quote && get_model_class(config('system_dirname'), 'putin') ) { //兼容考试与答题系统
            return $this->listmypaper($uid,$rows,$keyword);
        }
        if (empty($uid)) {
            $uid = $this->user['uid'];
        }
        if (empty($uid)) {
            return $this->err_js('UID不存在');
        }
        $map=[
            'uid'=>$uid,
        ];
        if ($mid>0){
            $map['mid'] = $mid;
        }
        if ($keyword!='') {
            $map['title'] = ['like','%'.$keyword.'%'];
        }
        
        if ($mid>0) {            
            if (empty(model_config($mid))) {
                return $this->err_js('模型不存在');
            }
            $data = $this->model->getListByMid($mid,$map,"id desc",$rows,$pages=[],$format=true);
        }else{
            $data = $this->model->getAll($map,"id desc",$rows,$pages=[],$format=true);
        }
        
        $array = getArray($data);
        foreach ($array['data'] AS $key=>$rs){
            $rs['picurl'] = tempdir($rs['picurl']);
            if(config('system_dirname')=='bbs'){
                $rs['content'] = fun("bbs@getContents",$rs['id'],100);
            }else{
                $rs['content'] = get_word(del_html($rs['content']), 100);
            }
            $rs['time'] = date('Y-m-d H:i',$rs['create_time']);
            $rs['url'] = iurl(config('system_dirname').'/content/show',['id'=>$rs['id']]);
            unset($rs['_content'],$rs['full_content'],$rs['sncode'],$rs['password']);
            $array['data'][$key] = $rs;
        }
        return $this->ok_js($array);
    }
    
    
    public function listmypaper($uid=0,$rows=20,$keyword=''){
        if (empty($uid)) {
            $uid = $this->user['uid'];
        }
        if (empty($uid)) {
            return $this->err_js('UID不存在');
        }
        $map=[
            'uid'=>$uid,
        ];
        if ($keyword!='') {
            $map['name'] = ['like','%'.$keyword.'%'];
        }
        $data = get_model_class(config('system_dirname'), 'category')->where($map)->order("id desc") -> paginate();
        
        $array = getArray($data);
        foreach ($array['data'] AS $key=>$rs){
            $rs['title'] =  $rs['name'];
            $rs['picurl'] = tempdir($rs['picurl']);
            $rs['content'] = get_word(del_html($rs['content']), 100);
            $rs['time'] = $rs['create_time'] ? format_time($rs['create_time'],'Y-m-d H:i') :'';
            $rs['url'] = iurl(config('system_dirname').'/category/index',['fid'=>$rs['id']]);
            unset($rs['_content'],$rs['full_content'],$rs['sncode']);
            $array['data'][$key] = $rs;
        }
        return $this->ok_js($array);
    }
    
    /**
     * 首页幻灯片
     * @return \think\response\Json
     */
    public function banner(){
        $map = ['status'=>2];
        $map['ispic'] = 1;
        $rows = 4;
        $array = getArray( $this->model->getListByMid(1,$map,'id desc',$rows) );
        foreach($array['data'] AS $key=>$rs){
            unset($rs['content'],$rs['full_content'],$rs['sncode']);
            $array['data'][$key] = $rs;
        }
        return $this->ok_js($array['data']);
    }
}













