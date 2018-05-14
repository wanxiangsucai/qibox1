<?php
namespace plugins\comment\index;
use plugins\comment\model\Content AS contentModel;
use app\common\util\Tabel;
use app\common\util\Form;
use app\common\controller\IndexBase;

class Content extends IndexBase
{
    protected $validate = '';

    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new contentModel();
    }
    
    /**
     * 被各频道调用评论数据
     * @param number $sysid 频道模块的ID
     * @param number $aid 频道内容的ID
     * @param number $page 显示第几页
     * @param number $rows 每页显示几条
     * @param number $type 设置为1的时候代表只取已审的，为0显示所有
     * @param string $order 按什么排序
     * @param string $by 升序还是降序
     * @return unknown
     */
    public function get_list($sysid=0,$aid=0,$rows=10,$type=0,$page=1,$order='',$by='desc'){
        $map = [];
        if($type==1){
            $map['status']=1;
        }
        if(in_array($order, ['id','list','create_time'])){
            $order.=$by=='desc'?' desc':' asc';
        }else{
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
        $listdb = contentModel::where($map)->order($order)->limit($min,$rows)->paginate();        
        return $listdb;
    }
    
// 	public function index($pid=0)
// 	{
// 	    $ids = $map = [];
	    
// 	    $listdb = contentModel::where($map)->order( $this->getOrder('list desc,id desc') )->paginate();

// 	    $tab = [
// 	            ['id','ID','text'],
// 	            ['content','评论内容','callback',function($value){
// 	                return get_word(del_html($value), 70);
// 	            }],
// 	            ['sysid','所属模块','text'],
// 	            //['pid','所属'.$this->cfg_fname,'select',$array],
// 	            ['list','排序值','text.edit'],
// 	            ['right_button', '操作', 'btn'],
// 	    ];
	    
// 	    $table = Tabel::make($listdb,$tab)
// 	    ->addTopButton('add',['title'=>'添加评论','href'=>purl('add',['sysid'=>1])])
// 	    //->addTopButton('delete')
// 	    ->addRightButton('edit')
// 	    ->addRightButton('delete')	    
// 	    //->addPageTips('省份管理')
// 	    ->addOrder('id,list')
// 	    ->addPageTitle('管理评论');

//         return $table::fetch();
// 	}
	
	/**
	 * 被各频道调用发布评论接口
	 * @param number $sysid
	 * @param number $aid
	 * @return string
	 */
	public function ajax_add($sysid=0,$aid=0){
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if (contentModel::create($data)) {
	            return 'ok';
	        } else {
	            return 'fail';
	        }
	    }
	}
	
	public function add($sysid=0){
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }	        
	        if (contentModel::create($data)) {
	            $this->success('发表成功', purl('index',['sysid'=>$sysid]) );
	        } else {
	            $this->error('发表失败');
	        }
	    }

	    $form = Form::make()
	    ->addTextarea('content','评论内容[:严禁发布违法言论，后果自负！]')
	    ->addHidden('sysid',$sysid)
	    ->addPageTitle('发布评论');
	    
	    return $form::fetch();
	}

	public function edit($id=0){
	    $info = contentModel::get($id);
	    if ($this->request->isPost()) {
	        $data = $this -> request -> post();	        
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }	        
	        if (contentModel::update($data)) {
	            $this->success('修改成功',purl('index',['sysid'=>$data['sysid']]));
	        } else {
	            $this->error('修改失败');
	        }
	    }

	    $form = Form::make([],$info)
	    //->setPageTips('修改省份')
	    ->addPageTitle('修改评论')	    
	    ->addTextarea('content','评论内容')
	    ->addHidden('sysid')
	    ->addHidden('id');

	    return $form->fetch();
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
