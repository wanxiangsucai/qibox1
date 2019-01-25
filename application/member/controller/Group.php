<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
use app\common\controller\MemberBase;
use app\common\traits\AddEditList;
use app\common\fun\Cfgfield;
use app\common\field\Post AS FieldPost;
use app\common\model\Grouplog AS GrouplogModel;

class Group extends MemberBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items;
    protected $tab_ext;
    protected $money_name='积分';
    protected $money_dw='个';
    
    protected function _initialize()
    {
        parent::_initialize();
        //$this->model = new UserModel();
        if($this->webdb['up_group_use_rmb']){
            $this->money_name='金额';
            $this->money_dw='元';
        }
    }
    
    public function index()
    {
        $data_list = getGroupByid(null,false);
        foreach($data_list AS $gid=>$rs){
            if($rs['type']==0&&$gid!=8){
                $groupdb[] = $rs;
            }
        }        
        $this->assign('groupdb',$groupdb);
        $this->assign('money_dw',$this->money_dw);
        $this->assign('money_name',$this->money_name);
        return $this->fetch();
    }

    public function buy($gid=0)
    {
        if ($gid<1) {
            $this->error('请选择要认证的用户组');
        }
        $data_list = getGroupByid(null,false);
        $ginfo = $data_list[$gid];
        if (empty($ginfo)){
            $this->error('用户组不存在!');
        }elseif ($ginfo['type']) {
            $this->error('系统组,不可以购买');
        }
        
        if($this->admin){
            $this->error('你是管理员,级别很高了,无须认证升级');
        }
        
        if($this->webdb['up_group_use_rmb']){
            if($this->user['rmb']<$ginfo['level']){
                $this->error('你的余额不足: '.$ginfo['level'] .'元，不能申请当前认证，请先充值！',purl('marketing/rmb/add'));
            }
        }else{
            if($this->user['money']<$ginfo['level']){
                $this->error('你的积分不足: '.$ginfo['level'] .'个，不能申请当前认证，请先充值！',purl('marketing/jifen/add'));
            }
        }        
        
        $this->form_items = Cfgfield::get_form_items($gid,'upgroup');
        if (empty($this->form_items)) { //没有需要填写的项目  , 会终止掉下面所有操作          
            $this->post($gid,$ginfo['level']);
        }
        
        if ($this->request->isPost()) {
            $data = $this->request->post();            
            $data = FieldPost::format_php_all_field($data,$this->form_items);            
            //form_items之外的项目不允许伪造表单修改
            $allow = [];
            foreach($this->form_items AS $key=>$ar){
                $allow[] = $ar[1];
            }
            foreach($data AS $key=>$value){
                if(!in_array($key, $allow)){
                    unset($data[$key]);
                }elseif($value==''){
                    $this->error('每一项都要必填!');
                }
            }
            $data['uid'] = $this->user['uid'];
            if ( UserModel::edit_user($data) ) {
                $this->post($gid,$ginfo['level']);
            } else {
                $this->error('数据更新失败');
            }
        }
        
        $this->assign('money_name',$this->money_name);
        $this->assign('money_dw',$this->money_dw);
        
        $this->tab_ext['page_title'] = '申请认证为: '.$ginfo['title'].($ginfo['level'] ? " 本次认证需要消费".$this->money_name." {$ginfo['level']} ".$this->money_dw:' 本次认证免费');
        $info = $this->user;
        return $this->editContent($info);
    }
    
    protected function post($gid=0,$money=0){
        $array = [
                'uid'=>$this->user['uid'],
                'gid'=>$gid,
        ];
        $info = GrouplogModel::get($array);
        if ($info && empty($info['status'])) {
            $this->error('你之前的认证资料还没通过审核,暂时不能重复申请!');
        }
        $array['create_time'] = time();
        $result = GrouplogModel::create($array);
        if ($result) {
            if ($money>0) {
                if($this->webdb['up_group_use_rmb']){
                    add_rmb($this->user['uid'],-$money,0,'认证升级用户身份');
                }else{
                    add_jifen($this->user['uid'],-$money,'认证升级用户身份');
                }                
            }
            $this->success('信息已提交,请等待管理员审核!',urls('index'));
        }else{
            $this->error('数据提交失败');
        }
    }
    
    public function delete(){
        die('禁止访问!');
    }
    public function add(){
        die('禁止访问!');
    }
    public function edit(){
        die('禁止访问!');
    }
    
}
