<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase; 
use app\common\traits\AddEditList;

//商城订单管理
class Order extends AdminBase
{
    use AddEditList;
    protected $model;
    protected $list_items;
    protected $form_items;
    protected $tab_ext = [];
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'order');
        $this->model_content        = get_model_class($dirname,'content');
    }
    
    /**
     * 修改页
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id = null) {
        if (empty($id)) $this -> error('缺少参数');
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data = \app\common\field\Post::format_all_field($data,-1); //对一些特殊的自定义字段进行处理,比如多选项,以数组的形式提交的;
            if ($data['shipping_code']!='') {
                $data['shipping_status'] = 1;  //标志已发货
                $data['shipping_time'] = time();
            }
            $this->request->post($data);
        }else{
            $this->form_items = array_merge(
                    \app\common\field\Form::get_all_field(-1),  //自定义字段
                    [
                            ['text','shipping_code','物流单号'],
                            ['money','pay_money','应付金额'],
                    ]
                    );
            $info = $this -> getInfoData($id);
        }        
        return $this -> editContent($info);
    }
    
    /**
     * 列表页
     * @return unknown|mixed|string
     */
    public function index() {
        if ($this->request->isPost()) {
            //修改排序
            return $this->edit_order();
        }
        
        //列表定义显示的字段
        $list_field = \app\common\field\Table::get_list_field(-1);
        $array = [
            ['id', '订购商品', 'callback',function($id,$rs){
                return $this->get_shop($rs);
            }],
            ['uid', '用户帐号', 'username'],
            ['order_sn', '订单号', 'text'],
            ['pay_status', '支付与否', 'switch'],
            ['shipping_status', '发货与否', 'switch'],
            ['receive_status', '签收与否', 'switch'],
            ['totalmoney', '订单总额', 'text'],            
            ['create_time', '下单日期', 'date'],
        ];
        $this->list_items = $list_field ? array_merge($array,$list_field) : $array;
        
        //搜索字段
        $search = \app\common\field\Table::get_search_field(-1);
        $this -> tab_ext['search'] = array_merge(['uid'=>'用户uid'],$search);
        
        //筛选字段
        $this->tab_ext['filter_search'] = [
            'pay_status'=>['未支付','已支付'],
            'shipping_status'=>['未发货','已发货'],
            'receive_status'=>['未签收','已签收'],
        ];
        $array = \app\common\field\Table::get_filtrate_field(-1);
        
        //右边菜单
        $this -> tab_ext['right_button'] = [
                ['type'=>'delete'],
                ['type'=>'edit'],
                [
                        'icon'=>'fa fa-file-o',
                        'title'=>'详情',
                        'url'=>auto_url('show','id=__id__'),
                ],
        ];
        
        if(input('excel')){
            return $this->excel(500);
        }
        
        $listdb = self::getListData($this->get_search());
        $this->tab_ext['id_name'] = '订单ID';
        $this->assign('search_time','create_time');
        if (empty($this->get_template('search_inc'))) {
            $this->tab_ext['search_file'] = $this->get_template('admin@common/order_search');
        }
        
        $this->tab_ext = [
            'page_title'=>'订单记录',
            'top_button'=>[
                ['type'=>'delete'],
                [
                    'title'=>'导出Exce',
                    'icon'=>'fa fa-table',
                    'url'=>$this->weburl.(strstr($this->weburl,'?')?'&':'?').'excel=1&page=1',
                ],
            ],
        ];
        return $this -> getAdminTable($listdb);
    }
    
    /**
     * 多条件搜索
     * @return string[][]|unknown[]|mixed[]
     */
    protected function get_search()
    {
        $map = [];
        $detail = input();

        if (isset($detail['search_pay_status']) && $detail['search_pay_status']!=='') {
            $map['pay_status'] = $detail['search_pay_status'];
        }
        if (isset($detail['search_shipping_status']) && $detail['search_shipping_status']!=='') {
            $map['shipping_status'] = $detail['search_shipping_status'];
        }
        if (isset($detail['search_receive_status']) && $detail['search_receive_status']!=='') {
            $map['receive_status'] = $detail['search_receive_status'];
        }
        return $map;
    }
    
    /**
     * 导出excel表格数据
     * @param number $rows 每卷几条记录
     * @return unknown
     */
    protected function excel($rows = 500){
        $array = self::getListData($this->get_search(), $order='' ,$rows );
        $field_array = [
            'i'=>'序号',
            'uid'=>'用户UID',
            '_uid'=>[
                'key'=>'uid',   //处理上面key重复的问题
                'title'=>'用户帐号',
                'type'=>'username',
            ],
            'linkman'=>'联系人',
            'telphone'=>'联系电话',
            'address'=>'联系地址',
            'totalmoney'=>'优惠前的总金额',
            'pay_money'=>'实付金额',
            'order_sn'=>'订单号',
            'shop'=>[
                'title'=>'所购买的商品',
                'callback'=>function($v){
                    $array = [];
                    $detail = explode(',',$v);
                    foreach ($detail AS $ar){
                        list($shopid,$num,$type1,$type2,$type3) = explode('-',$ar);
                        $info = $this->model_content->getInfoByid($shopid);
                        $array[] = $info['title']."({$num}份)";
                    }                    
                    return implode("；\r\n", $array);
                 },
            ],
            'create_time'=>[
                'title'=>'下单日期',
                'type'=>'time',
            ],
            'shipping_time'=>[
                'title'=>'发货日期',
                'type'=>'time',
            ],
            'receive_time'=>[
                'title'=>'收货日期',
                'type'=>'time',
            ],
            'pay_time'=>[
                'title'=>'支付日期',
                'type'=>'time',
            ],
            'pay_status'=>[
                'title'=>'支付状态',
                'opt'=>['未付款','已付款'],
            ],
            'receive_status'=>[
                'title'=>'收货状态',
                'opt'=>['未签收','已签收'],
            ],
            'shipping_status'=>[
                'title'=>'发货状态',
                'opt'=>['未发货','已发货'],
            ],
            'shipping_name'=>'物流名称',
            'shipping_code'=>'物流单号',
            'user_note'=>'用户备注',
            'admin_note'=>'商家备注',
            'ifolpay'=>[
                'title'=>'付款渠道',
                'opt'=>['线下付款','在线支付'],
            ],
            'ifolpay'=>[
                'title'=>'付款渠道',
                'opt'=>['线下付款','在线支付'],
            ],
            'introducer_1'=>[
                'title'=>'直接推荐人',
                'type'=>'username',
            ],
            'introducer_2'=>[
                'title'=>'二级推荐人',
                'type'=>'username',
            ],
            'introducer_3'=>[
                'title'=>'三级推荐人',
                'type'=>'username',
            ],
        ];
        return $this->bak_excel($array,$field_array);
    }
    
    /**
     * 查看详情
     * @param number $id
     * @return \app\common\traits\unknown
     */
    public function show($id=0){
        
        $form_items = \app\common\field\Form::get_all_field(-1);    //自定义字段
        $this->form_items = array_merge(
                [
                    ['text','order_sn', '订单号'],
                    ['text','shipping_code', '物流单号'],
                    ['radio','shipping_status', '发货与否', '',['未发货','已发货']],
                    ['radio','receive_status', '签收与否','',['未签收','已签收']],
                    ['text','totalmoney', '订单总额'],
                    ['datetime','create_time', '下单日期'],
                    ['radio','pay_status', '支付与否','',['未支付','已支付']],
                    ['text','linkman', '收货联系人'],
                    ['text','telphone', '联系电话'],
                    ['text','address', '收货地址'],
                    ['callback','id', '订购商品','',function($id,$rs){
                        return $this->get_shop($rs);
                    }],
                ],
                $form_items
          );
        
        $info = getArray( $this->getInfoData($id) );
        $info = fun('field@format',$info,'','show','',$this->form_items);  //数据转义
        
        return $this->getAdminShow($info) ;
    }
    
    /**
     * 获取订单里的商品信息
     * @param array $rs
     * @return string
     */
    private function get_shop($rs=[]){
        $array = [];
        $detail = explode(',',$rs['shop']);
        foreach($detail AS $value){
            if ($value=='') {
                continue;
            }
            list($shopid,$num,$type1,$type2,$type3) = explode('-',$value);
            $_info = $this->model_content->getInfoByid($shopid);
            $more_about = [];
            if ($type1) {
                $_array = json_decode($_info['type1'],true);
                $more_about[] = explode('|',$_array[$type1-1])[0];
            }
            if ($type2) {
                $_array = json_decode($_info['type2'],true);
                $more_about[] = $_array[$type2-1];
            }
            if ($type3) {
                $_array = json_decode($_info['type3'],true);
                $more_about[] = $_array[$type3-1];
            }
            $array[] = "<a href='".iurl('content/show',['id'=>$shopid])."' target='_blank'>".$_info['title']."</a> "
                .($more_about?' （'.implode('/', $more_about).'） ':'').$num.' 件';
        }
        return implode("<br>", $array);
    }

    
    
}
