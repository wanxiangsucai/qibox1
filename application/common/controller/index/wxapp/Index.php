<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
use app\common\traits\ModuleContent;


//小程序或APP调用的列表数据
abstract class Index extends IndexBase
{
    use ModuleContent;
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
     * 列表数据
     * @param number $fid 栏目ID
     * @param string $type 类型筛选
     * @return \think\response\Json
     */
    public function index($fid=0,$type=''){
        $map = [];
        $fid && $map['fid'] = $fid;
        //$map['ispic'] = 1;
        $rows = 5;
        $order = 'id desc';
        if($type=='star'){
            $map['status'] = 2;
        }elseif($type=='hot'){
            $order = 'view desc';
        }elseif($type=='new'){
            $order = 'id desc';
        }elseif($type=='reply'){
            $order = 'list desc';
        }
        $mid = $this->model->getMidByFid($fid) ?: $this->mid ;
        $array = getArray( $this->model->getListByMid($mid,$map,$order,$rows) );
        foreach($array['data'] AS $key => $rs){
            $rs['create_time'] = date('Y-m-d H:i',$rs['create_time']);
            unset($rs['sncode']);
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
        return $this->ok_js($array['data']);
    }
}













