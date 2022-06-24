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
	public function index($type='') {
	    $order = 'uid desc';
	    $this->list_items = [
	        //['uid', '用户UID', 'text'],
	        ['uid', '用户名', 'username'],
	        ['icon', '头像', 'image'],
	        ['groupid', '用户组', 'select',getGroupByid()],
	        ['grouptype', '角色', 'select',get_role()],
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
	    
	    //支持搜索的字段
	    $this -> tab_ext['search'] = [
	        'username'=>'用户名',
	        'nickname'=>'昵称',
	        'truename'=>'真实姓名',
	        'mobphone'=>'手机号',
	        'regip'=>'注册IP',
	        'weixin_api'=>'微信API',	        
	    ];
	    $this -> tab_ext['order'] = 'money,rmb,uid,regdate,lastvist';   //排序选择
	    $this -> tab_ext['id'] = 'uid';    //用户数据表非常特殊，没有用id而是用uid ， 这里需要特别指定id为uid
	    $this -> tab_ext['help_msg'] = '1、这里的权限很大,建议设置为只有超管才能使用
<br>2、这里修改财务不会有记录,要有记录的话,请分配财务人员使用插件中心>财务与积分功能><a href="'.purl('marketing/member/index',[],'admin').'" style="color:red;">会员积分财务管理</a> 在那里操作
<br>3、excel导出注意事项,鉴于负载效率考虑,每次只能导出2000条记录，要导出更多的话，请点击下一页再重复导出。';
	    
	    //筛选字段
	    $this -> tab_ext['filter_search'] = [
	            'groupid'=>getGroupByid(),
	            'grouptype'=>get_role(),
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
	    $weburl = get_url('location');
	    $this -> tab_ext['top_button'][]=[
	            'type'=>'excel',
	            'title'=>'导出excel表格',
	            'icon'=>'fa fa-table',
	            'url'=>$weburl . (strstr($weburl,'?')?"&":'?').'type=excel&page=1',
	    ];
	    
	    if ($type=='excel') {
	        return $this->excel($order);
	    }
	    
	    return $this -> getAdminTable(self::getListData($this->get_search(), $order ));
	}
	
	/**
	 * 多条件搜索
	 * @return string[][]|unknown[]|mixed[]
	 */
	protected function get_search()
	{
	    $map = [];
	    $detail = input();
	    if (isset($detail['search_bindphone']) && $detail['search_bindphone']!=='') {
	        $map['mob_yz'] = $detail['search_bindphone'];
	    }
	    if (isset($detail['search_bindwxmp']) && $detail['search_bindwxmp']!=='') {
	        $map['wx_attention'] = $detail['search_bindwxmp'];
	    }
	    if (isset($detail['search_bindwxlogin']) && $detail['search_bindwxlogin']!=='') {
	        $map['weixin_api'] = ['<>',''];
	    }
	    if (isset($detail['search_bindqqlogin']) && $detail['search_bindqqlogin']!=='') {
	        $map['qq_api'] = ['<>',''];
	    }
	    if (isset($detail['search_sex']) && $detail['search_sex']!=='') {
	        $map['sex'] = $detail['search_sex'];
	    }
	    if (isset($detail['search_bindidcard']) && $detail['search_bindidcard']!=='') {
	        $map['idcard_yz'] = $detail['search_bindidcard'];
	    }
	    if (isset($detail['search_faceyz']) && $detail['search_faceyz']!=='') {
	        $map['weixin_yz'] = $detail['search_faceyz'];
	    }
	    if (isset($detail['search_groupid']) && $detail['search_groupid']!=='') {
	        $map['groupid'] = $detail['search_groupid'];
	    }
	    return $map;
	}
	
	protected function excel($order='',$rows = 500){
	    $array = self::getListData($this->get_search(), $order ,$rows );
// 	    $map = $this->get_search();
// 	    $order = $this->getOrder() ? $this->getOrder() : 'uid ASC' ;
// 	    $array = UserModel::where($map)->order($order)->paginate($rows);
	    $field_array = [
	        'i'=>'序号',
	        'uid'=>'用户UID',
	        'username'=>'用户帐号',
	        'nickname'=>'用户昵称',
	        'qq_api'=>[
	            'title'=>'是否绑定QQ',
	            'callback'=>function($v){
	               return $v?'未绑定':'已绑定';
	            },
	        ],
	        'groupid'=>[
	            'title'=>'所属用户组',
	            'opt'=>getGroupByid(),
	        ],
	        'grouptype'=>[
	            'title'=>'用户角色',
	            'opt'=>get_role(),
	        ],
	        'wx_attention'=>[
	            'title'=>'是否关注公众号',
	            'opt'=>['未关注','已关注'],
	        ],
	        'weixin_api'=>[
	            'title'=>'是否绑定公众号登录(不一定关注)',
	            'callback'=>function($v){
	                return $v?'未绑定':'已绑定';
	            },
	        ],
	        'wxapp_api'=>[
	            'title'=>'是否绑定小程序登录',
	            'callback'=>function($v){
	               return $v?'未绑定':'已绑定';
	            },
	        ],
	        'wxopen_api'=>[
	            'title'=>'是否绑定APP微信登录',
	            'callback'=>function($v){	                
	                   return $v?'未绑定':'已绑定';
	               },
	        ],
	        'money'=>'可用积分',
	        'rmb'=>'可用余额',
	        'lastip'=>'最后访问IP',
	        'lastvist'=>[
	            'title'=>'最后访问时间',
	            'type'=>'time',
	        ],	        
	        'regdate'=>[
	            'title'=>'注册日期',
	            'type'=>'time',
	        ],
	        'group_endtime'=>[
	            'title'=>'用户组截止日期',
	            'type'=>'time',
	        ],
	        'regip'=>'注册IP',
	        'sex'=>[
	            'title'=>'性别',
	            'opt'=>['未知','先生','女士'],
	        ],
	        'introduce'=>'介绍签名',
	        'address'=>'联系地址',
	        'mobphone'=>'手机号',
	        'idcard'=>'证件号码',
	        'truename'=>'真实姓名',
	        'emai'=>'邮箱',
	        'email_yz'=>[
	            'title'=>'验证邮箱与否',
	            'opt'=>['未验证','已验证'],
	        ],
	        'mob_yz'=>[
	            'title'=>'验证手机与否',
	            'opt'=>['未验证','已验证'],
	        ],
	        'idcard_yz'=>[
	            'title'=>'证件验证与否',
	            'opt'=>['未验证','已验证'],
	        ],
	        'subscribe_mp'=>[
	            'title'=>'订阅公众号消息与否',
	            'opt'=>['未订阅','已订阅'],
	        ],
	        'subscribe_wxapp'=>[
	            'title'=>'订阅小程序消息与否',
	            'opt'=>['未验证','已验证'],
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
	 * 登录他人的帐号
	 * @param number $id
	 */
	public function login($id=0,$url=''){
	    $result = UserModel::login($id,$password='',$cookietime=null,$not_pwd=true,$type='uid');
	    if (is_array($result)) {
	        return $this->success('登录成功',$url?:get_url('member'));
	    }else{
	        return $this->error('登录失败');
	    }
	}
	
	private function get_user($uid=0){
	    if ($uid) {
	        $user = get_user($uid);
	        return "<a href=\"".get_url('user',$uid)."\" target=\"_blank\">帐号:{$user['username']} 昵称:{$user['nickname']}</a>";
	    }
	    return '';
	}
	
	/**
	 * 修改用户资料
	 * @param number $id 用户UID
	 */
	public function edit($id = 0,$type='',$url='')
	{
	    if(empty($id)) $this->error('缺少参数');
	    
	    if ($type=='login') {
	        $this->login($id,$url);
	    }
    	
	    $info = $this->model->get_info($id,'uid',false);
	    
	    $this->form_items = [
	            ['hidden', 'uid'],
	            ['text', 'username', '用户名','用户名请不要随意修改'],
	            ['text', 'password', '密码','留空则代表不修改密码,之前加密后的密码是：'.$info['password']],
	            ['select', 'groupid', '用户组','',getGroupByid()],
	        ['radio', 'grouptype', '用户角色','',get_role()],
	        ['datetime', 'group_endtime', '用户组有效期','留空则长期有效,除普通用户组外其它任何用户组（包括超管及黑名单）都可以设置有效期，过期后统一归为普通用户组'],
	            ['text', 'nickname', '昵称'],
	            ['text', 'email', '邮箱'],
	            ['text', 'weixin_api', '微信接口ID'],
	            ['text', 'qq_api', 'QQ接口ID'],
	            ['text', 'wxapp_api', '小程序接口ID'],
	            ['text', 'unionid', '开放平台接口ID'],
	            ['text', 'unionid', '开放平台接口ID'],
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
	        ['radio', 'wx_attention', '是否关注公众号','',['未关注','已关注']],
	        ['radio', 'yz', '帐号验证','',['未验证','已验证']],
	        ['number', 'introducer_1', '直接推荐人',$this->get_user($info['introducer_1'])],
	        ['number', 'introducer_2', '2级推荐人',$this->get_user($info['introducer_2'])],
	        ['number', 'introducer_3', '3级推荐人',$this->get_user($info['introducer_3'])],
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
