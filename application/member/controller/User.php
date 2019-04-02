<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
//use app\common\controller\MemberBase;
use app\common\controller\IndexBase;
use app\common\traits\AddEditList;
use app\common\fun\Cfgfield;
use app\common\field\Post AS FieldPost;

class User extends IndexBase
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
        }elseif (empty($uid)&&empty($this->user)){
            $this->error('UID不存在!');
        }else{
            $info = $this->user;
        }
        
        $key = 'visit_homepage_' . $uid . '_' . $this->user['uid'];
        if(time()-get_cookie($key)>600){     //每10分钟更新一次
            set_cookie($key, time());
            UserModel::where('uid','=',$uid)->setInc('view',1);
            cache('user_'.$uid,null);
        }

        $this->get_hook('view_homepage',$info , $this->user);
        hook_listen( 'view_homepage' , $info  , $this->user);      //监听浏览主页
		
        
        $info['introducer_1'] = $info['introducer_1']?get_user_name($info['introducer_1']):'';
        $info['introducer_2'] = $info['introducer_2']?get_user_name($info['introducer_2']):'';
        $info['introducer_3'] = $info['introducer_3']?get_user_name($info['introducer_3']):'';
        
        //自定义字段
        $f_array = Cfgfield::get_form_items($info['groupid']
                ,$this->user['uid']==$info['uid']?'admin':'view' //自己看自己的就相当于管理员权限
                ,$this->user);
        
        $this->assign('info',$info);
        $this->assign('uid',$info['uid']);
        $this->assign('f_array',$f_array);        
        $template = get_group_tpl('page',$info['groupid']);
        return $this->fetch($template);
    }

    public function delete(){
        die('禁止访问!');
    }
    public function add(){
        die('禁止访问!');
    }
    
    public function edit()
    {
        if (empty($this->user)){
            $this->error('你还没登录!');
        }
        $info = $this->model->get_info($this->user['uid']);
        
        $this->form_items = [
                ['hidden', 'uid'],
                ['text', 'username', '帐号',$this->webdb['edit_username_money']?'需要消费积分 '.intval($this->webdb['edit_username_money']).' 个':'请不要随意修改'],
                ['text', 'password', '密码','留空则代表不修改密码'],
                ['text', 'nickname', '昵称'],
                ['text', 'email', '邮箱'],
                ['radio', 'sex', '性别','',[0=>'保密',1=>'男',2=>'女']],
                ['jcrop', 'icon', '头像'],
                //['textarea', 'introduce', '自我介绍'],
        ];
        //某用户组下面的所有参数选项
        $array = Cfgfield::get_form_items($info['groupid'],'member');
        if ($array) {
            $this->form_items = array_merge($this->form_items,$array);
        }
        
        if ($this->request->isPost()) {
            
            $data = $this->request->post();
            
            if ($data['uid']!=$info['uid']) {
                $this->error('你不能修改别人的资料');
            }
            
            $data = FieldPost::format_php_all_field($data,$this->form_items);

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

//             $array = [
//                     'uid'=>$info['uid'],
//                     'nickname'=>$data['nickname'],
//                     'sex'=>$data['sex'],
//                     'email'=>$data['email'],
//                     'icon'=>$data['icon'],
//             ];
            
            if ($info['username']!=$data['username']) {
                $data['username'] = str_replace(['|',' ','',"'",'"','/','*',',','~',';','<','>','$',"\\","\r","\t","\n","`","!","?","%","^"],'',$data['username']);
                if ($info['money'] < $this->webdb['edit_username_money']) {
                    $this->error('你的积分不足 '.intval($this->webdb['edit_username_money']).' 个,不能修改帐号!');
                }elseif($data['username']==''){
                    $this->error('帐号不能为空!');
                }elseif(strlen($data['username'])>50||strlen($data['username'])<2){
                    $this->error('帐号不能少于2个字符或大于50个字符!');
                }elseif (UserModel::check_userexists($data['username'])){
                    $this->error('当前帐号已经存了,请更换一个!');
                }
                if(config('webdb.forbidRegName')!=''){
                    $detail = str_array(config('webdb.forbidRegName'));
                    if(in_array($data['username'], $detail)){
                        $this->error('请换一个用户名,当前用户名不允许使用'.$data['username']);
                    }
                }
                if($this->webdb['edit_username_money']){
                    add_jifen($info['uid'], -abs($this->webdb['edit_username_money']),'修改帐号消费');
                }
               // $array['username'] = $data['username'];
            }
            $data['introduce'] && $data['introduce'] = preg_replace("/(javascript|iframe|script |\/script)/i", '.\\1', $data['introduce']);
            
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
