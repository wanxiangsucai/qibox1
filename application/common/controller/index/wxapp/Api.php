<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
use app\common\traits\ModuleContent;
//use think\Db;

//小程序或APP调用的列表数据
abstract class Api extends IndexBase
{
    use ModuleContent;
    protected $model;                  //内容
    protected $mid;                      //模型ID
    
    public function add(){
        return $this->err_js('出错了!');
    }
    public function edit(){
        return $this->err_js('出错了!');
    }
    public function delete(){
        return $this->err_js('出错了!');
    }
    public function index(){
        return $this->err_js('出错了!');
    }
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'content');
        $this->model_reply = get_model_class($dirname,'reply');
        $this->mid = 1;
    }
    
    protected function check_getTab($id=0,$status=0){
        
        $info = $this->model->getInfoByid($id);
        if (empty($info)) {
            return '信息内容不存在';
        }elseif (!$this->admin && fun('admin@sort',$info['fid'])!==true && fun('admin@status_check',$status)!==true) {
            return '你没权限';
        }
//         $table = $this->model->getTableByMid($info['mid']);         
//         return array_merge($info,['table'=>$table]);
        return $info;
    }
    
    
    /**
     * 审核员操作
     * @param number $id
     * @param number $status
     * @param string $reason 操作理由
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function change_status($id=0,$status=0,$reason=''){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
        if ($info['status']==$status){
            return $this->err_js('重复设置，操作无效！');
        }
        $this->model->updates([
            'id'=>$id,
            'status'=>$status
        ]);
        
        $this->send_admin_msg($status,$info); //多级审核通知处理
        $this->send_author_msg($status,$info,$reason);
        
        return $this->ok_js([
            'status'=>$status,
            'id'=>$id,
            'status_name'=>fun('Content@status')[$status],
        ],fun('Content@status')[$status].' 操作成功');
    }
    
    /**
     * 给作者发消息
     * @param unknown $status
     * @param array $info
     * @param string $reason
     */
    protected function send_author_msg($status=0,$info=[],$reason=''){
        if ($reason!='') {
            $content = '你发的主题,';
            if($status==-9){
                $content .= '被拒审了';
            }elseif($status==-1){
                $content .= '被删除了';
            }elseif($status==1){
                $content .= '通过审核了';
            }else{
                $content .= '被执行了 '.fun('Content@status')[$status].' 操作';
            }
            $content .= '，标题是：'.$info['title'];
            if ($reason) {
                $content .= '，理由是：'.$reason;
            }
            if($status!=-1){
                $content .= '，<a href="'.get_url(iurl('content/show',['id'=>$info['id']])).'" target="_blank">点击查看详情</a>';
            }            
            send_msg($info['uid'],'被执行了 '.fun('Content@status')[$status].' 操作',$content);
            send_wx_msg($info['uid'], $content);
        }
    }
    
    /**
     * 对审核员进行消息通知
     * @param number $id
     * @param array $data
     */
    protected function send_admin_msg($status,$info=[]){
        if ($status>-2) {
            return ;
        }elseif($info['status']<$status){   //比如审核失误，重新退回审核，不通知，因为通知也是通知自己
            return ;
        }
        foreach(str_array($this->webdb['status_users'][abs($status)-1]) AS $_uid){
            if (!$_uid) {
                continue;
            }
            $title = '请及时审核 '.M('name').' 新主题';
            $content = '“'.$this->user['username'].'” 刚刚在 '.M('name').' 提交了: 《' . $info['title'] . '》请示你审核，请尽快处理！<a href="'.get_url(murl('content/manage')).'" target="_blank">点击查看详情</a>';
            send_msg($_uid, $title, $content);
            send_wx_msg($_uid, $content);
        }
    }
    
    /**
     * 审核操作
     * @param number $id
     * @param number $rid
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function yz($id=0,$rid=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
        if ($rid) {
            $reply_info = $this->model_reply->where('id',$rid)->find();
            if (empty($reply_info)) {
                return $this->err_js('回复不存在');
            }
            $this->model_reply->where('id',$rid)->update([
                'status'=>$reply_info['status']==1?0:1
            ]);
            return $this->ok_js([
                'status'=>$reply_info['status']
            ]);
        }else{
            $this->model->updates([
                'id'=>$id,
                'status'=>$info['status']==1?0:1
            ]);
            return $this->ok_js([
                'status'=>$info['status']
            ]);
        }
    }
    
    /**
     * 内容置顶
     * 置顶时间,单位小时
     * @param number $id 内容ID
     * @param number $time 置顶多久,单位小时
     * @return string|unknown[]|void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function top($id=0,$time=24){        
        $info = $this->check_getTab($id);        
        if (is_string($info)) {
            return $this->err_js($info);
        }        
//        $table = $info['table'];
        $list = $time*3600+time();
        $data = [
                'id'=>$id,
                'list'=>$list,
        ];
//         $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 信息沉底
     * @param number $id
     * @param number $time 默认为0,也可以设置压后多少个小时,
     * @return string|array|void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function bottom($id=0,$time=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
//        $table = $info['table'];        
        if ($time==0) {
            $list = 0;
        }else{
            $list = $info['create_time'] - $time*3600;
        }
        
        $data = [
                'id'=>$id,
                'list'=>$list,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 信息排序恢复原状
     * @param number $id
     * @param number $time
     * @return string|array|void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function recover($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
//        $table = $info['table'];        
        if ($info['update_time']) {
            $list = $info['update_time'];
        }else{
            $list = $info['create_time'];
        }
        
        $data = [
                'id'=>$id,
                'list'=>$list,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 推荐
     * @param number $id
     */
    public function star($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
 //       $table = $info['table'];    
        $data = [
            'id'=>$id,
            'status'=>2,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 取消推荐
     * @param number $id
     */
    public function unstar($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
 //       $table = $info['table'];    
        $data = [
            'id'=>$id,
            'status'=>1,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 锁定
     * @param number $id
     */
    public function lock($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
        $table = $this->model->getTableByMid($info['mid']);
        if (!table_field($table,'lock')) {
            query("ALTER TABLE  `qb_{$table}` ADD  `lock` TINYINT( 1 ) NOT NULL COMMENT  '是否锁定不给修改,删除,回复' AFTER  `status`");
        }
        
        $data = [
                'id'=>$id,
                'lock'=>1,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 取消锁定
     * @param number $id
     */
    public function unlock($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
//        $table = $info['table'];        
        $data = [
                'id'=>$id,
                'lock'=>0,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 标题加粗
     * @param number $id
     */
    public function fonttype($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
        $table = $this->model->getTableByMid($info['mid']); 
        if (!table_field($table,'font_type')) {
            query("ALTER TABLE  `qb_{$table}` ADD  `font_type` TINYINT( 1 ) NOT NULL COMMENT  '标题字体加粗或其它'");
        }        
        $data = [
                'id'=>$id,
                'font_type'=>1,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 取消标题加粗
     * @param number $id
     */
    public function unfonttype($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
//        $table = $info['table'];        
        $data = [
                'id'=>$id,
                'font_type'=>0,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 标题加红色
     * @param number $id
     */
    public function fontcolor($id=0,$type='#ff0000'){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
        $table = $this->model->getTableByMid($info['mid']); 
        if (!table_field($table,'font_color')) {
            query("ALTER TABLE  `qb_{$table}` ADD  `font_color`  VARCHAR( 7 ) NOT NULL COMMENT  '标题字体颜色' ");
        }
        
        $data = [
                'id'=>$id,
                'font_color'=>$type,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 取消标题加红色
     * @param number $id
     */
    public function unfontcolor($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
 //       $table = $info['table'];        
        $data = [
                'id'=>$id,
                'font_color'=>'',
        ];
 //       $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    public function list_model(){
        $array = model_config();
        $data = [];
        foreach ($array AS $rs){
            $data[] = [
                'id'=>$rs['id'],
                'name'=>$rs['title'],
            ];
        }
        return $this->ok_js($data);
    }
    
}







