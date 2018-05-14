<?php
namespace app\common\traits;
use app\common\builder\Form;
use app\index\model\Label AS LabelModel;
use app\common\traits\AddEditList;

trait LabelEdit {
    use AddEditList;
    protected $tag_id;
    protected $tag_name;
    protected $tag_pagename;
    protected $tag_class_cfg;
    protected $tag_cfg;
    protected $tag_extend_cfg;
    protected $tag_type;
    protected $tag_hide;
    protected $tag_cache_time;
    protected $tag_system_id;
    protected $tag_if_js;
    protected $tag_power_cfg;
    protected $tag_ifdata;
    protected $tag_view_tpl;
    
    /**
     * 取得所有对应的参数
     * @return mixed[]
     */
    protected function get_parameter(){
        return [
                'pagename'=>input('pagename'),
                'name'=>input('name'),
                'ifdata'=>input('ifdata'),
                'div_width'=>input('div_width'),
                'div_height'=>input('div_height'),
                'cache_time'=>input('cache_time'),
                'fromurl'=>input('fromurl'),
        ];
    }
    
    public function delete($name=''){
        if (LabelModel::destroy(['name'=>$name])) {
            $this -> success('删除成功');
        } else {
            $this -> error('删除失败');
        } 
    }
    
    protected function getVal($name,$value,$default=null){
        preg_match('/setTag_([_a-z]+)/', $name,$array);
        $key = $array[1];
        $v = 'tag_'.$key;
        if($value!==null){
            $this->$v = $value;
        }else{
            $this->$v = input('post.'.$key )?input('post.'.$key ):input($key);
        }
        if(empty($this->$v) && $default!==null){
            $this->$v = $default;
        }
    }    
    
    protected function setTag_cfg($value=null){
        if($value===null){
            $data = input();
            //多余的参数过滤掉
            unset($data['fromurl'],$data['view_tpl'],$data['ifdata'],$data['extend_cfg'],$data['id'],$data['name'],$data['cache_time'],$data['type'],$data['system_id'],$data['ifsql'],$data['page']);
            $this->tag_cfg = $data;
        }
        return $this;
    }
    protected function setTag_system_id($value=null){
        $systemid = 0;
        if(config('system_dirname')){
            $systemid = modules_config(config('system_dirname'))['id'];
        }
        $this->getVal(__METHOD__,$value,$systemid);
        return $this;
    }


    protected function setTag_value($class_cfg){
        $this->setTag_class_cfg($class_cfg)
            ->setTag_name()
            ->setTag_pagename()
            ->setTag_cfg()
            ->setTag_extend_cfg()
            ->setTag_type()
            ->setTag_hide()
            ->setTag_cache_time()
            ->setTag_system_id()
            ->setTag_if_js()
            ->setTag_power_cfg()
            ->setTag_view_tpl();
        return $this;
    }

    /**
     * 设置入库数据
     * @param array $array 覆盖默认设置的数据
     * @return array
     */
    protected function get_post_data($array=[]){        
        $_array = [
                'id'=>$this->tag_id,
                'extend_cfg'=>$this->tag_extend_cfg,
                'name'=>$this->tag_name,
                'pagename'=>$this->tag_pagename,
                'cache_time'=>intval($this->tag_cache_time),
                'cfg'=>serialize($this->tag_cfg),
                'class_cfg'=>$this->tag_class_cfg,
                'type'=>$this->tag_type,
                'ifdata'=>intval($this->tag_ifdata),
                'system_id'=>$this->tag_system_id,
                'view_tpl'=>$this->tag_view_tpl,
                'uid'=>intval($this->user['uid']),
                'update_time'=>time(),
        ];
        return array_merge($_array,$array);
    }
    
    //取得某条标签数据
    protected function getTagInfo(){
        //return getArray(LabelModel::get(['name'=>input('name'),'pagename'=>input('pagename')]));       
        return getArray( LabelModel::get(['name'=>input('name')]) );
    }
    
    //保存标签数据
    protected function save($array){
        $result = LabelModel::save_data($array);
        if($result===true){
            echo '<script type="text/javascript">
                    parent.layer.msg("设置成功!"); 
                    parent.layer.close(parent.layer.getFrameIndex(window.name));parent.location.reload();
                    </script>';
            exit;
            //$this->success('设置成功',$this->fromurl);
        }else{
            $this->error('设置失败'.$result);
        }
    }
    
    //自动生成表格
	protected function get_form_table($info,$tab_items) {
	    $tab_items[] = ['number','cache_time','标签缓存时间','单位是秒'];
		$form_table = Form :: make('admin') -> addFormItems($tab_items) -> setFormdata($info); 
		
		// 分组显示
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['nav'])) {
			$form_table -> setTabNav($this -> tab_ext['nav'][0], $this -> tab_ext['nav'][1]);
		} 
		
		// 引入JS文件
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['js_file'])) {
			$form_table -> js($this -> tab_ext['js_file']);
		}
		
		// 额外的JS代码
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['js_code'])) {
		    $form_table -> setExtraJs($this -> tab_ext['js_code']);
		} 
		
		// 设置页面标题
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['page_title'])) {
		    $form_table -> setPageTitle($this -> tab_ext['page_title']);
		}

		// 增加按钮
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['addbtn'])) {
		    $form_table -> addBtn($this -> tab_ext['addbtn']);
		}
		
		// 隐藏按钮
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['hidebtn'])) {
		    $form_table -> hideBtn($this -> tab_ext['hidebtn']);
		} 
		
		// 页面提醒注意事项信息
		empty($this -> tab_ext['warn_msg']) || $form_table -> setPageTips($this -> tab_ext['warn_msg'], 'warning');

		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['trigger'])) {
			foreach($this -> tab_ext['trigger'] AS $rs) {
				$rs[0] && $rs[2] && $form_table -> setTrigger($rs[0], $rs[1], $rs[2], isset($rs[3])?false:true);
			} 
		} 

		return $form_table -> fetch();
	}
	
	
	protected function setTag_name($value=null){
	    $this->getVal(__METHOD__,$value);
	    return $this;
	}
	protected function setTag_pagename($value=null){
	    $this->getVal(__METHOD__,$value);
	    return $this;
	}
	protected function setTag_class_cfg($value=null){
	    $this->getVal(__METHOD__,$value);
	    return $this;
	}
	protected function setTag_extend_cfg($value=null){
	    $this->getVal(__METHOD__,$value,'');
	    return $this;
	}
	protected function setTag_type($value=null){
	    $this->getVal(__METHOD__,$value);
	    return $this;
	}
	protected function setTag_hide($value=null){
	    $this->getVal(__METHOD__,$value,0);
	    return $this;
	}
	protected function setTag_cache_time($value=null){
	    $this->getVal(__METHOD__,$value,0);
	    return $this;
	}
	protected function setTag_if_js($value=null){
	    $this->getVal(__METHOD__,$value,0);
	    return $this;
	}
	protected function setTag_power_cfg($value=null){
	    $this->getVal(__METHOD__,$value,'');
	    return $this;
	}
	protected function setTag_ifdata($value=null){
	    $this->getVal(__METHOD__,$value,'');
	    return $this;
	}
	protected function setTag_view_tpl($value=null){
	    $this->getVal(__METHOD__,$value,'');
	    return $this;
	}

} 
