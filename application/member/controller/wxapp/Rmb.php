<?php
namespace app\member\controller\wxapp;

use app\common\controller\MemberBase;
use think\Db;

class Rmb extends MemberBase
{
    /**
     * 统计当前用户的RMB消费与收入日志.可以按月日星期
     * @param string $type 可以是week month day
     * @param string $nums 0代表当前,1代表上一个周期
     * @param string $in_out 可以是i 或o 收入或支出
     * @return void|\think\response\Json
     */
    public function index($type='week',$nums='0,1,2,3,4,5,6',$in_out='')
    {
        if (!in_array($type, ['week','day','month'])) {
            return $this->err_js('类型有误');
        }
        $data = [];
        foreach(explode(',',$nums) AS $num){
            if(!is_numeric($num)){
                continue ;
            }
            if($in_out=='i'){
                $data[] = $this->total($type,$num,true);    //收入
            }elseif($in_out=='o'){
                $data[] = $this->total($type,$num,false);    //支出
            }else{
                $t1 = $this->total($type,$num,true);    //收入
                $t2 = $this->total($type,$num,false);    //支出
                $data[] = [$t1,$t2];
            }
        }
        return $this->ok_js($data);
    }
    
    /**
     * 统计收入或支出
     * @param string $type 可以是week month day
     * @param string $add 是收入或支出
     * @param number $num 哪一个周期
     * @return number
     */
    private function total($type='week',$num=1,$add=true){
        if ($add) {
            $map = [
                'money'=>['>',0],
            ];
        }else{
            $map = [
                'money'=>['<',0],
            ];
        }
        $map['uid'] = $this->user['uid'];
        $map['posttime'] = fun('time@only',$type,$num);
        return Db::name('rmb_consume')->where($map)->sum('money');
    }
}
