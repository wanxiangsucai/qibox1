<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase; 
use app\index\controller\Attachment;
use app\common\traits\ModuleContent;

//小程序 发表内容处理
abstract class Post extends IndexBase
{
    use ModuleContent;
    protected $model;                  //内容
    protected $s_model;                  //栏目
    protected $mid;                    //模型ID
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model            = get_model_class($dirname,'content');
        $this->s_model          = get_model_class($dirname,'sort');
        $this->m_model          = get_model_class($dirname,'module');
        $this->category_model   = get_model_class($dirname,'category');
        $this->info_model       = get_model_class($dirname,'info');
        $this->f_model          = get_model_class($dirname,'field');
        
        $this->mid = 1;
    }
    
    /**
     * 获取 postFile 上传的文件 
     * @param string $skey
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function getfile($skey=''){
        $url = cache('file-'.$skey)?:cache('file-');
        if($url){
            return $this->ok_js(['url'=>$url]);
        }else{
            return $this->err_js('上传失败，文件不存在！');
        }
    }
   
    /**
     * APP及小程序上传文件
     */
    public function postFile(){
        if (empty($this->user)) {
            return $this->err_js("你还没登录!");
        }
        $obj = new Attachment();
        $o = $obj->upload('wxapp','wxapp','wxapp');
        $info = $o->getData();
        if($info['code']){
            $data['url'] = tempdir($info['id']);
            if(input('skey')){
                cache('file-'.input('skey'),$data['url'],600);
            }
            return $this->ok_js($data, $info['info']);
        }else{
            return $this->err_js($info['info']);
        }
    }
    

    
    /**
     * 删除主题
     * @param number $id 主题ID
     * @return \think\response\Json
     */
    public function delete($id=0){
        $info = $this->model->getInfoByid($id , false);
        $this->mid = $info['mid'];
        
        hook_listen('cms_delete_begin',$id);
//         if($info['uid']!=$this->user['uid']&&!$this->admin){
//             return $this->err_js('你没权限');
//         }
        if (($result=$this->delete_check($id,$info))!==true) {  //权限判断
            return $this->err_js($result);
        }        
        
        if($this->deleteOne($id,$info['mid'])){
        //if($this->model->deleteData($id)){
            return $this->ok_js([],'删除成功');
        }else{
            return $this->err_js('系统问题,删除失败!');
        }
    }
    
    /**
     * 修改主题 
     * @param number $id
     * @return \think\response\Json
     */
    public function edit($id=0){
		if( empty($this->request->isPost()) ){
			return $this->err_js('必须POST方式提交数据');
		}
        $info = $this->model->getInfoByid($id , false);
        $this->mid = $info['mid'];
        $data = get_post();
//         if($info['uid']!=$this->user['uid']&&!$this->admin){
//             return $this->err_js('你没权限');
//         }

        is_array($data['picurl']) && $data['picurl'] = implode(',', $data['picurl']);   //小程序传过来的是数组
        
        $result = $this->edit_check($id,$info,$data);
        if($result!==true){
            return $this->err_js($result);
        }
        
        $data = $this->format_post_data($data);
       
        //unset($data['uid'],$data['status'],$data['view'],$data['mid'],$data['list']);
        //$data['ispic'] = empty($data['picurl']) ? 0 : 1 ;
//         $array = [
//                 'id' =>$data['id'],
//                 'content' =>$data['content'],
//                 'fid' =>$data['fid'],
//                 'title' =>$data['title'],
//                 'ispic' =>$data['picurl']?1:0,
//                 'picurl' =>$data['picurl'],
//                 'update_time' => time(),
//         ];
        $reult = $this->model->editData($info['mid'],$data);
        if($reult){
            
            //以下两行是接口
            //hook_listen('cms_edit_end',$data,$reult);
            hook_listen('cms_edit_end',$data,['result' =>$result, 'module' =>$this->request->module(),'info'=>$info]);
            $this->end_edit($data['id'],$data,$info);
            
            return $this->ok_js(['id'=>$id,'url'=>iurl('content/show',['id'=>$id])],'修改成功');
        }else{
            return $this->err_js('修改失败');
        }    
    }
    
    /**
     * 保存数据
     * @param number $mid
     * @param array $data
     * @return unknown
     */
    protected function savaNewData($mid=0,&$data=[]){
        return $this->model->addData($mid,$data);
    }
    
    /**
     * 新发表主题
     * @return \think\response\Json
     */
    public function add($mid=1){
		if( empty($this->request->isPost()) ){
			return $this->err_js('必须POST方式提交数据');
		}
		$data = get_post();
		if ($data['fid']) {
		    $mid = get_sort($data['fid'],'mid');  //避免MID不一致
		    if (empty($mid)) {
		        return $this->err_js('当前栏目的MID值不存在');
		    }
		}
        $this->mid = $mid;        
        
        //接口
        hook_listen('cms_add_begin',$data);
        
        $result=$this->add_check($mid,$data['fid'],$data);

        if ($result!==true) {
            return $this->err_js($result);
        }
        
//         if(!$this->user){
//             return $this->err_js('你还没登录');
//         }
        unset($data['id']);
        $data['mvurl'] = url_clean_domain($data['mvurl']);    //把http清除掉
        is_array($data['picurl']) && $data['picurl'] = implode(',', $data['picurl']);   //小程序传过来的是数组
        $data['picurl'] = url_clean_domain($data['picurl']);    //把http清除掉 
        $data['uid'] = $this->user['uid'];
        $data = $this->format_post_data($data);
        
        $id = $this->savaNewData($mid,$data);
        
        if(is_numeric($id)){
            
            //以下两行是接口
            hook_listen('cms_add_end',$id,['data' =>$data, 'module' =>$this->request->module()]);
            $this->end_add($id,$data);
            
            return $this->ok_js(['id'=>$id,'url'=>iurl('content/show',['id'=>$id])],'提交成功');
        }else{
            return $this->err_js('添加内容失败,详情如下:'.$id);
        }
    }
    
    /**
     * 获取栏目数据
     * @return \think\response\Json
     */
    public function get_sort(){
        $_array = $this->s_model->getTitleList();
        $array = [];
        foreach ($_array AS $key=>$value){
            $array[] = [
                    'id'=>$key,
                    'name'=>$value
            ];
        }
        return $this->ok_js($array);
    }
    
    /**
     * 主题点赞
     * @param number $id 主题ID
     * @return \think\response\Json
     */
    public function agree($id=0){
        $k = $id.'-'.($this->user['uid']?:$this->onlineip);
        hook_listen( 'topic_agree' , $id , $this->request->module() );      //监听点赞主题
        if(cache('TopicAgree_'.$k)){
            return $this->err_js('一小时内,只能点赞一次!');
        }        
        if($this->model->addAgree($id)){
            cache('TopicAgree_'.$k, time(),3600);
            return $this->ok_js();
        }else{
            return $this->err_js('数据库执行失败');
        }        
    }
}













