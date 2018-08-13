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
    protected $tab_ext = [
            'page_title'=>'订单记录',
            'top_button'=>[ ['type'=>'delete']],
    ];
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'order');
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
            $this->request->post($data);
        }else{
            $this->form_items = array_merge(
                    \app\common\field\Form::get_all_field(-1),  //自定义字段
                    [
                            ['text','shipping_code','物流单号'],
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
                ['order_sn', '订单号', 'text'],
                ['pay_status', '支付与否', 'switch'],
                ['totalmoney', '订单总额', 'text'],
                ['uid', '用户帐号', 'username'],
                ['create_time', '下单日期', 'text'],
        ];
        $this->list_items = $list_field ? array_merge($array,$list_field) : $array;
        
        //搜索字段
        $search = \app\common\field\Table::get_search_field(-1);
        $this -> tab_ext['search'] = array_merge(['uid'=>'用户uid'],$search);
        
        //筛选字段
        $this->tab_ext['filter_search'] = \app\common\field\Table::get_filtrate_field(-1);
        
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
        
        $listdb = self::getListData($map = [], $order = []);
        return $this -> getAdminTable($listdb);
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
                        ['text','totalmoney', '订单总额'],
                        ['date','create_time', '下单日期'],
                        ['radio','pay_status', '支付与否','',['未支付','已支付']],
                ],
                $form_items
         );
        
        $info = getArray( $this->getInfoData($id) );
        $info = fun('field@format',$info,'','show','',$this->form_items);  //数据转义
        
        return $this->getAdminShow($info) ;
    }

    
    
}
