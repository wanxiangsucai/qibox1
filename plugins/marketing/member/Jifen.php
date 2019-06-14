<?php
namespace plugins\marketing\member;

use app\common\controller\MemberBase; 
use plugins\marketing\model\Moneylog AS Model;
use plugins\marketing\model\RmbInfull;

class Jifen extends MemberBase
{
    public function index()
    {
        $map = [
                'uid'=>$this->user['uid']
        ];
        $data_list = Model::where($map)->order("id desc")->paginate(15);
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
    
    private function pay_end($numcode=''){
        $info = RmbInfull::get(['numcode'=>$numcode]);
        if ($info['ifpay']==0) {
            $this->error('你还没有支付成功');
        }
        if ($this->user['rmb']>=$info['money']){
            //$this->user['rmb'] = $this->user['rmb'] - abs($info['money']);
            $this->webdb['money_ratio']>0 || $this->webdb['money_ratio']=10;
            add_jifen($this->user['uid'],$info['money']*$this->webdb['money_ratio'],'在线充值积分');
            add_rmb($this->user['uid'], -abs($info['money']), 0,'充值积分消费');
            return true;
        }
    }
    
    public function add($numcode='',$ispay=0)
    {
        if($numcode){
            if ($ispay==1) {
                if($this->pay_end($numcode)===true){
                    $this->success('充值成功','index');
                }else{
                    $this->error('充值失败','index');
                }
            }else{
                $this->error('你并没有付款','index');
            }
        }
        if (IS_POST) {
            $data = $this->request->post();
            if ( $data['money']<0.01 ) {
                $this->error('充值金额不能小于0.01元');
            }
            $numcode = 'j'.date('ymdHis').rands(3);      //订单号
            //直接跳转支付
            post_olpay([
                    'money'=>$data['money'],
                    'return_url'=>purl('add',['numcode'=>$numcode]),
                    'banktype'=>$data['paytype'],
                    'numcode'=>$numcode,
                    'callback_class'=>'',
            ] , true);	
        }
        return $this->fetch();
    }
    
    public function delete($id)
    {
        if( empty($this->admin) ){
            $this->error('非管理员,不能删除积分日志');
        }
        if (Model::destroy([$id])) {
            $this->success('删除成功','index');
        }else{
            $this->error('删除失败');
        }
    }
}
