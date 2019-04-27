<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 
use app\common\traits\AddEditList;
use app\common\model\User as UserModel;
//use app\common\model\Groupcfg AS GroupcfgModel;
use app\common\fun\Cfgfield;
use app\common\field\Post AS FieldPost;

/**
 * 用户管理
 */
class Member extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items;
	protected $list_items;
	protected $tab_ext = [
				'id'=>false,                //用户数据表非常特殊，没有用id而是用uid
				'page_title'=>'会员资料管理',
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
	    $order = 'uid desc';
	    $map = [];
	    $this->list_items = [
	        //['uid', '用户UID', 'text'],
	        ['uid', '用户名', 'username'],
	        ['groupid', '用户组', 'select',getGroupByid()],
	        ['regdate', '注册日期', 'datetime'],
            ['lastvist', '登录日期', 'datetime'],
	        ['regip', '注册IP', 'text'],
	        ['money', '积分', 'text'],
	        ['rmb', '余额', 'text'],
	        ['yz', '审核', 'switch'],
	        ['wx_attention', '关注公众号', 'callback',function($v,$rs){
	            if ($rs['weixin_api']==''&&$rs['wx_attention']==1) {
	                $rs['wx_attention'] = 0;
	                edit_user(['uid'=>$rs['uid'],'wx_attention'=>0]);
	            }
	            $url = purl('weixin/check/ifgz',[],'index');
	            if ($rs['weixin_api']&&$rs['wx_attention']) {
	                $code = '<i class="fa fa-check-circle-o" id="uid-'.$rs['uid'].'" style="color:red;"></i>';
	            }else{
	                $code = '<i class="fa fa-ban" id="uid-'.$rs['uid'].'" style="color:#666;"></i>';
	            }
	            $code .=<<<EOT
<script type="text/javascript">
if("{$rs['weixin_api']}"!=""){
	$.get("{$url}?type=set&uid={$rs['uid']}",function(res){
		if(res.code==0){
			$("#uid-{$rs['uid']}").removeClass("fa-ban");
            $("#uid-{$rs['uid']}").removeClass("fa-check-circle-o");
			$("#uid-{$rs['uid']}").addClass("fa-check-circle-o");
			$("#uid-{$rs['uid']}").css({"color":"red"});
		}else if(res.code==1){
            $("#uid-{$rs['uid']}").css({"color":"#666"});
        }else{
            //同步失败
        }
	});
}
</script>
EOT;
	            return $code;
	        }],
	        ['uid','登录','callback',function($k,$v){
	            $url = urls('edit',['type'=>'login','id'=>$k]);
	            return "<a target='_blank' href='$url' class='fa fa-child' onclick='return confirm(\"你确认要登录他的帐号吗?\")' title='你确认要登录他的帐号吗?'>登录</a>";
	        }],
	    ];	    
	    
	    $this -> tab_ext['search'] = ['username'=>'用户名','uid'=>'用户ID','regip'=>'注册IP'];    //支持搜索的字段
	    $this -> tab_ext['order'] = 'money,rmb,uid,regdate,lastvist';   //排序选择
	    $this -> tab_ext['id'] = 'uid';    //用户数据表非常特殊，没有用id而是用uid ， 这里需要特别指定id为uid
	    
	    //筛选字段
	    $this -> tab_ext['filter_search'] = [
	            'groupid'=>getGroupByid(),
	            'wx_attention'=>['未关注','已关注'],
	            'yz'=>['未审核','已审核'],
	    ];
	    $this -> tab_ext['top_button'] = [
	        [
	                'type'=>'add',
	                'title'=>'创建新用户',
	        ],
	    ];
	    if ($this->user['groupid']==3) {   //超管才有批量删除功能
	        $this -> tab_ext['top_button'][]=[
	                'type'=>'delete',
	                'title'=>'批量删除用户',	 
	        ];
	    }
	    
	    return $this -> getAdminTable(self::getListData($map, $order ));
	}
	
	/**
	 * 登录他人的帐号
	 * @param number $id
	 */
	public function login($id=0){
	    $result = UserModel::login($id,$password='',$cookietime=null,$not_pwd=true,$type='uid');
	    if (is_array($result)) {
	        return $this->success('登录成功',get_url('member'));
	    }else{
	        return $this->error('登录失败');
	    }
	}
	
	/**
	 * 修改用户资料
	 * @param number $id 用户UID
	 */
	public function edit($id = 0,$type='')
	{
	    if(empty($id)) $this->error('缺少参数');
	    
	    if ($type=='login') {
	        $this->login($id);
	    }
    	
	    $info = $this->model->get_info($id);
	    
	    $this->form_items = [
	            ['hidden', 'uid'],
	            ['static', 'username', '用户名','用户名不可修改'],
	            ['text', 'password', '密码','留空则代表不修改密码,之前加密后的密码是：'.$info['password']],
	            ['select', 'groupid', '用户组','',getGroupByid()],
	        ['datetime', 'group_endtime', '用户组有效期','留空则长期有效,除普通用户组外其它任何用户组（包括超管及黑名单）都可以设置有效期，过期后统一归为普通用户组'],
	            ['text', 'nickname', '昵称'],
	            ['text', 'email', '邮箱'],
	            ['text', 'weixin_api', '微信接口ID'],
	            ['text', 'qq_api', 'QQ接口ID'],
	            ['text', 'wxapp_api', '小程序接口ID'],
	            ['radio', 'sex', '性别','',[0=>'保密',1=>'男',2=>'女']],
	            ['jcrop', 'icon', '头像'],
	            ['number', 'money', '积分'],
	            ['money', 'rmb', '可用余额'],
	            ['money', 'rmb_freeze', '冻结余额'],
	            ['text', 'rmb_pwd', '支付密码','这是加密后的效果,要修改的话,只须输入原始密码即可'],
	           ['text', 'truename', '真实名'],
	        ['text', 'mobphone', '手机号'],
	        ['text', 'idcard', '证件号'],
	        ['image', 'idcardpic', '证件扫描件'],
	        ['radio', 'email_yz', '邮箱验证与否','',['未验证','已验证']],
	        ['radio', 'mob_yz', '手机验证与否','',['未验证','已验证']],
	        ['radio', 'idcard_yz', '证件验证与否','',['未验证','已验证']],
	        ['number', 'introducer_1', '直接推荐人'],
	        ['number', 'introducer_2', '2级推荐人'],
	        ['number', 'introducer_3', '3级推荐人'],
	    ];
	    
	    //某用户组下面的所有参数选项
	    $array = Cfgfield::get_form_items($info['groupid']);
	    if ($array) {
	        $this->form_items = array_merge($this->form_items,$array);
	    }
	    
	    if ($this->request->isPost()) {    //修改入库处理
	        $data = $this->request->post();
	        if(SUPER_ADMIN!==true&&$data['groupid']==3&&$this->user['groupid']!=3){
	            $this->error('你无权修改超管资料!');
	        }
	        // 验证
	        if(!empty($this->validate)){
	            // 验证
	            $result = $this->validate($data, $this->validate);
	            if(true !== $result) $this->error($result);
	        }
	        $data = FieldPost::format_php_all_field($data,$this->form_items);
	        if ($data['rmb_pwd'] && strlen($data['rmb_pwd'])!=32) {
	            $data['rmb_pwd'] = md5($data['rmb_pwd']);
	        }
	        if ( $this->model->edit_user($data) ) {
	            $this->success('修改成功', 'index');
	        } else {
	            $this->error('修改无效');
	        }
	    }
	    
	    $info['password']='';
	    if (empty($info['introducer_1'])) {
	        $info['introducer_1']='';
	    }
	    if (empty($info['introducer_2'])) {
	        $info['introducer_2']='';
	    }
	    if (empty($info['introducer_3'])) {
	        $info['introducer_3']='';
	    }
	    return $this->editContent($info);
	}
	
	/**
	 * 后台快速添加新用户
	 */
	public function add()
	{
	    if (IS_POST) {	        
	        $data = get_post('post');
	        if(SUPER_ADMIN!==true&&$data['groupid']==3&&$this->user['groupid']!=3){
	            $this->error('你无权创建超管!');
	        }
	        // 验证
	        if(!empty($this->validate)){
	            // 验证
	            $result = $this->validate($data, $this->validate);
	            if(true !== $result) $this->error($result);
	        }	        
	        $uid = $this->model->register_user($data);
	        
	        if ( is_numeric($uid) ) {
	            $this->success('添加成功', 'index');
	        } else {
	            $this->error('添加失败:'.$uid);
	        }
	    }
	    
	    $this->form_items = [
	            ['text', 'username', '用户名','创建后不可修改'],
	            ['text', 'password', '密码'],
	            ['select', 'groupid', '用户组','',getGroupByid()],
	            ['text', 'email', '邮箱'],
	    ];
	    return $this->addContent();
	}
	
	/**
	 * 删除用户 用户的其它资料这里没做删除，需要 借助钩子做删除处理 user_delete_end
	 * @param unknown $ids
	 */
	public function delete($ids = null)
	{
	    if(empty($ids)) $this->error('缺少参数');	    
	    $ids = is_array($ids) ? $ids : [$ids];	    
	    $num = 0;
	    foreach($ids AS $uid){
	        $data = UserModel::get_info($uid);
	        if($data['groupid']==3&&$this->user['groupid']!=3){
	            $this->error('你无权删除超管!');
	        }
	        if($this->model->delete_user($uid)){
	            $num++;
	        }
	    }
	    
	    if( $num ){
	        $this->success('成功删除 '.$num.' 个用户', 'index');
	    }else{	        
	        $this->error('删除失败');
	    }
	}
}
