<?php
namespace app\shop\index;

use app\common\controller\IndexBase; 
use app\common\traits\AddEditList;
use app\shop\model\Car as CarModel;
use think\console\Input;

//购物车
class Car extends IndexBase
{
    use AddEditList;
    protected $model;
    protected $car_model;
    protected $form_items = [
            ['text', 'linkman', '联系人'],
            ['text', 'telphone', '联系电话'],
            //['image', 'picurl', '分享图片'],
            ['radio', 'ifolpay', '支付类型', '', [1 => '在线付款', 0 => '货到付款'], 1],
    ];
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new CarModel();  
    }
    
    public function act($shopid=0,$type=''){
        if (!$shopid) {
            return 'fail';
        }
		if (!$this->user) {
            return 'fail';
        }
        $info = $this -> model -> where(['shopid'=>$shopid,'uid'=>$this->user['uid']]) -> find() ;
        $num = intval(input('num'));
        $type1 = intval(input('type1'));
        $type2 = intval(input('type2'));
        $type3 = intval(input('type3'));
        if ($type=='del') { //踢出购物车
            //$shopids = is_array($shopid)?$shopid:[$shopid];
            if ($this -> model -> destroy($info['id'])) {
                return 'ok';
            } else {
                return 'fail';
            }
        }elseif(!$info){    //购物车没有的话,就直接加进去
            $num<1 && $num=1;
            $data = [
                    'shopid'=>$shopid,
                    'uid'=>$this->user['uid'],
                    'type1'=>$type1,
                    'type2'=>$type2,
                    'type3'=>$type3,
                    'num'=>$num,
            ];
            if ($this -> model -> create($data)) {
                return 'ok';
            } else {
                return 'fail';
            }
        }else{
            
            if($type=='plus'){     //购物车页面简单的加减数据
                $_num = $info['num']+intval($num);  //$num可以是负数
                if($_num<1){
                    $_num = 0;
                }
                $data = [
                        'id'=>$info['id'],
                        'num'=>$_num,
                ];
            }elseif($type=='change_num'){   //直接修改购买数量
                $data = [
                        'id'=>$info['id'],
                        'num'=> intval($num),
                ];
            }elseif($type=='choose'){   //是否选中 下单
                $data = [
                        'id'=>$info['id'],
                        'ifchoose'=> input('ck')==1 ? 1 : 0,
                ];
            }else{
                $data = [
                        'id'=>$info['id'],
                        'type1'=>$type1,
                        'type2'=>$type2,
                        'type3'=>$type3,
                        'num'=>$num,
                ];
            }
            if ($this -> model -> update($data)) {
                return 'ok';
            } else {
                return 'fail';
            }
        }
    }
    
    //购物车列表 
    public function index() {
        $listdb = $this->model->getList($this->user['uid']);
        $this->assign('listdb', $listdb);
        return $this ->fetch();
    } 
    
    
}
