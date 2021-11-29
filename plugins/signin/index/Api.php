<?php
namespace plugins\signin\index;

use app\common\controller\IndexBase;
use plugins\signin\model\Member as Model;
use plugins\signin\model\Cfg AS CfgModel;

class Api extends IndexBase
{
    /**
     * 获取圈子签到配置参数
     * @param number $ext_id
     * @param number $ext_sys
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function get_cfg($id=0,$ext_sys=0){
        if (empty($ext_sys)) {
            $ext_sys = 'qun';
        }
        $map = [
            'ext_id'=>$id,
        ];
        $ext_sys = is_numeric($ext_sys)?$ext_sys:modules_config($ext_sys)['id'];
        $map['ext_sys'] = $ext_sys;
        $info = getArray(CfgModel::where($map)->find());
        if ($info['day_money']==''||$info['day_money']==0) {
            return $this->err_js('还没配置详细参数');
        }
        $detail = explode(',',$info['day_money']);
        $num = count($detail)>1?count($detail):3;
        $money_days = [];
        for($i=0;$i<$num;$i++){
            $money_days[] = [
                'day'=>$i+1,
                'money'=>$detail[$i]>0 ? $detail[$i] : ($detail[$i-1]>0?$detail[$i-1]:$detail[$i-2]),
            ];
        }
        $continue_num = 0;  //连续签到天数
        $today_have_signin = false;
        if ($this->user) {
            $map =[
                'uid'=>$this->user['uid'],
                'aid'=>$id,
                'sysid'=>$ext_sys,
            ];
            $array = Model::where($map)->order('id desc')->limit($num)->column('create_time');
            $nowtime = time();
            if(date('ymd',$nowtime)==date('ymd',$array[0])){
                $today_have_signin = true;
                $i = 0;
            }else{
                $i = 1;
            }            
            foreach ($array AS $key=>$time){
                $nowtime -= $i*3600*24;
                if(date('ymd',$nowtime)==date('ymd',$time)){
                    $continue_num++;
                }else{
                    break;
                }
                $i++;
            }
        }
        
        if ($info) {
            $info['today_have_signin'] = $today_have_signin;
            $info['money_days'] = $money_days;
            $info['continue_num'] = $continue_num;
            return $this->ok_js(getArray($info));
        }else{
            return $this->err_js('还没配置参数');
        }
    }
    
    /**
     * 签到接口
     * @param number $id 信息ID
     * @param string $type 频道ID或目录名
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function sign($id=0,$type=''){
        if (empty($this->user)) {
            return $this->err_js('你还没登录');
        }
        
        if (!check_tncode()) {
            return $this->err_js('验证码不对！');
        }
        
        //扩展给频道做深层次的应用
        $sysid = 0;
        $id = intval($id);
        $sign_cfg = [];
        if ($id) {
            if (empty($type)) {
                $type = 'qun';
            }
            $array = modules_config($type);
            if (empty($array)) {
                return $this->err_js('频道有误');
            }
//             elseif (  !query($array['keywords'].'_content',['where'=>['id'=>$id],'value'=>'id'])) {
//                 return $this->err_js('主题并不存在');
//             }
            $sysid = $array['id'];
            $sign_cfg = CfgModel::where('ext_sys',$sysid)->where('ext_id',$id)->find();
            if (empty($sign_cfg)) {
                return $this->err_js('该 '.QUN.' 还没设置签到参数配置');
            }
        }
        

        $result = $this->get_money($this->user['uid'],$sysid,$id,$sign_cfg);
        if (is_string($result)) {
            return $this->err_js($result);
        }
        if ($this->webdb['must_wxlogin'] && empty($this->user['weixin_api'])) {
            $result['money'] = 0;
        }
        $result['money'] = abs(intval($result['money']));
        $data = [
                'uid'=>$this->user['uid'],
                'aid'=>$id,
                'sysid'=>$sysid,
                'money'=>$result['money'],
                'rank'=>intval($result['rank']),
                'intime'=>intval($result['intime']),
                'create_time'=>time(),
                'ip'=>get_ip(),
        ];
        
//         if (!table_field('signin_member','ip')) {
//             into_sql("ALTER TABLE  `qb_signin_member` ADD  `ip` VARCHAR( 15 ) NOT NULL;");
//         }
        
        if(Model::create($data)){
            if ($result['money']>0) {
                if ($sign_cfg) {
                    $hy_user = get_user($sign_cfg['uid']);
                    if ($hy_user['money']<$result['money']) {
                        return $this->err_js('签到成功,但没有获得积分奖励,因为本 '.QUN.' 管理员帐户上的积分不足,请联系他尽快充值!');
                    }
                    add_jifen($sign_cfg['uid'], -$result['money'],$this->user['username'].'在你的 '.QUN.' 签到扣除');
                }
                $this->fx($result['money'],$sign_cfg);  //分销奖励
                add_jifen($this->user['uid'], $result['money'],'签到奖励');
            }
            if ($result['rank']==1) {   //处理颁奖,其实可以放在定时任务那里.
                $obj = new Task();
                $obj->run();
            }
            if ($this->webdb['must_wxlogin'] && empty($this->user['weixin_api'])) {
                return $this->err_js('签到成功,但因为你没绑定微信登录,没有奖励!');
            }
            return $this->ok_js($result,$result['msg']);
        }else{
            return $this->err_js('数据库插入失败');
        }
    }
    
    /**
     * 分销奖励
     * @param number $money 当前用户奖励的积分
     * @param array $sign_cfg 如果数组不为空,代表在商家那里签到
     */
    private function fx($money=0,$sign_cfg=[]){
        if ($sign_cfg) {
            $fx_1 = $this->fx_money($money,$sign_cfg['fx_1']);
            $fx_2 = $this->fx_money($money,$sign_cfg['fx_2']);
            $fx_3 = $this->fx_money($money,$sign_cfg['fx_3']);
        }else{
            $fx_1 = $this->fx_money($money,$this->webdb['sign_fx_1']);
            $fx_2 = $this->fx_money($money,$this->webdb['sign_fx_2']);
            $fx_3 = $this->fx_money($money,$this->webdb['sign_fx_3']);
        }
        $this->gave_fx($fx_1,1,$sign_cfg);
        $this->gave_fx($fx_2,2,$sign_cfg);
        $this->gave_fx($fx_3,3,$sign_cfg);
    }
    
    /**
     * 分销入帐
     * @param number $money 实际分销获利
     * @param number $step 第几级
     * @param array $sign_cfg 如果数组不为空,代表在商家那里签到
     */
    private function gave_fx($money=0,$step=1,$sign_cfg=[]){
        if ($money<1) {
            return ;
        }
        $uid = $this->user['introducer_'.$step];    //推荐人的UID
        if (empty($uid)) {
            return ;
        }
        if ($sign_cfg) {
            $hy_user = get_user($sign_cfg['uid']);
            if ($hy_user['money']<$money) {
                return ;    //商家积分不足,不给推荐人奖励 这里最好再加上通知,告诉商家及时充值
            }
            $content = $this->user['username'].'签到,'.get_status($step,['','一','二','三']).'级推荐人获利扣取';
            add_jifen($sign_cfg['uid'], -$money,$content);
        }
        
        $content = $this->user['username'].'签到,你作为'.get_status($step,['','一','二','三']).'级推荐人获利奖励';
        add_jifen($uid, $money,$content);
        
        $title = '会员签到,推荐人奖励';        
        send_msg($uid,$title,$content);
    }
    
    /**
     * 计算分销获利数值
     * @param number $money
     * @param string $fx 可能是具体数值,也可能是百分比
     */
    private function fx_money($money=0,$fx=''){
        if (empty($fx)) {
            return ;
        }
        if (is_numeric($fx)) {
            return $fx;
        }
        $fx = str_replace(['%','％'], '', $fx);
        return ceil($money*$fx/100);
    }
    
    /**
     * 核查是否签到过
     * @param number $uid
     * @param number $sysid
     * @param number $id
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function check($uid=0,$type='',$id=0){
        if (empty($this->user)) {
            return $this->err_js('你还没登录',[],2);
        }
        
        $sysid = 0;
        $id = intval($id);
        if ($id) {
            if (empty($type)) {
                $type = 'qun';
            }
            $array = modules_config($type);
            if (empty($array)) {
                return $this->err_js('频道有误',[],3);
            }
            $sysid = $array['id'];
        }
        
        $map = [
                'uid'=>$uid?:$this->user['uid'],
                'sysid'=>$sysid,
                'aid'=>$id,
        ];
        $info = Model::where($map)->order('id desc')->find();
        if ($info) {
            $info = getArray($info);
            if ( date('z',$info['create_time'])==date('z') ) {
                $info['create_time'] = format_time($info['create_time']);
                return $this->ok_js($info,'你今天已经签到过了,请明天再来!');
            }
        }
        return $this->err_js('你今天还没签到!');
    }
    
    /**
     * 每天按凌晨0点开始统计排名
     * @return void|string[]|number[]|number[]|string[]
     */
    private function get_day_rank($ext_sys=0,$ext_id=0){
        if ($this->webdb['sign_max_num']<1) {   //签到人数小于1 即不限制签到人数
            return ;
        }
        //取最近的有效签到人数,有可能有昨天的数据
        $data = Model::where('sysid',$ext_sys)->where('aid',$ext_id)->order('id desc')->limit($this->webdb['sign_max_num'])->column('id,create_time');
        if ( $this->webdb['sign_max_num']==count($data) && date('z',end($data))==date('z') ) {  //如果最后的记录是今天,并且刚好等于有效人数的话,则没名额了
            if ( $this->webdb['sign_max_minmoney']>0 ) {    //给予安慰奖
                $rank = Model::whereTime('create_time','today')->where('sysid',$ext_sys)->where('aid',$ext_id)->count('id') + 1;
                return [
                        'rank'=>$rank,                    //第几名
                        'money'=>$this->webdb['sign_max_minmoney'],
                        'msg'=>'你来得太晚了，所以只获取得 ' . $this->webdb['sign_max_minmoney'] . '个积分的奖励，每天只有前 ' . $this->webdb['sign_max_num'] . ' 名签到的用户，才能获得更高的奖励，你现在是第 ' . $rank . ' 名。',
                ];
            }else{
                return [
                        'money'=>0,
                        'msg'=>'很遗憾，你来晚了一步，今天的签到积分被抢光了，每天只有前 ' . $this->webdb['sign_max_num'] . ' 名签到的用户，才能获得积分奖励。',
                ];
            }
        }
    }
    
    
    /**
     * 每天按不同时间段进行统计 比如 9:00=10,12:00=8
     * @param number $ext_sys 频道ID
     * @param number $ext_id 频道内容ID
     * @return void|string[]|number[]|number[]|string[]
     */
    private function get_time_rank($ext_sys=0,$ext_id=0){
        if (!strstr($this->webdb['sign_max_num'], '=')) {
            return ;
        }
        $detail = explode(',',$this->webdb['sign_max_num']);
        foreach($detail AS $value){
            list($time,$max) = explode('=',$value);
            $array[str_replace(':', '', $time)] = $max;
        }
        ksort($array);  //时间从早到晚进行重新排序
        $now = date('Hi');
        $total = 0;     //签到的有效名额
        foreach($array AS $time=>$num){
            if ($now>=$time) {
                $total +=$num;  //把之前的有效名额往后累加
            }
        }
        //及时有效签到的用户, 有的可能是昨天的
        $data = Model::where(['intime'=>1])->where('sysid',$ext_sys)->where('aid',$ext_id)->order('id desc')->limit($total)->column('id,create_time');
        if ( $total==count($data) && date('z',end($data))==date('z') ) {    //数量等于有效名额,并且最后一条记录是今天的话 ,那说明今天的有效名额已用完
            if ( $this->webdb['sign_max_minmoney']>0 ) {    //安慰奖励
                $rank = Model::whereTime('create_time','today')->where('sysid',$ext_sys)->where('aid',$ext_id)->count('id') + 1;
                return [
                        'rank'=>$rank,                    //第几名
                        'money'=>$this->webdb['sign_max_minmoney'],
                        'msg'=>'你来得太晚了，所以只获取得 ' . $this->webdb['sign_max_minmoney'] . '个积分的奖励，你现在是第 ' . $rank . ' 名签到者。',
                ];
            }else{  //不给安慰奖
                return [
                        'money'=>0,
                        'msg'=>'很遗憾，你来晚了一步，签到积分被抢光了。',
                ];
            }
        }elseif($total==0){     //时间太早,还没有任何有效名额
            $rank = Model::whereTime('create_time','today')->where('sysid',$ext_sys)->where('aid',$ext_id)->count('id') + 1;
            return [
                    'rank'=>$rank,                    //第几名
                    'money'=>$this->webdb['sign_max_minmoney'],
                    'msg'=>'你太猴急，来得太早了，所以只获取得 ' . $this->webdb['sign_max_minmoney'] . '个积分的奖励，你现在是第 ' . $rank . ' 名签到者。',
            ];
        }
    }
    
    /**
     * 统计用户本次签到能领取到多少积分
     * 未能领就返回字符串说明原因, 能领取就返回数组
     * @param number $uid
     * @param number $sysid 频道ID扩展用
     * @param number $aid 频道信息ID扩展用
     * @param array $cfg 频道的签到参数配置
     * @return string|string[]|number[]|string[]|string[]|number[]|unknown[]
     */
    private function get_money($uid=0,$sysid=0,$aid=0,$cfg=[]){
        $map = [
                'uid'=>$uid,
                'sysid'=>$sysid,
                'aid'=>$aid,
        ];
        //只统计用户最近7天的数据.是否连续访问
        $listdb = Model::where($map)->order('id desc')->limit(7)->column('id,create_time,money');
        $listdb = array_values($listdb);
        if ($listdb) {
            if ( date('z',$listdb[0]['create_time'])==date('z') ) {
                return '你今天已经签到过了,请明天再来!';
            }elseif (   date('Hi',$listdb[0]['create_time'])==date('Hi')  ) {   //两天签到时间一模一样
            //}elseif (   date('i',$listdb[0]['create_time'])==date('i')  || abs(date('Hi',$listdb[0]['create_time'])-date('Hi'))<5   ) {
                return '你有作弊嫌疑，签到失败！请晚点再签到，留点机会给别人！';
            }elseif( Model::whereTime('create_time','today')->where('ip',get_ip())->where('sysid',$sysid)->where('aid',$aid)->count('id') > 0 ){
                return '同一个IP每天只能签到一次，签到失败！请明天再签到！';
            }
        }
        
        if ($cfg) { //频道应用
            $this->webdb['sign_max_num'] = $cfg['limit_num'];               //限制每天前多少名签到才有奖,若设置9:00=10,14:30=10指定时刻后的前几名才有奖            
            $this->webdb['sign_max_minmoney'] = $cfg['min_money'];   //超过签到人数后,给予的安慰积分个数
            $this->webdb['sign_money'] = $cfg['day_money'];                  //每天有效签到奖励积分, 可以是整数,也可以是 3,5,8 有逗号隔开,代表第二天第三天的不同奖励
        }
       
        //每天前几名签到才享受正常奖励, 超过,将不奖励,或者是少奖励
       if ($this->webdb['sign_max_num']!='') {              //每天签到做了名额限制
           if (strstr($this->webdb['sign_max_num'], '=')) {
               $array = $this->get_time_rank($sysid,$aid);  //按每天的时间段统计,比如  设置9:00=10,14:30=10
           }else{
               $array = $this->get_day_rank($sysid,$aid);   //直接限制前几名,这里是 大于或等于 1以上的整数
           }
           if (!empty($array)) {    //没有名额了,有效签到名额没有, 则终止下面的所有处理 直接跳出当前函数
               return $array;
           }           
       }
       
       $money = 0;  //每天有效的签到积分奖励
       if($this->webdb['sign_money_day2'] || $this->webdb['sign_money_day3']){      //额外增加的奖励, 这两项存在的话,代表是平台系统后台设置的
           list($min,$max) = explode(',',$this->webdb['sign_money']);                           //设置了随机值 比如  3,8
           $money = $max ? rand($min,$max) : $min;                                                  //每天有效的签到积分奖励
       }else{
           $_array = explode(',',$this->webdb['sign_money']);
           foreach ($_array AS $key=>$value){
               if ($key==0) {
                   $money = $value;     //基础奖励
               }elseif($value>$money){
                   $_key = $key+1;
                   $this->webdb['sign_money_day'.$_key] = $value-$money;    //连续签到第二天第三天以上的额外奖励
               }
           }
       }
       
       $addmoney = 0;   //连续签到额外奖励
       
       //$premoney = 0;   //上一次的奖励.可以拓展为不断类加,但最好要有个封顶
       //连续签到积分奖励 跨年问题没做处理
       
       $i = 0;
       $today = date('z');
       foreach ($listdb as $key => $rs) {
           --$today;    //上一天
           if($today==date('z',$rs['create_time'])){    //上一天有签到过
               $i = $key+2;                                         //代表此次是第二天签到,有额外奖励
               if( $this->webdb['sign_money_day'.$i] >0 ){
                   $addmoney = $this->webdb['sign_money_day'.$i];   //额外增加的奖励
                   //$premoney = $rs['money'];    //对应那一次的奖励.可以拓展为不断类加
               }else{   //比如只设置了前3天,那么第4天不存在额外奖励,就跳出去了, 第4天没有,即使第5天有,都不处理了,不符合常规逻辑
                   break;
               }
           }else{   //非连续的天数,就直接跳出不再统计了
               break;
           }
       }
       
       $totalmoney = $money;    //有效签到的最基本奖励
       
       if ($addmoney>0) {   //连续签到额外再奖励
           //$totalmoney = $premoney>$money ? $premoney : $money;    //上一次的奖励.可以拓展为不断类加,但最好要有个封顶
           $totalmoney += $addmoney;
       }
       
       //$rank = 0;
       $rank = Model::whereTime('create_time','today')->where(['sysid'=>$sysid,'aid'=>$aid])->count('id') + 1;
       $msg = '恭喜你,签到成功,获得 '. $money . ' 个积分的奖励.';
       if ($this->webdb['sign_max_num']) {    //每天有名额限制 可以是整数 ,也可以是时间点限制 ,比如 9:00=8,12:00=5,
           //$rank = $this->get_rank($data);
           $msg = '恭喜你,第 ' . $rank . ' 名签到,成功获得 '. $money . ' 个积分的奖励.';         //每天的前几名用户
       }
       
       $day = $i-1;
       $msg .= $addmoney>0 ? "其中连续签到 {$day} 天,额外获得了 {$addmoney} 个积分的奖励,本次奖励总共 {$totalmoney} 个积分" : '';
       return [
               'intime'=>1,                        //是否及时签到
               'rank'=>$rank,                    //总签到的第几名,但排名前的,不一定就能得到奖励,所谓来得早不如来得巧 
               'day'=>$day,                        //连续签到几天
               'money'=>$totalmoney,              //最终得到的奖励
               'addmoney'=>$addmoney,   //其中额外得到的奖励
               'msg'=>$msg,
       ];       
    }
    
    /**
     * 取得第几名
     * @param array $array
     * @return number
     */
    private function get_rank($array=[]){
        $i = 1;
        foreach ($array AS $time){
            if (date('z',$time)==date('z')) {
                $i++;
            }else{
                break;
            }
        }
        return $i;
    }

}
