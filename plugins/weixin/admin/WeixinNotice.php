<?php
namespace plugins\weixin\admin;

use app\common\controller\AdminBase; 

use app\common\traits\AddEditList;

use plugins\weixin\model\WeixinNotice as Model;

class WeixinNotice extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
        'page_title'=>'订阅/模板消息模板管理',
	    'help_msg'=>'<a href="https://www.kancloud.cn/php168/x1_of_qibo/2595257" target="_blank">个性模板消息的配置使用有一点点复杂，请先阅读教程。点击查看教程</a>',
	];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new Model(); 
		$this->list_items = [
		    ['title', '描述', 'text'],
		    
		    ['keyword', '关键字', 'text'],
		    
			['template_id', '模板ID', 'text'],
		    
			['data_field', '使用字段', 'callback', function($value,$data){
			    $str = '';
			    $array = json_decode($value,true);
			    foreach ($array AS $rs){
					if($rs['title3']){
						$str .= "{$rs['title1']}：{$rs['title2']}<br>";
					}			        
			    }
			    return $str;
                },'__data__'], 
            ['type', '类型', 'select2',['小程序订阅消息','公众号订阅消息','公众号模板消息']],
            ['status', '启用与否', 'switch'],
		];
		$this->form_items = [
		    ['radio', 'type', '消息类型','',['小程序订阅消息','公众号订阅消息','公众号模板消息'],2],
		    ['text','keyword', '程序调用关键字','程序中会根据此关键字调用消息模板'],
		    ['text', 'template_id', '模板ID'],
		    ['text', 'title', '标注描述'],
		    ['radio', 'status', '是否启用','',['禁用','启用'],1],
// 		    ['array4', 'data_field', '内容字段'
// 		        ,'名称：比如下单时间、联系人（可随意）。<br>系统变量名：即程序中使用的变量标志<br>微信变量名：比如first、remark、keyword1、thing1、date2、time15，<br>指定内容：你可以个性化定义内容，不要程序写死的。（一般为空，因为需要程序配合）'
// 		        ,['title'=>['名称（可随意）','系统变量名','微信变量名','指定内容(一般为空,随意)']]
// 		        ,'[{"title1":"标题","title2":"title","title3":"first","title4":""},{"title1":"附注","title2":"content","title3":"remark","title4":""}]'
// 		    ],
		    ['array3', 'data_field3', '内容字段'
		        ,'第一项：微信字段变量名：比如常用first、remark、keyword1、thing1、date2、time15，<br>第二项：系统字段内容：即程序中使用的变量标志，可以多个变量+文字组合。也可以不要变量，只用固定的文字<br>第三项：备注，与微信中的字段描述一样即可，主要是方便自己查看，没实际意义，比如下单时间、联系人。'
		        ,['title'=>['微信字段变量名','系统字段内容','字段备注']]
		        ,'[{"title1":"first","title2":"标题是{title}","title3":"标题"},{"title1":"remark","title2":"内容是{content}","title3":""},{"title1":"keyword1","title2":"{time}","title3":"时间"},{"title1":"keyword2","title2":"{username}","title3":"用户"}]'
		    ],
		    ['textarea', 'about', '系统字段备注','用到的字段如上,你也可以多个组合起来放在微信的某个字段里比如“作者{username}发表的{title}”，特别是系统字段不够用的话，就可以组合起来。或者直接用固定的文字'],
		];
		
		
		if ($this->request->isPost()){
		    $data = $this->request->post();
		    $_ar = [];
		    $ar3 = json_decode($data['data_field3'],true);
		    foreach($ar3 AS $rs){
		        $_ar[] = [
		            'title1'=>$rs['title3'],
		            'title2'=>'a_'.rands(5),
		            'title3'=>$rs['title1'],
		            'title4'=>$rs['title2'],
		        ];
		    }
		    $this->request->post([
		        'data_field'=>json_encode($_ar,JSON_UNESCAPED_UNICODE),
		    ]);
		}
		
	}
	
	
	public function index() {
	    if (!table_field('weixinnotice','about')) {
	       into_sql("ALTER TABLE `qb_weixinnotice` ADD `about` TEXT NOT NULL COMMENT '备注';");
	    }
	    $listdb = $this->getListData($map = [], $order = '');
	    return $this -> getAdminTable($listdb);
	}
	
	public function edit($id = null) {
	    if (empty($id)) $this -> error('缺少参数');
	    $info = $this -> getInfoData($id);
	    

	    
	    $array = json_decode($info['data_field'],true);
	    if(!$info['about']){
	        $info['about'] = "";
	        foreach($array AS $rs){
	            $info['about'].=$rs['title1'].":{".$rs['title2']."}\r\n";
	        }
	    }
	    $_data = [];
	    foreach($array AS $rs){
	        if($rs['title3']){
	            $_data[] = [
	                'title1'=>$rs['title3'],
	                'title2'=>$rs['title4']?:'{'.$rs['title2'].'}',
	                'title3'=>$rs['title1'],
	            ];
	        }
	    }
	    $info['data_field3'] = json_encode($_data);
	    
	    return $this -> editContent($info);
	}
	
	public function add(){
	    if($this->request->isPost()){
	        $data = $this->request->post();
	        if(!$data['keyword']){
	            $this->error('程序调用关键字不能为空！');
	        }elseif(!$data['template_id']){
	            $this->error('模板ID不能为空！');
	        }elseif(!$data['data_field']){
	            $this->error('内容字段不能为空！');
	        }
	        $map = [
	            'keyword'=>$data['keyword'],
	        ];
	        if($this->model->where($map)->find()){
	            $this->error('当前关键字已被使用了，请更换一个！');
	        }
	        
	        if ($type = $this->model->create($data)) {
	            $this->end_act();
	            $this->success('添加成功', 'index');
	        } else {
	            $this->error('添加失败');
	        }
	    }
	    return $this->addContent();
	}
	
	
	protected function end_act($type='',$data=[]){
	    cache('weixin_notice_template',null);
	}

}
