<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\ModuleContent;

abstract class C extends AdminBase
{
    use ModuleContent;
    protected $model;
    protected $m_model;
    protected $f_model;
    protected $s_model;
    
    protected $validate = 'Content';
    protected $form_items;
    protected $list_items;
    protected $tab_ext;
    protected $mid;
    
    protected function _initialize()
    {
        parent::_initialize();        
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'content');
        $this->s_model     = get_model_class($dirname,'sort');
        $this->m_model   = get_model_class($dirname,'module');
        $this->f_model     = get_model_class($dirname,'field');
    }
    
    /**
     * 新发表内容入口 有的模型可能不使用栏目 而是直接在模型下面发布东西
     * @param number $mid
     * @return unknown
     */
    public  function postnew($mid = 0){
        if (config('post_need_sort')==true) {
            return self::chooseSort($mid);
        }else{
            return self::chooseModule();
        }
    }
    
    /**
     * 发布页，可以根据栏目ID或者模型ID，但不能为空，不然不知道调用什么字段
     * @param number $fid
     * @param number $mid
     * @return unknown
     */
    public function add($fid=0,$mid=0)
    {
        $data = $this->request->post();
        isset($data['fid']) && $fid = $data['fid'];
        
        if($fid && !$mid){
            $mid = $this->model->getMidByFid($fid);
        }elseif( !$fid && !$mid ){  //栏目与模型都为空
            return self::postnew($mid);
        }elseif( config('post_need_sort') && !$fid ){  //指定必须要选择栏目的频道
            return self::postnew($mid);
        }
        $this->mid = $mid;
        
        //接口
        hook_listen('cms_add_begin',$data);
        if (($result=$this->add_check($mid,$fid,$data))!==true) {
            $this->error($result);
        }
        
        // 保存数据
        if ($this -> request -> isPost()) {
//             $data = $this->request->post();
            
//             if(isset($data['map'])){
//                 list($data['map_x'],$data['map_y']) = explode(',', $data['map']);
//             }
            
            $this->saveAdd($mid,$fid,$data);
        }
        
        //发表时可选择的栏目
        $sort_array = $this->s_model->getTreeTitle(0,$mid);
        //发布页要填写的字段
        $this->form_items = $this->getEasyFormItems();     //发布表单里的自定义字段
        //如果栏目存在才显示栏目选择项
        if( config('post_need_sort') ){
            $this->form_items = array_merge(
                        [
                                [ 'select','fid','所属栏目','',$sort_array,$fid],
                               // [ 'linkages','street_id','所属地区','','area',4],
                        ],
                        $this->getEasyFormItems()
                    );
        }
        
        //联动字段
        $this->tab_ext['trigger'] = $this->getEasyFieldTrigger();
        
        $this->tab_ext['area'] = config('use_area'); //是否启用地区
        
        //分组显示
        $this->tab_ext['group'] = $this->get_group_form($this->form_items);
        if( $this->tab_ext['group'] ){
            unset($this->form_items);
        }

        $this->tab_ext['page_title'] = '发布 '.$this->m_model->getNameById($this->mid);

        return $this->addContent('index',['fid'=>$fid]);
    }
    
    /**
     * 模型分类导航
     * @return string[][]|NULL[][]|unknown[][]
     */
    protected function nav(){
        $tab_list   = [
                [
                        'title'=>'所有内容',
                        //'url'=>auto_url('listall'),
                        'url'=>auto_url('index','type=listall'),
                ]
        ];
        $mlist = $this->m_model->getTitleList();
        foreach ( $mlist AS $key => $value) {
            $tab_list[$key] = [
                    'title'=>$value,
                    'url'=>auto_url('index', ['mid' => $key]),
            ];
        }
        return $tab_list;
    }
    
    /**
     * 一次性列出所有模型的内容，效率会比较低
     * @return mixed|string
     */
    public function listall()
    {
        isset($this->tab_ext['nav'])              || $this->tab_ext['nav'] = [ $this->nav() , 0];
        isset($this->tab_ext['page_title'])     || $this->tab_ext['page_title'] = '内容管理';
        isset($this->tab_ext['top_button'])   || $this->tab_ext['top_button'] = [
                [
                        'title' => '发布内容',
                        'icon'  => 'fa fa-plus',
                        'class' => 'btn btn-primary',
                        'href'  => auto_url('postnew')
                ],
                [
                        'type'       => 'delete',
                ],
                [
                        'title'       => '添加到辅栏目',
                        'icon'        => 'fa fa-indent',
                        'class'       => 'btn btn-primary ajax-post confirm',
                        'target-form' => 'ids',
                        'href'        => auto_url('info/add')
                ],
                [
                        'title' => '返回栏目列表',
                        'icon'  => 'fa fa-reply',
                        'class' => 'btn btn-primary',
                        'href'  => auto_url('sort/index')
                ],
        ];
        
        //比如万能表单是不需要栏目的，就不要显示栏目
        if(empty(config('post_need_sort'))){
            unset($this->tab_ext['top_button'][3]);
        }
        if(empty(config('use_category'))){
            unset($this->tab_ext['top_button'][2]);
        }
        
        if(empty($this->list_items)){
            //列表显示哪些字段
            $this->list_items =  [
                    ['title', '标题', 'link',iurl('content/show',['id'=>'__id__']),'_blank'],
                    ['fid', '所属栏目', 'select',$this->s_model->getTitleList(),config('system_dirname')],
                    ['mid', '所属模型', 'select2',$this->m_model->getTitleList()],
                    ['uid', '发布者', 'callback',function($value){
                        $array = get_user($value);
                        return $array['username'];
                    }],
                    ['create_time', '发布日期', 'datetime'],
                    ['view', '浏览量', 'text.edit'],
                    ['list', '排序值', 'text.edit'],
                    ['status', '审核', 'select',['未审','已审','已推荐']],
            ];
            //比如万能表单是不需要栏目的，就不要显示栏目
            if(empty(config('post_need_sort'))){
                unset($this->list_items[1]);
            }
        }
        $data = $this->model->getAll( $this->getMap() ,$this->getOrder('id desc'));
        return $this->getAdminTable($data);
    }
    
    
    /**
     * 列出所有内容，可以根据栏目ID或者模型ID，但不能为空，不然不知道调用什么字段
     * @param number $fid
     * @param number $mid
     * @param string $type 为listall值的话,显示所有,在这里调用的目的是为了省去再单独的设置后台权限
     * @return mixed|string
     */
    public function index($fid=0,$mid=0,$type='')
    {    
        if($type=='listall'){   //放在这里的话,就省去再单独的设置后台权限
            return $this->listall();
        }elseif($type=='excel'){
            return $this->excel($mid,input()['ids']);
        }
        if(!$mid && !$fid){
            //$this->error('参数有误！');
            //$mid = $this->m_model->getID();
            return $this->listall();
        }elseif($fid){ //根据栏目选择发表内容
            $mid = $this->model->getMidByFid($fid);
            //$mid = ContentModel::getMidByFid($fid);
        }
        
        $this->mid = $mid;
        $this->tab_ext['nav'] = [ $this->nav() , $mid];
        isset($this->tab_ext['page_title']) || $this->tab_ext['page_title'] = '内容管理';

        
        if(!isset($this->tab_ext['top_button'])){
            $this->tab_ext['top_button'] = [
                    [
                            'title' => '发布内容',
                            'icon'  => 'fa fa-plus',
                            'class' => 'btn btn-primary',
                            'href'  => auto_url('add',$fid?['fid'=>$fid]:['mid'=>$mid])
                    ],
                    [
                            'type'       => 'delete',
                    ],
                    [
                            'title'       => '添加到辅栏目',
                            'icon'        => 'fa fa-indent',
                            'class'       => 'btn btn-primary ajax-post confirm',
                            'target-form' => 'ids',
                            'href'        => auto_url('info/add')
                    ],
                    [
                            'title' => '返回栏目列表',
                            'icon'  => 'fa fa-reply',
                            'class' => 'btn btn-primary',
                            'href'  => auto_url('sort/index')
                    ],
            ];
            
            //比如万能表单是不需要栏目的，就不要显示栏目
            if(empty(config('post_need_sort'))){
                unset($this->tab_ext['top_button'][3] );
            }
            
            //不使用辅栏目
            if(empty(config('use_category'))){
                unset($this->tab_ext['top_button'][2]);
            }
            
        }
        
        //排序方式
        $this -> tab_ext['order'] = 'view,list';
        //搜索字段
        $this -> tab_ext['search'] = array_merge(['title'=>'标题','uid'=>'用户uid'],$this->getEasySearchItems());
        //筛选字段
        $this->tab_ext['filter_search'] = array_merge( $this->getEasyfiltrateItems(),[
                'fid'=>get_sort(0,'all'),
                'status'=>['未审核','已审核','已推荐']
        ]);
        
        if(empty($this->list_items)){
            $array =  [
                    ['fid', '所属栏目', 'select',$this->s_model->getTitleList(),config('system_dirname')],
                    //['mid', '所属模型', 'select2',$this->m_model->getTitleList()],
                    ['uid', '发布者', 'callback',function($value){
                        $array = get_user($value);
                        return $array['username'];
                    }],
                    ['create_time', '创建日期', 'datetime'],
                    ['view', '浏览量', 'text.edit'],
                    ['list', '排序值', 'text.edit'],
                    ['status', '审核', 'select',['未审','已审','已推荐']],
            ];
            
            //比如万能表单是不需要栏目的，就不要显示栏目
            if(empty(config('post_need_sort'))){
                unset( $array[0] );
            }
            
            //列表显示哪些字段
            $this->list_items = array_merge(
                    $this->getEasyIndexItems(),  //列表要显示的自定义字段
                    $array
                    );            
        }
        
        $data = self::getListData($fid ? ['fid'=>$fid] : ['mid'=>$mid]);
        return $this->getAdminTable($data);
    }
    

    
    /**
     * 修改内容
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id)
    {
        $this->mid = $this->model->getMidById($id);
        
        // 保存数据
        if ($this -> request -> isPost()) {
			//表单数据
			$data = $this->request->post();
			
			if(isset($data['map'])){
			    list($data['map_x'],$data['map_y']) = explode(',', $data['map']);
			}

            $this->saveEdit($this->mid,$data);
        }
        
        //发表时可选择的栏目
        $sort_array = $this->s_model->getTreeTitle(0,$this->mid);
        //发布页要填写的字段
        $this->form_items = $this->getEasyFormItems();     //发布表单里的自定义字段
        //如果栏目存在才显示栏目选择项
        if(config('post_need_sort')){
            $this->form_items = array_merge(
                    [
                            [ 'select','fid','所属栏目','',$sort_array],
                    ],
                    $this->getEasyFormItems()
                    );
        }
        
        //联动字段
        $this->tab_ext['trigger'] = $this->getEasyFieldTrigger();
        
        $this->tab_ext['group'] = $this->get_group_form($this->form_items);
        if( $this->tab_ext['group'] ){
            unset($this->form_items);
        }
        
        $this->tab_ext['page_title'] = $this->m_model->getNameById($this->mid);
        
        $this->tab_ext['area'] = config('use_area'); //是否启用地区
        
        if(empty($id)) $this->error('缺少参数');
        
        $info = $this->getInfoData($id);
        
        //修改内容后，最好返回到模型列表页，因为有可能修改了栏目
        return $this->editContent($info);
    }
    
    
    /**
     * 删除内容
     * @param unknown $ids 内容ID
     */
    public function delete($ids=null)
    {
        if(empty($ids)){
            $this->error('ID有误');
        }        
        $num = $this->deleteContent($ids);        
        if( $num>0 ){
            $this->success("成功删除 {$num} 条记录", auto_url('index',['mid'=>$this->mid]));
        }else{
            $this->error('删除失败');
        }
    }
    
    /**
     *ajax快速编辑
     * {@inheritDoc}
     * @see \app\common\controller\AdminBase::quickedit()
     */
    public function quickedit(){
        $data = input();
        if($data['name']&&$data['pk']){
            if($data['type']=='switch'){
                $data['value'] = $data['value']=='false' ? 0 : 1;
            }
            $data = [
                    'id'=>$data['pk'],
                    $data['name']=>$data['value'],
            ];
            if( $this->model->editData(0,$data) ){
                $this->success('设置成功');
            }else{
                $this->error('设置失败');
            }
            //return $data;
        }
    }
    
    
    /**
     * 导出excel表格
     * @param number $mid
     * @param array $ids
     */
    public function excel($mid=0,$ids=[]){

        if(!$ids){
            $this->error('没有数据可导出!');
        }
        
        $array = $this->getEasyFormItems();
        foreach($array AS $rs){
            $fieldDB[$rs[1]] = $rs[2];
        }
        
        
        $outstr="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"5\"><tr>";
        $outstr.="<th bgcolor=\"#A5A0DE\">序号</th>";
        
        
        foreach($fieldDB AS $title){
            $outstr.="<th bgcolor=\"#A5A0DE\">$title</th>";
        }
        $outstr.="</tr>";
        foreach($ids  AS $id){
            $rs = $this->model->getInfoByid($id , true);
            $outstr.="<tr><td align=\"center\">$rs[id]</td>";
            foreach($fieldDB AS $k=>$v){
                $outstr.="<td align=\"center\">{$rs[$k]}</td>";
            }
            $outstr.="</tr>";
        }
        $outstr.="</table>";
        ob_end_clean();
        header('Last-Modified: '.gmdate('D, d M Y H:i:s',time()).' GMT');
        header('Pragma: no-cache');
        header('Content-Encoding: none');
        header('Content-Disposition: attachment; filename=MicrosoftExce.xls');
        header('Content-type: text/csv');
        echo "<!doctype html>
<html lang='en'>
 <head>
  <meta charset='UTF-8'>
  <title></title>
 </head>
 <body>
$outstr
 </body>
</html>";
        exit;
    }
    
}