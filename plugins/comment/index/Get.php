<?php
namespace plugins\comment\index;
use plugins\comment\model\Content AS contentModel;
use app\common\controller\IndexBase;

class Get extends IndexBase
{
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new contentModel();
    }
    
    
    /**
     * 被各频道调用评论数据
     * @param string $sys 频道模块的ID或目录名
     * @param number $aid 频道内容的ID
     * @param number $rows 每页显示几条
     * @param number $status 设置为1的时候代表只取已审的，为0显示所有
     * @param string $order 按什么排序
     * @param string $by 升序还是降序
     * @return unknown
     */
    public function index($sys='',$aid=0,$pid=0,$rows=10,$status=1,$order='',$by=''){
        
        if($pid>0){  //取引用回复
            $map = [
                'pid'=>$pid,
            ];
        }else{
            if (!$sys) {
                return $this->err_js('没有指定频道');
            }
            if (!is_numeric($sys)) {
                $module = modules_config($sys);
            }
            if (!$module) {
                return $this->err_js('频道不存在');
            }
            $map = [
                'aid'=>$aid,
                'sysid'=>$module['id'],
                'pid'=>0,
            ];
        }        
        
        if($status<1){
            $status = 1;
        }
        $map['status'] = ['>=',$status];
        
        if(!in_array($order, ['id','list','create_time','agree','reply'])){
            $order = 'list desc,id desc';   //普通回复的话,时间晚的在前面
            $by = '';
        }elseif(!in_array($by,['asc','desc'])){
            $by = 'desc';
        }
        $rows = intval($rows);
        if($rows<1){
            $rows=10;
        }
        $listdb = getArray(contentModel::where($map)->order($order,$by)->paginate($rows));
        if(!$pid){
            unset($map['pid']);
        }        
        foreach($listdb['data'] AS $key=>$rs){
            $rs = $this->format_content($rs);
            $rs['children'] = [];
            if($rs['reply']>0){
                $_children = contentModel::where('pid',$rs['id'])->order('id asc')->column(true);
                foreach ($_children AS $k=>$v){
                    $_children[$k] = $this->format_content($v);
                }
                $rs['children'] = array_values($_children);
            }
            $listdb['data'][$key] = $rs;
        }
        $listdb['totals'] = contentModel::where($map)->count('id');
        if ($pid) {
            $listdb['info'] = $this->format_content(getArray(contentModel::where('id',$pid)->where('status','>=',0)->find()));
        }
        $listdb['data'] = array_values($listdb['data']);
        return $this->ok_js($listdb);
    }
    
    protected function format_content($rs=[]){
        $rs['time'] = format_time(strtotime($rs['create_time']),true);
        $rs['username'] = get_user_name($rs['uid']);
        $rs['icon'] = get_user_icon($rs['uid']);        
        return $rs;
    }
	
}
