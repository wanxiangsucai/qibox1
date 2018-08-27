<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;

//小程序 评论
abstract class Reply extends IndexBase
{
    protected $model;                  //评论模型表
    protected $topic_model;                  //评论模型表

    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'reply');
        $this->topic_model = get_model_class($dirname,'content');
    }
    
    /**
     * 获取评论数据
     * @param number $id 内容主题ID
     * @param string $orderby 排序方式
     * @param string $rows 每次取几条数据
     * @param string $page 第几页
     * @return \think\response\Json
     */
    public function index($id=0,$orderby='',$rows='',$page=0){        
        if($orderby=='asc'){
            $orderby = 'id asc';
        }else{
            $orderby = 'id desc';
        }
        $data = getArray($this->model->getListByAid($id,'',$orderby,10));
        return $this->ok_js($data);
    }
    
    
    
    /**
     * 删除回复评论
     * @param number $id 评论ID
     * @return \think\response\Json
     */
    public function delete($id=0){
        $info = $this->model->get($id);
        if($info['uid']!=$this->user['uid'] && !$this->admin){
            return $this->err_js('你没权限');
        }
        if($this->model->delete_Info($id)){
            return $this->ok_js();
        }else{
            return $this->err_js('删除失败');
        }
    }
    
    
    
    /**
     * 评论 点赞
     * @param number $rid 回复评论ID
     * @return \think\response\Json
     */
    public function agree($id=0){
        if(time()-get_cookie('cReply_'.$id)<3600){
            return $this->err_js('一小时内,只能点赞一次!');
        }
        set_cookie('cReply_'.$id, time());
        if($this->model->agree($id)){
            return $this->ok_js();
        }else{
            return $this->err_js('数据库执行失败');
        }
    }
    
    /**
     * 发表评论
     * @param unknown $id 主题ID
     * @param unknown $pid 引用评论ID
     * @return \think\response\Json
     */
    public function add($id=0,$pid=0){
        if( empty($this->request->isPost()) ){
            return $this->err_js('必须POST方式提交数据');
        }
//         if(!$this->user){
//             return $this->err_js('你还没登录');
//         }
        $data = get_post();
//         if($data['content']==''){
//             return $this->err_js('评论内容不能为空!');
//         }elseif(!$this->admin){
//             if(get_cookie('reply_content')==md5($data['content'])){
//                 return $this->err_js('请不要重复发表相同的内容!');
//             }
//             set_cookie('reply_content', md5($data['content']));
//         }

//         $data['picurl'] = implode(',',$data['picurl']);
//         $data['picurl'] = url_clean_domain($data['picurl']);
//         $data['mvurl'] = url_clean_domain($data['mvurl']);
//         unset($data['id']);
//         $data['uid'] = $this->user['uid'];
        
        //以下是接口        
        hook_listen('reply_add_begin',$data);
        $result = $this->add_check($data,$id,$pid);
        if($result!==true){
            return $this->err_js($result);
        }

        if( ($result = $this->model->add($data,$id,$pid))!=false ){
            $array = getArray($this->model->getListByAid($id,'','id desc',10));
            
            //以下是接口
            $this->end_add($data,$result->id);
            hook_listen('reply_add_end',$data,$result->id);
            
            return $this->ok_js($array);
        }else{
            return $this->err_js('系统原因,评论失败');
        }
    }
    
    /**
     *  提交前的判断处理,可重新定义
     * @param array $data POST数据
     * @param number $id 主题ID
     * @param number $pid 引用回复ID
     * @return void|\think\response\Json|boolean
     */
    protected function add_check(&$data=[],$id=0,$pid=0){
        $info = $this->topic_model->getInfoByid($id);
        if(!$info){
            return '主题不存在!';
        }elseif(!$this->user){
            return '你还没登录';
        }elseif($this->user['groupid']==2){
            return '很抱歉,你已被列入黑名单,没权限发布,请先检讨自己的言行,再联系管理员解封!';
        }
        
        if (!empty($this -> validate)) {   //验证数据
            $result = $this -> validate($data, $this -> validate);
            if (true !== $result){
                return $result;
            }
        }
        
        if($data['content']==''){
            return '评论内容不能为空!';
        }elseif(!$this->admin){
            if(get_cookie('reply_content')==md5($data['content'])){
                return '请不要重复发表相同的内容!';
            }
            set_cookie('reply_content', md5($data['content']));
        }
        
        if (fun('ddos@reply',$data)!==true) {    //防灌水
            return fun('ddos@reply',$data);
        }
        
        is_array($data['picurl']) && $data['picurl'] = implode(',',$data['picurl']);
        $data['picurl'] && $data['picurl'] = url_clean_domain($data['picurl']);
        $data['mvurl'] && $data['mvurl'] = url_clean_domain($data['mvurl']);
        if(isset($data['id'])){
            unset($data['id']);
        }
        $data['aid'] || $data['aid'] = $id;
        $data['pid'] || $data['pid'] = $pid;
        
        $data['list'] = time();
        
        $data['uid'] = intval($this->user['uid']);
        
        return true;
    }
    
    /**
     * 提交后的处理,可重新定义
     * @param array $data 入库后取出的数据
     */
    /**
     * 提交后的处理,可重新定义
     * @param array $data 提交的数据
     * @param unknown $id 入库后返回的ID
     */
    protected function end_add($data=[],$id){
        $topic = $this->topic_model->getInfoByid($data['aid']);
        $pinfo = $data['pid'] ? getArray($this->model->get($data['pid'])) : [];
        if( $this->webdb['reply_send_wxmsg'] ){
            $content = '主题: 《' . $topic['title'] . '》刚刚 “'.$this->user['username'].'” 对此进行了回复,<a href="'.get_url(urls('content/show',['id'=>$data['aid']])).'">你可以点击查看详情</a>';
            if($topic['uid']!=$this->user['uid']){
                if($this->forbid_remind($topic['uid'])!==true){
                    send_wx_msg($topic['uid'], '你发表的'.$content);
                }                
            }
            
            if($pinfo && $topic['uid']!=$pinfo['uid']){
                if($pinfo['uid']!=$this->user['uid']){
                    if($this->forbid_remind($pinfo['uid'])!==true){
                        send_wx_msg($pinfo['uid'], '你参与讨论的'.$content);
                    }                    
                }                
            }
        }
    }
    
    /**
     * 用户是否关闭了提醒
     * @param number $uid
     * @return boolean
     */
    protected function forbid_remind($uid=0){
        $user = get_user($uid);
        if (isset($user['sendmsg']['bbs_reply_wxmsg'])&&empty($user['sendmsg']['bbs_reply_wxmsg'])) {
            return true;
        }
    }
    

}













