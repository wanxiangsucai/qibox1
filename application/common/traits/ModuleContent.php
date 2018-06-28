<?php
namespace app\common\traits;

use app\common\builder\Listpage;

trait ModuleContent
{
	use AddEditList;
    
	/**
	 * 新发表内容入口 有的模型可能不使用栏目 而是直接在模型下面发布东西
	 * @param number $mid
	 * @return unknown
	 */
	public  function postnew($mid = 0){
	    if (config('post_need_sort')==true) {
	        return self::chooseSort($mid);
	    }else{
	        return self::chooseModule();
	    }
	}
	
	/**
	 * 新发表内容入口  选择栏目
	 * @param number $mid 必须先指定模型I
	 * @return unknown
	 */
	protected function chooseSort($mid = 0){
	    $sort_list = $this->s_model->getTreeList(0, $mid);
	    $template = getTemplate('postnew');
	    $tpl = $template ? $template : config('post_choose_sort');
	    return $this->fetch($tpl,['sort_list'=>$sort_list]);
	}
	
	/**
	 * 新发表内容入口  选择模型
	 * @return unknown
	 */
	protected function chooseModule(){
	    $model_list = $this->m_model->getList();
	    $template = getTemplate('postnew');
	    $tpl = $template ? $template : config('post_choose_model');
	    return $this->fetch($tpl,['model_list'=>$model_list]);
	}
	
	

	/**
	 * 处理提交的新发表数据
	 * @param number $mid 模型ID
	 * @param number $fid 栏目ID
	 * @param array $data POST表单的数据
	 */
	protected function saveAdd($mid=0,$fid=0,$data=[]){

	    //主要针对多选项的数组进行处理
	    $data = $this->format_post_data($data);
	    
	    if(!empty($this->validate)){
	        // 验证
	        $result = $this->validate($data, $this->validate);
	        if(true !== $result) $this->error($result);
	    }
	    $data['uid'] = $this->user['uid'];
	    $data['mid'] = $this->mid;	    
	    $id = $this->model->addData($this->mid,$data);	
	    
	    if(is_numeric($id)){

			//以下两行是接口
			hook_listen('cms_add_end',$id,['data' =>$data, 'module' =>$this->request->module()]);	    
			$this->end_add($id,$data);

	        $this->success('新增成功', auto_url('index',$fid ? ['fid'=>$fid] : ['mid'=>$mid]) );
	    }else{
	        $this -> error('新增失败:'.$id);
	    }
	}
	
	/**
	 * 保存修改的数据
	 * @param number $mid
	 */
	protected function saveEdit($mid=0,$data=[]){	    
	    
	    //主要针对多选项的数组进行处理
	    $data = $this->format_post_data($data);
	    
	    // 验证
// 	    if(!empty($this->validate)){
// 	        $result = $this->validate($data, $this->validate);
// 	        if(true !== $result) $this->error($result);
// 	    }
	    //$data['ispic'] = empty($data['picurl']) ? 0 : 1 ;

	    $result = $this->model->editData($this->mid,$data);
	    
	    if($result){
	        //以下两行是接口
	        hook_listen('cms_edit_end',$data,['result' =>$result, 'module' =>$this->request->module()]);	        
	        $this->end_edit($data['id'],$data);
	        
	        $this -> success('修改成功', auto_url('index',['mid'=>$mid]) );
	    }else{
	        $this -> error('修改无效');
	    }
	}
	
	/**
	 * 可同时删除多条
	 * @param unknown $ids
	 * @return number
	 */
	protected function deleteContent($ids){
	    $ids = is_array($ids) ? $ids : [$ids];
	    $num = 0;
	    foreach($ids AS $id){
	        if($this->deleteOne($id)){	            
	            $num++;
	        }
	    }
	    return $num;
	}
	
	/**
	 * 获取相关栏目，给做模板时扩展调用,不是必须的
	 * @param string $type
	 * @param number $fid
	 * @return unknown
	 */
	public function get_sort_title($type='top',$fid=0){
	    if ($type=='all') {    //所有栏目
	        $map = [];
	    }elseif($type=='top'){     //一级栏目
	        $map = ['pid'=>0];
	        $array = $this->s_model->getTitleList(['pid'=>0]);
	    }elseif($type=='son'&&$fid){     //子栏目
	        $map = ['pid'=>$fid];
	    }else{
	        $map = [];
	    }
	    $array = $this->s_model->getTitleList($map);
	    return $array;
	}
	
	/**
	 * 获取相关模型，做模板时扩展调用 ,不是必须的
	 * @return unknown
	 */
	public function get_model_title(){
	    $array = $this->m_model->getTitleList();
	    return $array;
	}
	
	/**
	 * 列表数据的筛选字段
	 * @return array[]
	 */
	protected function getEasySearchItems()
	{
	    $array = [];
	    $field_array = $this->f_model->getFields(['mid'=>$this->mid]);	    
	    foreach ($field_array AS $rs){
	        if(!$rs['ifsearch']){
	            continue;
	        }	        
	        if(!in_array($rs['type'], ['radio','select','checkbox'])){
	            continue;
	        }	        
	        $rs['options'] && $rs['options'] = str_array($rs['options']);
	        $array[$rs['name']] = $rs['options'];

	    }
	    return $array;
	}
	
	/**
	 * 获取列表页面要显示的自定义字段
	 * @return unknown[][]|string[][]|mixed[][]
	 */
	protected function getEasyIndexItems($field_array=[])
	{
	    $array = [];
		//$field_array || $field_array = $this->f_model->getFields(['mid'=>$this->mid]);
		$field_array || $field_array = get_field($this->mid);
		
		foreach ($field_array AS $rs){
		    if(!$rs['listshow']){
		        continue;
		    }
			$rs['options'] && $rs['options'] = str_array($rs['options']);
			if(in_array($rs['type'], ['radio','select','checkbox'])){
				$type = 'select';
			}elseif($rs['type']=='image'){
			    $type = 'picture';
			}elseif($rs['type']=='images'){
			    $type = 'pictures';
			}elseif(in_array($rs['type'], ['textarea','ueditor'])){
				$type = 'textarea';
			}elseif($rs['type']=='datetime'){
				$type = 'datetime';
			}elseif($rs['type']=='date'){
				$type = 'date';
			}elseif($rs['type']=='time'){
				$type = 'time';
			}else{
				$type = 'text';
			}
			if($rs['name']=='title'){
			    $array[] = ['title', $rs['title'], 'link',iurl('content/show',['id'=>'__id__']),'_blank'];
			}else{
			    $array[] = [
					$rs['name'],
					$rs['title'],
					 $type,
			         $rs['options'],
			     ];
			}
		}
		return $array;
	}
	
	/**
	 * 某个字段要关联其它字段
	 * @return string[][]|unknown[][]
	 */
	protected function getEasyFieldTrigger(){
	    $array = [];
	    $field_array = $this->f_model->getFields(['mid'=>$this->mid]);
	    foreach ($field_array AS $rs){
	        if($rs['type']=='select'||$rs['type']=='radio'||$rs['type']=='checkbox'){
	            $detail = explode("\r\n",$rs['options']);
	            foreach($detail AS $value){
	                list($v,$b,$otherFields) = explode("|",$value);
	                if($otherFields){
	                    $_fs = explode(',',$otherFields);
	                    foreach($_fs AS $otherField ){
	                        $array[$rs['name']][$otherField][] = $v;
	                    }	                    
	                }
	            }
	        }
	    }
	    $tri = [];
	    foreach($array as $name=>$ar){
	        foreach($ar AS $otherField=>$rs){
	            $tri[] = [$name,implode(',', $rs),$otherField];
	        }	        
	    }
	    return $tri;
	}
	
	/**
	 * 把单选\多选\下拉框架的参数转义为可选项数组
	 * @param string $str 可以是类 app\bbs\model\Sort@getTitleList
	 * @return void|string|array|unknown[]
	 */
	protected function options_2array($str=''){
	    if($str==''){
	        return ;
	    }
	    if(preg_match('/^[a-z]+(\\\[\w]+)+@[\w]+/',$str)){
	        list($class_name,$action,$params) = explode('@',$str);
	        if(class_exists($class_name)&&method_exists($class_name, $action)){
	            $obj = new $class_name;
	            $_params = $params ? json_decode($params,true) : [] ;	            
	            $array = call_user_func_array([$obj, $action], $_params);
	        }
	    }else{
	        $array = str_array($str);
	    }
	    return $array;
	}
	
	/**
	 * 发表与修改表页面的自定义字段信息
	 * @return unknown[][]|array[][]
	 */
	protected function getEasyFormItems()
	{
		$array=[];		
		$field_array = $this->f_model->getFields(['mid'=>$this->mid]);
		foreach ($field_array AS $rs){
			//$rs['options'] && $rs['options'] = str_array($rs['options']);
		    $rs['options'] = $this->options_2array($rs['options']);
			if($rs['type']=='hidden'){   //隐藏域比较特别些
			    $rs['title'] = $rs['value'];
			}
			if($rs['type']=='select'||$rs['type']=='radio'||$rs['type']=='checkbox'){
			    $array[]=[
			            $rs['type'],
			            $rs['name'],
			            $rs['title'],
			            $rs['about'],
			            $rs['options'],			            
			            $rs['value'],
			    ];
			}else{
			    $array[]=[
			            $rs['type'],
			            $rs['name'],
			            $rs['title'],
			            $rs['about'],
			            $rs['value'],
			            $rs['options']
			    ];
			}			
		}
		return $array;
	}
	
	/**
	 * 具体某个栏目的配置信息
	 * @param unknown $fid
	 * @return array
	 */
	protected function sortInfo($fid){
	    $s_info = [];
	    if($fid){
	        $s_info = $this->s_model->getInfoById($fid);
	    }
	    return $s_info;
	}
	
	/**
	 * 列表页取数据
	 * @param array $map
	 * @param array $order
	 * @param array $pages
	 * @return unknown
	 */
	protected function list_page_data($map=[],$order=[],$pages=[]){
	    return $this->getListData($map ,$order ,$pages);
	}

	/**
	 * 获取数据，自定义字段的必须按模型或栏目获取，因为字段不一样。
	 * @param array $map
	 * @param string $order
	 * @param number $rows
	 * @param array $pages
	 * @param string $format 是否对数据进行转义
	 * @return unknown
	 */
	protected function getListData($map=[],$order='',$rows=0,$pages=[],$format=false)
	{
		// 查询
	    $map = array_merge($this->getMap(),$map);
		// 排序
	    $order = $this->getOrder($order);
		// 数据列表
		//$data_list = $this->model->where($map)->order($order)->paginate();		
	    //$table = $this->model->get_model_key().'_content'.$this->mid;
	    $order = trim($order);
	    if(empty($order)){
	        $order = 'list desc ,id desc';
	    }elseif($order == 'list desc'){
	        $order .= ',id desc';
	    }
	    return $this->model->getListByMid($this->mid,$map,$order,$rows,$pages,$format);
	}
		
	/**
	 * 对POST的数据进行转义处理
	 * @param unknown $data
	 * @return number
	 */
	protected function format_post_data($data){
	    //$field_array = $this->f_model->getFields(['mid'=>$this->mid]);
	    $field_array = get_field($this->mid);
	    foreach ($field_array as $rs) {
	        $value = \app\common\field\Post::format($rs,$data);
	        if($value!==null){     //这里要做个判断,MYSQL高版本,不能任意字段随意插入null
	            $data[$rs['name']] = $value;
	        }
// 	        $name = $rs['name'];
// 	        $type = $rs['type'];
// 	        if (!isset($data[$name])) {
// 	            switch ($type) {
// 	                // 开关
// 	                case 'switch':
// 	                    $data[$name] = 0;
// 	                    break;
// 	                case 'checkbox':
// 	                    $data[$name] = '';
// 	                    break;
// 	            }
// 	        } else {
// 	            // 如果值是数组则转换成字符串，适用于复选框等类型
// 	            if (is_array($data[$name])) {
// 	                $data[$name] = implode(',', $data[$name]);
// 	                $type == 'checkbox' && $data[$name] = ','.$data[$name] .',';   //方便搜索 like %,$value,%
// 	            }
// 	            switch ($type) {
// 	                // 开关
// 	                case 'switch':
// 	                    $data[$name] = 1;
// 	                    break;
// 	                case 'images2':
// 	                    //$data[$name] = serialize(array_values($data['images2'][$name]));
// 	                    //$data[$name] = json_encode(array_values($data['images2'][$name])); 
// 	                    break;
// 	                    // 日期时间
// 	                case 'date':
// 	                case 'time':
// 	                case 'datetime':
// 	                    $data[$name] = strtotime($data[$name]);
// 	                    break;
// 	            }
// 	        }
	    }
	    return $data;
	}
	
	
	/**
	 * 获取单条内容信息,修改内容时要用到 内容显示页也会用到
	 * @param number $id 内容ID
	 * @param string $format  是否转义, 修改内容时不允许转义,必须取数据库的原始数据, 内容页也不建议使用
	 * @return unknown
	 */
	protected function getInfoData($id=0,$format=false)
	{
	    return $this->model->getInfoByid($id , $format);
	}
	
	/**
	 * 删除单条内容
	 * @param unknown $id 内容ID
	 * @param number $mid 模型ID,可为空
	 * @return boolean
	 */
	protected function deleteOne($id,$mid=0){
	    $info = $this->getInfoData($id);
	    
	    if ($this->model->deleteData($id,$mid)) {
	        //以下两行是接口
	        hook_listen('cms_delete_end',$id,$this->request->module());	            
	        $this->end_delete($id,$info);
	        
	        return true;
	    }
	}
	
	/**
	 * 会员中心自动生成辅栏目列表页模板
	 * @param unknown $data_list
	 * @param string $tpl
	 * @param array $vars
	 * @return mixed|string
	 */
	protected function makeListInfo($data_list,$tpl='',$vars=[])
	{	    
	    //前台列表页母模型，可以自由定义
	    $template = $tpl ? $tpl : config('automodel_category_listpage');
	    
	    $list_table = Listpage::make()
	    ->setTemplate($template)
	    ->setRowList($data_list)
	    ->addVars($vars);
	    
	    return $list_table->fetch();
	}
	
	/**
	 * 分组显示处理
	 * @param unknown $form_items
	 * @return array|unknown
	 */
	protected function get_group_form($form_items){
	    $_field = $this->f_model->where('mid',$this->mid)->where('nav','<>','')->column('name,nav');
	    
	    if(!empty($_field)){
	        $_group = [];
	        foreach ($form_items AS $key=>$rs){
	            if($_field[$rs[1]]){
	                $_group[$_field[$rs[1]]][] = $rs;
	            }else{
	                $_group['基础信息'][] = $rs;
	            }
	        }
	    }
	    return $_group;
	}
	

	/**
	 * 适用于前台会员 新增加前做检查
	 * @param number $mid 模型ID
	 * @param number $fid 栏目ID
	 * @param array $data POST表单的数据,可以进行再次修改
	 * @return boolean
	 */
	protected function add_check($mid=0,$fid=0,&$data=[]){
	    if(!$this->user){
	        return '请先登录!';
	    }elseif($this->user['groupid']==2){
	        return '很抱歉,你已被列入黑名单,没权限发布,请先检讨自己的言行,再联系管理员解封!';
	    }elseif($mid && !get_field($mid)){
	        return '模型不存在!';
	    }elseif(!$this->admin && config('webdb.can_post_group') && !in_array($this->user['groupid'], config('webdb.can_post_group'))){
	        return '你所在用户组没权限!';
	    }
	    if(!$this->admin && config('webdb.post_auto_pass_group') && !in_array($this->user['groupid'], config('webdb.post_auto_pass_group'))){
	        $data['status'] = 0;
	    }else{
	        $data['status'] = 1;
	    }
	    
	    $s_config = get_sort($fid,'config');
	    if($s_config['allowpost']){
	        if( !$this->admin && !in_array($this->user['groupid'], explode(',',$s_config['allowpost'])) ){
	            return '你所在用户组,无权在此栏目发布!';
	        }
	    }
	    if($s_config['ext_id'] && !$data['ext_id']){
	        $data['ext_id'] = $s_config['ext_id']; //比如论坛栏目自动绑定到圈子
	    }
	    
	    if(!$this->admin){
	        if($data['title']){
	            if(get_cookie('cms_title')==md5($data['title'])){
	                return '请不要重复发表相同的主题!';
	            }
	        }
	        if($data['content']){
	            if(get_cookie('cms_content')==md5($data['content'])){
	                return '请不要重复发表相同的内容!';
	            }
	        }
	    }
	    
	    $data = array_merge(input(),$data);
	    $array = explode(',','view,replynum,usernum,agree,reward,list');
	    foreach($array AS $key){
	        unset($data[$key]);
	    }
	    if(isset($data['map'])&&strstr($data['map'],',')){
	        list($data['map_x'],$data['map_y']) = explode(',', $data['map']);
	    }
	    $data['title'] = filtrate($data['title']);                             //标题过滤
	    $data['content'] = fun('filter@str',$data['content']);     //内容过滤
	    if (fun('ddos@add',$data)!==true) {    //防灌水
	        return fun('ddos@add',$data);
	    }

	    return true;
	}
	
	/**
	 * 适用于前台会员 修改前做检查
	 * @param number $id 内容ID
	 * @param array $info 内容数据
	 * @return boolean
	 */
	protected function edit_check($id=0,$info=[],&$data=[]){
	    if($info['uid']!=$this->user['uid']&&empty($this->admin)){
	        return '你没权限!';
	    }
	    if($data){
    	    if(isset($data['map'])){
    	        list($data['map_x'],$data['map_y']) = explode(',', $data['map']);
    	    }
    	    unset($data['uid'],$data['status'],$data['view'],$data['mid'],$data['list']);
    	    $data['ispic'] = empty($data['picurl']) ? 0 : 1 ;
    	    if(!empty($this->validate)){
    	        $result = $this->validate($data, $this->validate);
    	        if(true !== $result) return $result;
    	    }
	    }
	    $data['title'] = filtrate($data['title']);                             //标题过滤
	    $data['content'] = fun('Filter@str',$data['content']);     //内容过滤	    
	    return true;
	}

	/**
	 * 适用于前台会员 删除前做检查
	 * @param number $id 内容ID
	 * @param array $info 内容数据
	 * @return boolean
	 */
	protected function delete_check($id=0,$info=[]){
	    if($info['uid']!=$this->user['uid']&&empty($this->admin)){
	        return '你没权删除ID:' . $id;
	    }
	    return true;
	}
	
	/**
	 * 同时适用于前台与后台 新增加后做个性拓展
	 * @param number $id 内容ID
	 * @param number $data 内容数据
	 */
	protected function end_add($id=0,$data=[]){
	    set_cookie('cms_title', md5($data['title']));
	    set_cookie('cms_content', md5($data['content']));
	}
	
	/**
	 * 同时适用于前台与后台 修改后做个性拓展
	 * @param number $id 内容ID
	 * @param array $data 内容数据
	 */
	protected function end_edit($id=0,$data=[]){
	}
	
	/**
	 * 同时适用于前台与后台 删除后做个性拓展
	 * @param number $id 内容ID
	 * @param array $info 内容数据
	 */
	protected function end_delete($id=0,$info=[]){
	}
	
}





