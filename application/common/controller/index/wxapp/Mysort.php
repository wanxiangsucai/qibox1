<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase; 


//获取我的栏目信息
abstract class Mysort extends IndexBase
{
    protected $model;
    protected $dirname;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $this->dirname = $array[0][1];
        $this->model = get_model_class($this->dirname,'mysort');
    }
    
    /**
     * 获取我的栏目数据
     * @param number $uid 用户UID
     * @param number $ext_id 圈子UID
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function index($uid=0,$ext_id=0){
        if ($uid) {
            $map['uid'] = $uid;
        }elseif($ext_id){
            $map['ext_id'] = $ext_id;
        }else{
            return $this->err_js('参数不存在!');
        }
        $data_list = $this->model->where($map)->order('list desc,id asc')->column(true);
        return $this->ok_js(array_values($data_list));
    }
}













