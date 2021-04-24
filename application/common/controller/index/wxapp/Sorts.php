<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase; 
use app\common\fun\Sort AS SortFun;

//获取栏目信息
abstract class Sorts extends IndexBase
{
    protected $model;
    protected $dirname;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $this->dirname = $array[0][1];
        $this->model = get_model_class($this->dirname,'sort');
    }
    
    /**
     * 只获取同级的栏目，也包括本身
     * @param number $id
     * @param number $type
     * @return void|unknown|\think\response\Json
     */
    public function brother($id=0,$type=0){
        $data = SortFun::brother($id);
        foreach($data AS $key=>$name){
            $array[] = [
                'id'=>$key,
                'name'=>$name,
            ];
        }
        return $this->ok_js($array);
    }
    
    /**
     * 获取子栏目
     * @param number $id 当前栏目ID
     * @param number $type 为1的话，把子、孙也一起列出来,
     * @return void|unknown|\think\response\Json
     */
    public function son($id=0,$type=1){
        $array = [];
        if ($type) {            
            $data = SortFun::sons($id);            
        }else{
            $data = SortFun::son($id);
        }
        foreach($data AS $key=>$name){
            $array[] = [
                'id'=>$key,
                'name'=>$name,
            ];
        }
        return $this->ok_js($array);
    }
    
    /**
     * 获取父级栏目
     * @param number $id 当前栏目ID
     * @param number $type 为1的话，获取父辈及祖父,曾祖父,这样的上级栏目,一般用在面包屑导航,也包括自身,
     * @return void|unknown|\think\response\Json
     */
    public function father($id=0,$type=0){
        if ($type) {
            $array = [];
            $data = SortFun::fathers($id);
            foreach($data AS $key=>$name){
                $array[] = [
                    'id'=>$key,
                    'name'=>$name,
                ];
            }
        }else{
            $array = SortFun::father($id);
        }        
        return $this->ok_js($array);
    }
    
    /**
     * 获取栏目数据
     * @param number $fid 默认只获取一级栏目的数据，即fid=0 要列出所有的话，就设置为all
     * @param number $onlytile 设置为1的话，只获取栏目名称
     * @param string $onlyid 是否只获取指定的栏目ID，多个用半角逗号隔开
     * @param string $noid 是否过滤不要哪些ID，多个用半角逗号隔开
     * @return unknown
     */
    public function index($fid=0,$onlytitle=0,$onlyid='',$noid='',$mid=''){
        if (input('fid')) {
            $fid = input('fid');
        }
        if (input('onlytitle')) {
            $onlytitle = input('onlytitle');
        }
        if (input('onlyid')) {
            $onlyid = input('onlyid');
        }
        if (input('noid')) {
            $noid = input('noid');
        }
        if (input('mid')) {
            $mid = input('mid');
        }
        $only_array = [];
        if ($onlyid) {
            $only_array = explode(',', $onlyid);
        }
        $not_array = [];
        if ($noid) {
            $not_array = explode(',', $noid);
        }
        $mid_array = [];
        if ($mid) {
            $mid_array = explode(',', $mid);
        }
        $array = [];
        $sort_array = sort_config($this->dirname,null,null);
        foreach($sort_array AS $rs){
            if ($only_array) {
                if (!in_array($rs['id'], $only_array)) {
                    continue ;
                }
            }elseif (is_numeric($fid) && $rs['pid']!=$fid) {
                continue ;
            }
            if ($not_array && in_array($rs['id'], $not_array)) {
                continue ;
            }
            if ($mid_array && !in_array($rs['mid'], $mid_array)) {
                continue ;
            }
            if ($onlytitle) {
                $array[] = [
                    'id'=>$rs['id'],
                    'name'=>$rs['name'],
                ];
            }else{
                $array[] = $rs;
            }
        }        
        return $this->ok_js($array);
    }
    
    public function hot(){
        $array = [
                [
                        'id' => 'reply',
                        'name' => '最新回复',
                ],
                [
                        'id' => 'new',
                        'name' => '最新贴子',
                ],
                [
                        'id' => 'star',
                        'name' => '推荐贴子',
                ],
                [
                        'id' => 'hot',
                        'name' => '热门贴子',
                ],
        ];
        return $this->ok_js($array);
    }

}













