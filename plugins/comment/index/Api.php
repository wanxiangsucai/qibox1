<?php
namespace plugins\comment\index;
use plugins\comment\model\Content AS contentModel;
use app\index\model\Label AS LabelModel;
use app\common\controller\IndexBase;

class Api extends IndexBase
{
    protected $validate = '';
    private static $get_children = null; //取引用回复
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new contentModel();
    }
    
    /**
     * 被各频道调用发布评论接口
     * @param number $sysid
     * @param number $aid
     * @return string
     */
    public function add($name='',$page='',$pagename='',$sysid=0, $aid=0 ,$rows=0,$order='',$by='',$status=''){
        $_array = input();
        $agree = $_array['agree'];
        $type = $_array['type'];
        $data_type = $_array['data_type'];
        $pid = $_array['pid'];
        $id = $_array['id'];
        
//         self::$get_children = $pid;
//         echo self::ajax_content($name,$page,$pagename,$sysid, $aid,$rows,$order,$by,$status,$type);
//         exit;
        
        if($agree==1){  //点赞
            if(time()-get_cookie('comment_'.$id)<3600){
                return $this->err_js('一小时内,只能点赞一次!');
            }
            set_cookie('comment_'.$id, time());
            if( contentModel::where('id',$id)->setInc('agree',1) ){
                //echo self::ajax_content($name,$page,$pagename,$sysid, $aid,$rows,$order,$by,$status,$data_type);
                return $this->ok_js();
            }else{
                return $this->err_js('数据库执行失败!');
            }
        }elseif ($this->request->isPost()) {
            $data = $this->request->post();
            if (!empty($this -> validate)) {   //验证数据
                $result = $this -> validate($data, $this -> validate);
                if (true !== $result){
                    return $this->err_js($result);
                }
            }
            if($data['content']==''){
                return $this->err_js('内容不能为空');
            }elseif(!$this->admin){
                if(get_cookie('reply_content')==md5($data['content'])){
                    return $this->err_js('请不要重复发表相同的内容!');
                }
                set_cookie('reply_content', md5($data['content']));
            }
            $data['aid'] = $aid;
            $data['sysid'] = $sysid;
            $data['pid'] = intval($pid);
            $data['uid'] = intval($this->user['uid']);
            if (contentModel::create($data)) {
                if($pid){
                    self::$get_children = $pid;
                    contentModel::where('id',$pid)->setInc('reply',1);
                }
                return self::ajax_content($name,$page,$pagename,$sysid, $aid,$rows,$order,$by,$status,$data_type);
            } else {
                return $this->err_js('数据库执行失败!');
            }
        }
    }
    
    private function get_tag_config($name='',$pagename=''){
        static $tag_array = null;
        if($tag_array!=null){
            return $tag_array;
        }
        $tag_array = cache('qb_tag_'.$name);    //数据库参数配置文件
        if(empty($tag_array)){                             //数据库设定的模板优先
            $tag_array = LabelModel::get_tag_data_cfg($name , $pagename);
            //cache('qb_tag_'.$tag_name,$tag_array,$tag_array['cache_time']);
            //trim($tag_array['view_tpl']) && $view_tpl = $tag_array['view_tpl'];
        }
        return $tag_array;
    }
    
    /**
     * 获取模板
     * @param string $name 标签名
     * @param string $pagename 模板路径
     * @return string|\app\index\model\NULL
     */
    private function get_tpl($name='',$pagename=''){
        $page_tpl = cache('tags_comment_tpl_'.$pagename);  //模板缓存
        if(!empty($page_tpl)){
            $view_tpl = $page_tpl[$name];
        }
        
        $tag_array = self::get_tag_config($name,$pagename);    //数据库参数配置文件
        trim($tag_array['view_tpl']) && $view_tpl = $tag_array['view_tpl'];
        
        if(self::$get_children!=null){
            $string = stristr($view_tpl,'<?php if(is_array($rs[\'children\'])'); //变量名必须是 $rs['children']
            $num =  stripos($string,'<?php endforeach; endif; else: echo "" ;endif; ?>');
            $view_tpl = substr($string,0,$num).'<?php endforeach; endif; else: echo "" ;endif; ?>';
        }else{
            //截取循环那段模板，其它不需要
            $string = stristr($view_tpl,'<?php if(is_array($listdb)');  //变量名必须是 $listdb
            $num =  strripos($string,'<?php endforeach; endif; else: echo "" ;endif; ?>');
            $view_tpl = substr($string,0,$num).'<?php endforeach; endif; else: echo "" ;endif; ?>';
        }
        return $view_tpl;
    }
    
    /**
     * 取JSON数据
     * @param string $name
     * @param string $page
     * @param string $pagename
     * @param number $sysid
     * @param number $aid
     * @param number $rows
     * @param string $order
     * @param string $by
     * @param string $status
     * @param string $data_type
     * @return void|\think\response\Json
     */
    private function ajax_content($name='',$page='',$pagename='',$sysid=0, $aid=0 ,$rows=0,$order='',$by='',$status='',$data_type=''){
        
        //$tag_array = self::get_tag_config($name,$pagename);
        
        $view_tpl = self::get_tpl($name,$pagename);        
        
        if(empty($view_tpl)){
            return $this->err_js('not_tpl');
            //die('tpl not exists !');
        }
        
        $data_list = $this->get_list($sysid,$aid,$rows,$status,$order,$by,$page);
        $array = getArray($data_list);
        $listdb = $array['data'];
        
        if(empty($listdb)){
            //die('null');
            $content = '';
        }else{
            if(self::$get_children!=null){
                $rs['children'] = $listdb;
            }
            //ob_end_clean();ob_start();
            eval('?>'.$view_tpl);
            $content = ob_get_contents();
            ob_end_clean();
        }
        
        $array['data'] = $content;
        return $this->ok_js($array);
    }
    
    /**
     * AJAX获取分页数据
     * @param string $name 标签名
     * @param string $page 第几页
     * @param string $pagename 模板文件名
     * @param number $sysid 频道系统ID
     * @param number $aid 内容ID
     * @param number $rows 每页取几条
     * @param string $order 按什么排序
     * @param string $by 升序还是降序
     * @param string $status 是否审核
     */
    public function ajax_get($name='',$page='',$pagename='',$sysid=0, $aid=0 ,$rows=0,$order='',$by='',$status=''){
        $_array = input();
        $data_type = $_array['data_type'];
        $content = self::ajax_content($name,$page,$pagename,$sysid, $aid,$rows,$order,$by,$status,$data_type);
        return  $content;
    }
    
    
    
    /**
     * 被各频道调用评论数据
     * @param number $sysid 频道模块的ID
     * @param number $aid 频道内容的ID
     * @param number $page 显示第几页
     * @param number $rows 每页显示几条
     * @param number $status 设置为1的时候代表只取已审的，为0显示所有
     * @param string $order 按什么排序
     * @param string $by 升序还是降序
     * @return unknown
     */
    public function get_list($sysid=0,$aid=0,$rows=0,$status=0,$order='',$by='',$page=0){
        
        if(self::$get_children!=null){  //取引用回复
            $map = [
                    'pid'=>self::$get_children,
            ];
        }else{
            $map = [
                    'aid'=>$aid,
                    'sysid'=>$sysid,
                    'pid'=>0,
            ];
        }
        
        if($status==1){
            $map['status']=1;
        }
        if(!in_array($order, ['id','list','create_time','agree','reply'])){
            $order = 'list desc,id desc';
        }
        $rows = intval($rows);
        if($rows<1){
            $rows=10;
        }
        $page = intval($page);
        if ($page<1) {
            $page=1;
        }
        $min = ($page-1)*$rows;
        $listdb = contentModel::where($map)->order($order)->paginate($rows);
        if(!is_object($listdb)){
            return $listdb;
        }
        $listdb->each(function($rs,$key){
            $rs['time'] = format_time(strtotime($rs['create_time']),true);
            $rs['username'] = get_user_name($rs['uid']);
            $rs['icon'] = get_user_icon($rs['uid']);            
            if($rs['reply']){
                $_children = contentModel::where('pid',$rs['id'])->column(true);
                foreach ($_children AS $k=>$v){
                    $_children[$k]['username'] = get_user_name($v['uid']);
                }
                $rs['children'] = $_children;
            }
            return $rs;
        });
        return $listdb;
    }
    

	
	public function delete($ids){
	    if (empty($ids)) {
	        $this -> error('ID有误');
	    }
	    $ids = is_array($ids)?$ids:[$ids];
	    $info = contentModel::get($ids[0]);
	    if (contentModel::destroy($ids)) {
	        $this->success('删除成功',purl('index',['sysid'=>$info['sysid']]));
	    } else {
	        $this->error('删除失败');
	    }
	}
	
}
