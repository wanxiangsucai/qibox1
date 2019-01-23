<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\Grouplog AS GrouplogModel;

use app\common\traits\AddEditList;

class GroupLog extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext;

	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new GrouplogModel();
	}
    
	public function pass($id,$status=0){
	    $info = GrouplogModel::get($id);
	    if (empty($info)) {
	        $this->error('资料不存在');
	    }
	    $array = [
	            'id'=>$id,
	            'status'=>$status,
	            'check_time'=>time(),
	    ];
	    $result = GrouplogModel::update($array);
	    if ($result) {
	        $data = [
	                'uid'=>$info['uid'],
	                'groupid'=>$info['gid'],
	        ];
	        if ($status==1) {	            
	            edit_user($data);
	            send_msg($info['uid'],"恭喜你，你申请的认证信息通过审核了","你申请认证的身份为:“".getGroupByid($info['gid'])."”被通过审核了");
	            $this->success('审核成功');
	        }else{
	            $data['groupid'] = 8;      //取消审核的话,就会把用户级别降为普通会员
	            edit_user($data);
	            send_msg($info['uid'],"很抱歉，你申请的认证信息被取消审核了","你申请认证的身份为:“".getGroupByid($info['gid'])."”被取消审核了");
	            $this->success('取消审核成功');
	        }	        
	    }else{
	        $this->error('更新失败!');
	    }
	}

	public function index($group=0)
    {
		$this->tab_ext = [
				'page_title'=>'用户认证升级管理',
		];

		$this->list_items = [
				['uid', '用户名', 'username'],              
				['gid', '申请用户组', 'callback',function($value){
				    return getGroupByid($value);
				}],
				['create_time', '申请日期', 'datetime'],
		        ['status', '审核与否', 'yesno',['否','是']],		
		        ['check_time', '审核日期', 'datetime'],
		        ['id', '审核操作', 'callback',function($value,$rs){
		            if($rs['status']){
		                $code = '<a href="'.urls('pass',['id'=>$value,'status'=>0]).'" title="点击取消审核"><i class="fa fa-ban"></i></a>';
		            }else{
		                $code = '<a href="'.urls('pass',['id'=>$value,'status'=>1]).'" title="点击通过审核"><i class="fa fa-check"></i></a>';
		            }
		            return $code;
		        }],
		];
		
		$this->tab_ext['top_button'] =[
		        [
		                'title' => '批量删除',
		                'icon'  => 'fa fa-times-circle',
		                'type'  => 'delete'
		        ],
		];
		
		$map = [];
		$data = $this->model->where($map)->order('id','desc')->paginate(50);		
		return $this->getAdminTable( $data );
    }

}
