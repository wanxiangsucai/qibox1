<?php
namespace plugins\marketing\member;

use app\common\controller\MemberBase; 
use plugins\marketing\model\RmbGetout;
use plugins\marketing\model\RmbInfull;
use plugins\marketing\model\RmbConsume;

class Rmb extends MemberBase
{
    //充值、消费记录
	public function index(){
		$map = [
                'uid'=>$this->user['uid']
        ];
		$data_list = RmbConsume::where($map)->order("id desc")->paginate(10);
		$data_list->each(function($rs,$key){
		    $rs['title'] = del_html($rs['about']);
		    return $rs;
		});
		    $pages = $data_list->render();
		    $listdb = getArray($data_list)['data'];
		    //给模板赋值变量
		    $this->assign('pages',$pages);
		    $this->assign('listdb',$listdb);
		return $this->pfetch();
	}
	/**
	 * 删除充值消费记录
	 */
	public function delete($id)
	{
	    if (RmbConsume::destroy([$id])) {
	        $this->success('删除成功','index');
	    }else{
	        $this->error('删除失败');
	    }
	}
    
	//充值
	public function add(){
	    if(IS_POST){
	        $data = $this->request->post();
	        if ( $data['money']<0.01 ) {
	            $this->error('充值金额不能小于0.01元');
	        }
	        $numcode = rands(10);
	        //直接跳转支付
	        post_olpay([
	                'money'=>$data['money'],
	                'return_url'=>purl('index'),
	                'banktype'=>'weixin',
	                'numcode'=>$numcode,
	                'callback_class'=>'',
	        ] , true);	
	    }
	    return $this->pfetch();
	}
	
	//提现
	public function getmoney(){
	    if (empty($this->user['rmb_pwd'])) {
	        $this->success('你还没有设置支付密码,请先设置支付密码!','pwd');;
	    }
	    if(IS_POST){
	        $data = $this->request->post();	        
	        if($this->user['rmb']<1){
	            $this->error("你当前可用余额小于1元,无法提现!");
	        }
	        
	        if(!$data['banktype']){
	            $this->error("请选择一个收款帐号!");
	        }elseif($data['money'] > $this->user['rmb']){
	            $this->error("提现金额不能大于你的可用余额");
	        }elseif($data['money']<1){
	            $this->error("提现金额不能小于1元");
	        }elseif($this->user['rmb_pwd']!=md5($data['pwd'])){
	            $this->error("支付密码不对!");
	        }
	        $data['uid'] = $this->user['uid'];
	        $data['username'] = $this->user['username'];
	        $data['posttime'] = time();
	        if ( RmbGetout::create($data) ) {
	            add_rmb($this->user['uid'],-$data['money'],$data['money'],'申请提现冻结');
	            $this->success('你的信息已提交，我们将于3个工作日内审核，并邮件通知您，请注意查收。如有疑问请联系客服',auto_url('marketing/rmb/index'));
	        } else {
	            $this->error('提现失败');
	        }
	    }
	    
	    $cfg = unserialize($this->user['config']);
	    $detail = explode("\r\n",$cfg['bank']);
	    foreach($detail AS $key=>$value){
	        if (empty($value)) {
	            unset($detail[$key]);
	        }
	    }
	    $this->assign('listdb',$detail);
	    return $this->pfetch();
	}
	
	//提现记录
	public function log(){
	    $listdb = RmbGetout::where('uid',$this->user['uid'])->order('id','desc')->paginate(20);
	    $listdb->each(function($rs,$key){
	        $rs['posttime'] = date('Y-m-d H:i',$rs['posttime']);
	        $rs['replytime'] = $rs['replytime']?date('Y-m-d H:i',$rs['replytime']):'';
	        return $rs;
	    });
	    $pages = $listdb->render();	    
	    $this->assign('listdb',$listdb);
	    $this->assign('pages',$pages);	    
	    return $this->pfetch();
	}
	
	//收款帐号设置
	public function edit(){
	    $cfg = unserialize($this->user['config']);
	    if(IS_POST){	        
	        $data = $this->request->post();
	        $cfg['alipay_id'] =  $data['alipay_id'];
	        $cfg['bank'] =  $data['bank'];
	        $cfg = serialize($cfg);
	        $array = [
	                'uid'=>$this->user['uid'],
	                'config'=>$cfg,
	        ];
	        if ( edit_user($array) ) {
	            $this->success('更新成功');
	        } else {
	            $this->error('更新失败');
	        }
	    }
	    $this->assign('cfg',$cfg);
	    return $this->pfetch();
	}
	
	//修改余额支付密码
	public function pwd(){
	    if(IS_POST){
	        $data = $this->request->post();
	        if($data['pay_pwd']!=$data['pay_pwd2']){
	            $this->error('两次输入密码不一致');
	        }elseif($this->user['rmb_pwd'] && $this->user['rmb_pwd']!=md5($data['old_pwd'])){
	            $this->error('原密码不正确');
	        }
	        $array = [
	                'uid'=>$this->user['uid'],
	                'rmb_pwd'=>md5($data['pay_pwd']),
	        ];
	        if ( edit_user($array) ) {
	            $this->success('更新成功');
	        } else {
	            $this->error('更新失败');
	        }
	    }
	    return $this->pfetch();
	}
}
