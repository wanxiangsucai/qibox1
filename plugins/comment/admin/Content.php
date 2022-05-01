<?php
namespace plugins\comment\admin;

use app\common\controller\AdminBase; 
use plugins\comment\model\Content AS contentModel;
use app\common\traits\AddEditList;

class Content extends AdminBase
{
    use AddEditList;
    protected $validate = '';

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new contentModel();
        
        //顶部菜单
        $this->tab_ext['top_button'] = [
            ['type'=>'delete'],
            [
                'title'       => '审核',
                'icon'        => '',
                'class'       => 'ajax-post confirm',
                'target-form' => 'ids',
                'icon'        => 'fa fa-check-circle-o',
                'href'        => auto_url('batch',['action'=>'yz'])
            ],
            [
                'title'=>'导出Exce',
                'icon'=>'fa fa-table',
                'url'=>$this->weburl.(strstr($this->weburl,'?')?'&':'?').'excel=1&page=1',
            ],
        ];
        
        //右边菜单
        $this->tab_ext['right_button'] = [
                ['type'=>'delete'],
        ];
        
        $this->tab_ext['page_title'] = '评论管理';
        
        //搜索字段
        $this->tab_ext['search'] = [
                'content'=>'评论内容',
                'uid'=>'发布者UID',
                'sysid'=>'频道ID',
        ];
        $array = [];
        foreach(modules_config() AS $rs){
            if(in_array($rs['keywords'], ['bbs','search','tongji'])){
                continue;
            }
            $array[$rs['id']] = $rs['name'];
        }
        //筛选字段
        $this -> tab_ext['filter_search'] = [
            'sysid'=>$array,
            'status'=>['未审核','已审核'],
        ];
        
        $this->list_items = [            
                    ['content','评论内容','callback',function($value){
                        return get_word(del_html($value), 70);
                    }],
                    ['create_time','评论日期','datetime'],
                    ['status','审核与否','switch'],               
                    ['uid','发布者','username'],
                    ['sysid','所属模块','callback',function($value){
                        return $value>0?modules_config($value)['name']:plugins_config(abs($value))['name'];
                    }],
                    ['list','排序值','text.edit'],
                    ['id','来源','callback',function($k,$rs){
                        if ($rs['sysid']>0) {
                            $dirname = modules_config($rs['sysid'])['keywords'];
                            $url = iurl("$dirname/content/show",['id'=>$rs['aid']]);
                        }elseif($rs['sysid']<0){
                            $dirname = plugins_config(abs($rs['sysid']))['keywords'];
                            $url = purl("$dirname/content/show",['id'=>$rs['aid']],'index');
                        }
                        return "<a href='{$url}' target='_blank' class='si si-link' title='查看来源于哪个主题'></a>";
                    }],
                ];
    }
    
    public function index() {
        
        if(input('excel')){
            return $this->excel(500);
        }

        $listdb = $this->getListData($map = [], $order = '');
        return $this -> getAdminTable($listdb);
    }
    
    /**
     * 导出excel表格数据
     * @param number $rows 每卷几条记录
     * @return unknown
     */
    protected function excel($rows = 500){
        $array = self::getListData($map = [], $order='' ,$rows );
        $array_module = [];
        foreach (modules_config() AS $rs){
            $array_module[$rs['id']] = $rs['name'];
        }
        foreach (plugins_config() AS $rs){
            $array_module[-$rs['id']] = $rs['name'];
        }
        $field_array = [
            'i'=>'序号',
            'id'=>'ID',
            'uid'=>'用户UID',
            '_uid'=>[
                'key'=>'uid',   //处理上面key重复的问题
                'title'=>'用户帐号',
                'type'=>'username',
            ],
            'create_time'=>[
                'title'=>'评论日期',
                'type'=>'time',
            ],
            'content'=>'评论内容',
            'agree'=>'支持数',
            'disagree'=>'反对数',
            'sysid'=>[
                'title'=>'所属频道',
                'opt'=>$array_module,
            ],
            'aid'=>[
                'title'=>'归属主题',
                'callback'=>function($v,$rs){
                    $array = $rs['sysid']>0?modules_config($rs['sysid']):plugins_config(abs($rs['sysid']));
                    $dirname = $array['keywords'];
                    $model = get_model_class($dirname,'content');
                    if($model){
                        $info = $model->getInfoByid($v);
                        return $info['title'];
                    }
                },
             ],
        ];
        return $this->bak_excel($array,$field_array);
    }
    
    /**
     * 批量处理
     * @param string $action
     * @param array $ids
     */
    public function batch($action='',$ids=[]){
        if (!$ids) {
            $this->error('内容不存在!');
        }
        if ($action=='yz'){
            $this->model->where('id','IN',$ids)->update(['status'=>1]);
            $this->success('操作完成!');
        }
    }
}
