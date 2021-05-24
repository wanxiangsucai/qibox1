<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
use app\common\traits\ModuleContent;

//小程序 显示内容
abstract class Show extends IndexBase
{
    use ModuleContent;
    protected $model;                  //内容主题
    protected $mid;                    //模型ID
    
    
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
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'content');
        $this->mid = 1;
    }
    
    /**
     * 调取显示内容主题
     * @param number $id 内容ID
     * @return \think\response\Json
     */
    public function index($id=0){
        $info = $this->getInfoData($id,true);
        if(empty($info)){
            return $this->err_js('内容不存在');
        }        
        $info = $this->format_data($info);        
        return $this->ok_js($info);
    }
    
    /**
     * 主要是当接口用，方便频道做二开
     * @param array $info
     * @return unknown
     */
    protected function format_data($info=[]){
        
        $this->model->addView($info['id']); //更新浏览量
        
        if($info['picurls']==''){
            $info['picurls'] = [];
        }
        
        $info['username'] = get_user_name($info['uid']);
        $info['create_time'] = date('Y-m-d H:i',$info['create_time']);
        $info['content'] = $info['full_content'] ;
        $info['content'] = str_replace('="/public/uploads', '="'.$this->request->domain().'/public/uploads', $info['content']);
        unset($info['full_content'],$info['sncode'],$info['password']);
        
        return $info;
    }
    
    /**
     * 商品的内容页展现形式
     * @param number $id
     * @return void|unknown|\think\response\Json
     */
    public function shop_index($id=0){
        $rs = $this->model->getInfoByid($id , true);
        if(empty($rs['picurls'])){
            $rs['picurls'] = [$rs['picurl']];
        }
        $rs['content'] = $rs['full_content'];
        
        $rs['type1'] = $this->format_shop_type('type1',$rs);
        $rs['type2'] = $this->format_shop_type('type2',$rs);
        $rs['type3'] = $this->format_shop_type('type3',$rs);
        $rs['content'] = str_replace('="/public/uploads', '="'.$this->request->domain().'/public/uploads', $rs['content']);
        unset($rs['full_content'],$rs['sncode'],$rs['password']);
        return $this->ok_js( $this->shop_format($rs) );
    }
    
    protected function shop_format($info=[]){
        $slider = [];
        foreach($info['picurls'] AS $rs){
            $slider[] = [
                'type' => 'image',
                'value' => $rs['picurl'],
            ];
        }
        $spec_array = [];
        if($info['type1']){
            $spec_array[] = $this->get_des('型号',$info['type1']['items'],'100');
        }
        if($info['type2']){
            $spec_array[] = $this->get_des('尺寸',$info['type2']['items'],'200');
        }
        if($info['type3']){
            $spec_array[] = $this->get_des('颜色',$info['type3']['items'],'300');
        }
        $sku = $this->get_sku($spec_array,$info['price'],isset($info['max_user'])?$info['max_user']:$info['num']);
        $data = [
            'basic' =>[
                'field' =>[
                    'goods_id' => $info['id'],
                    'uid' => $info['uid'],
                    'fid' => $info['fid'],
                    'view' => $info['view'],
                    'goods_name' => $info['title'],
                    'goods_short_name' => '',
                    'goods_image' => $info['picurl'],
                    'shop_price' => $info['price'],
                    'market_price' => $info['market_price'],
                    'detail_mobile' =>json_encode($slider),
                    'content' => $info['content'],
                    'is_sale' => 1,
                    'activity_price' => $info['price'],
                    'sales_volume' => 0,
                    'reference_price' => $info['market_price'],
                    'final_price' => $info['price'],
                    'integral' => 0,
                    'is_collect' => 0,
                ],
            ],
            'attribute' =>['show' => 0,'field' =>['text' => '详细参数'],'list' =>[
                ['label'=>'产地','value'=>'中国'],
                ['label'=>'性别','value'=>'男士'],
            ]],
            'ordertime' =>$this->get_ordertime($info),
            'spec' =>[
                'list' =>$spec_array,
                'field' =>[
                    'text' => '请选择 型号 尺寸',
                    'images' =>[],
                ],
                'show' => count($spec_array),
            ],
            'sku' =>['list' =>$sku],
            'banner' =>['show' => 0,'field' =>['image' => 'https://img12.360buyimg.com/img/s750x100_jfs/t1/153061/18/15107/99354/6001c03dE08c6dcab/fac73f98de764bc9.png.webp']],
            'countdown' =>[
                'show' => 1,
                'field' =>[
                    'label' => '结束时间',
                    'start' => 7200,
                ],
                'style' =>[
                    'background' => 'https://img12.360buyimg.com/img/s750x100_jfs/t1/119650/20/7754/47352/5ed632eaE8b4d8095/1e2456ef7139c95f.png',
                    'indicator' =>[
                        'color' => '#f71471',
                        'background' => '',
                    ],
                ],
            ],
            'slider' =>['list'=>$slider],
            'operation' =>[
                'list' => config('car_one')?[[
                    'text' => '立即下单',
                    'code' => 'buy',
                    'background' => '#ff6034',
                    'background2' => '#ee0a24',
                ]]:[
                    [
                        'text' => '加入购物车',
                        'code' => 'cart',
                        'background' => '#ffd01e',
                        'background2' => '#ff8917',
                    ],
                    [
                        'text' => '立即购买',
                        'code' => 'buy',
                        'background' => '#ff6034',
                        'background2' => '#ee0a24',
                    ],
                ],
            ],
        ];
        return $data;
    }
    
    protected function get_des($label='',$items=[],$type=''){
        $array = [];
        foreach($items AS $key=>$rs){
            $array[] = [
                'goods_attr_id' => $type.($key+1),
                'attr_id' => '152559205143625730',
                'attr_value' => $rs['name'],
                'attr_value_label' => $rs['name'],
                'price' => $rs['price'],
                'remark' => '',
                'attr_name' => $rs['name'],
                'input_type' => '',
                'image' => '',
                'num' => $rs['num'],
            ];
        }
        return [
            'label' => $label,
            'attr_name' => $label,
            'props' =>$array,
        ];
    }
    
    protected function get_ordertime($info = []){
        if (!function_exists('get_order_time')) {
            return ['show' => 0];
        }
        $listdb = [];
        $array = get_order_time($info);
        foreach($array AS $rs){
            $times = [];
            foreach($rs['time'] AS $vs){
                $times[] = [
                    'title'=>$vs['name'],
                    'value'=>$vs['id'],
                    'num'=>$vs['num'],
                    'price'=>$vs['price'],
                ];
            }
            $listdb[] = [
                'title'=>$rs['day']['title'],
                'value'=>$rs['day']['key'],
                'times'=>$times,
            ];
        }
        
        return [
            'show' => 2,
            'field' =>[
                'text' => '具体预约日期',
                'day_name' => '预期日期',
                'time_name' => '预约时间段',
                'max'=>$info['onlybuyone']?:$info['max_user'],
            ],
            'list' =>$listdb,
        ];
    }
    
    protected function get_sku($array=[],$price=0,$num=0){
        if (!$array[0]['props']) {  //不存在产品型号选择的情况
            return [$this->set_sku([],$price,$num,'','')];
        }
        $data = [];
        foreach($array[0]['props'] AS $rs){
            if ($array[1]) {
                foreach($array[1]['props'] AS $vs){
                    if($array[2]){
                        foreach($array[2]['props'] AS $qs){
                            $data[] = $this->set_sku($rs,$price,$num,
                                $rs['attr_value_label'].'，'.$vs['attr_value_label'].'，'.$qs['attr_value_label'],
                                $rs['goods_attr_id'].','.$vs['goods_attr_id'].','.$qs['goods_attr_id']);
                        }
                    }else{
                        $data[] = $this->set_sku($rs,$price,$num,
                            $rs['attr_value_label'].'，'.$vs['attr_value_label'],
                            $rs['goods_attr_id'].','.$vs['goods_attr_id']);
                    }
                }
            }else{
                $data[] = $this->set_sku($rs,$price,$num,$rs['attr_value_label'],$rs['goods_attr_id']);
            }
        }
        return $data;
    }
    
    protected function set_sku($rs=[],$price=0,$num='',$name='',$key=''){
        return [
            'goods_image' => '',
            'shop_price' => $rs['price']>0?$rs['price']:$price,
            'stock' => is_numeric($rs['num'])?$rs['num']:$num,
            'goods_sku_text' => $name,
            'goods_attr' => $key,
            'goods_sku_key' => $key,
            'goods_sku_id' => $key,
        ];
    }
    
    /**
     * 对商品属性进行转义处理
     * @param unknown $type 只支持type1 type2 type3 三个变量名的处理
     * @param unknown $rs
     * @return NULL|NULL|unknown[][][]|unknown[]
     */
    protected function format_shop_type($type,$rs){
        if($rs[$type]==''){
            return null;
        }
        $value = json_decode($rs[$type],true);
        $array = [];
        foreach ($value AS $vs){
            if($vs==''){
                continue;
            }
            list($name,$price,$num) = explode('|', $vs);
            $array[] = [
                'name'=>$name,
                'price'=>$price,
                'num'=>$num,
            ];
        }
        $model = get_field($rs['mid']);
        $data = [
            'title'=>$model[$type]['title'],
            'items'=>$array,
        ];
        return $array?$data:null;
    }
    
}













