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
    
    public function pay_end($numcode=''){
        $info = RmbInfull::get(['numcode'=>$numcode]);
        if ($info['ifpay']==0) {
            $this->error('你还没有支付成功');
        }
        if ($this->user['rmb']>=$info['money']){
            $this->user['rmb'] = $this->user['rmb'] - abs($info['money']);
            add_jifen($this->user['uid'],$info['money']*100,'在线充值');
            add_rmb($this->user['uid'], -abs($info['money']), 0,'充值积分消费');
        }
    }
    
    public function add($numcode='')
    {
        if($numcode){
            $this->pay_end($numcode);
            $this->success('充值成功','index');
        }
        if (IS_POST) {
            $data = $this->request->post();
            if ( $data['money']<0.01 ) {
                $this->error('充值金额不能小于0.01元');
            }
            $numcode = rands(10);
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
        if (Model::destroy([$id])) {
            $this->success('删除成功','index');
        }else{
            $this->error('删除失败');
        }
    }
}
