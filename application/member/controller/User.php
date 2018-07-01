<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
use app\common\controller\MemberBase;
use app\common\traits\AddEditList;

class User extends MemberBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items;
    protected $tab_ext;
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new UserModel(); 
    }
    
    public function index($uid=0)
    {
        if($uid){
            $info = UserModel::getById($uid);
            if(empty($info)){
                $this->error('该用户不存在!');
            }
        }else{
            $info = $this->user;
        }
        $this->assign('info',$info);
        $this->assign('uid',$info['uid']);
        return $this->fetch();
    }

    public function delete()
    {
        die('禁止访问!');
    }
    public function add()
    {
        die('禁止访问!');
    }
    
    public function edit()
    {
        $info = $this->model->get_info($this->user['uid']);
        
        $this->form_items = [
                ['hidden', 'uid'],
                ['text', 'password', '密码','留空则代表不修改密码'],
                ['text', 'nickname', '昵称'],
                ['text', 'email', '邮箱'],
                ['radio', 'sex', '性别','',[0=>'保密',1=>'男',2=>'女']],
                ['jcrop', 'icon', '头像'],
        ];
        
        if (IS_POST) {
            
            $data = get_post('post');
            
            if ($data['uid']!=$info['uid']) {
                $this->error('你不能修改别人的资料');
            }

            // 验证
            if(!empty($this->validate)){
                // 验证
                $result = $this->validate($data, $this->validate);
                if(true !== $result) $this->error($result);
            }
            
            //密码要重新加密，所以要特别处理
            if (empty($data['password'])) {
                unset($data['password']);
            }

            //form_items之外的项目不允许伪造表单修改
            $allow = [];            
            foreach($this->form_items AS $key=>$ar){
                $allow[] = $ar[1];
            }
            foreach($data AS $key=>$value){
                if(!in_array($key, $allow)){
                    unset($data[$key]);
                }
            }            

            $array = [
                    'uid'=>$info['uid'],
                    'nickname'=>$data['nickname'],
                    'sex'=>$data['sex'],
                    'email'=>$data['email'],
                    'icon'=>$data['icon'],
            ];
            
            if ( $this->model->edit_user($data) ) {
                $this->success('修改成功');
            } else {
                $this->error('数据更新失败');
            }
        }
        $info['password']='';
        return $this->editContent($info);
    }
}
