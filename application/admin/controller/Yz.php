<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
use app\common\model\User as UserModel;

/**
 * 用户实名认证
 */
class Yz extends AdminBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items;
    protected $list_items;
    protected $tab_ext = [
        'id'=>false,                //用户数据表非常特殊，没有用id而是用uid
        'page_title'=>'用户实名认证',
    ];
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new UserModel();
    }
    
    /**
     * 用户列表
     */
    public function index() {
        $order = 'lastvist desc';
        $map = ['idcard'=>['<>','']];
        $this->list_items = [
            //['uid', '用户UID', 'text'],
            ['uid', '帐号', 'username'],
            ['truename', '真实名', 'text'],
            ['idcard', '证件号码', 'text'],
            ['uid','证件扫描件','callback',function($k,$v){
                $ext = $v['ext_field']?json_decode($v['ext_field'],true):[];
                if($ext['idcardpic']){
                    $ext['idcardpic'] = tempdir($ext['idcardpic']);
                }
                return "<a href='{$ext['idcardpic']}' target='_blank'><img src='{$ext['idcardpic']}' style='width:100px;height:75px;'></a>";
            }],
            ['idcard_yz', '审核操作', 'switch'],            
        ];
        
        $this -> tab_ext['search'] = ['username'=>'帐号','truename'=>'真实名','uid'=>'用户ID'];    //支持搜索的字段
        $this -> tab_ext['order'] = 'truename,idcard,uid';   //排序选择
        $this -> tab_ext['id'] = 'uid';    //用户数据表非常特殊，没有用id而是用uid ， 这里需要特别指定id为uid
        
        //筛选字段
        $this -> tab_ext['filter_search'] = [
            'groupid'=>getGroupByid(),
            'wx_attention'=>['未关注','已关注'],
            'yz'=>['未审核','已审核'],
            'idcard_yz'=>['未审核','已审核'],
        ];
        $this -> tab_ext['top_button'] = [];
        $this -> tab_ext['right_button'] = [];
 
        
        return $this -> getAdminTable(self::getListData($map, $order ));
    }
    
 
}
