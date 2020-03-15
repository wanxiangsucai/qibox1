<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use app\index\model\Labelhy AS Model;

class Labelmodels extends IndexBase
{
    protected function make_wap($path='',$tags='',$cfg=[]){

        $_path = str_replace(['/','.'], '___', $path);
        $basename = end(explode('___',$_path));
        $div='';
        $id = config('system_dirname')=='qun'?intval(input('id')):0; //避免CMS内容页也当作圈子处理
        $js="label_model_init('{$_path}','{$tags}',{$id});\r\n";
        if (SHOW_SET_LABEL===true) {
            $div = "
                <div class='headle'>
                        <em class='up glyphicon glyphicon-arrow-up'>上移</em>  
                        <em class='down glyphicon glyphicon-arrow-down'>下移</em> 
                        <em class='margin-size glyphicon glyphicon-resize-vertical'>边距</em>
                        <em class='delete fa fa-times-circle'> 删除</em> 
                        <em class='copy fa fa-copy'> 复制</em>                        
                    </div>
                ";
        }
        $top = $cfg['top']?:0;
        $bottom = $cfg['bottom']?:0;
        $left = $cfg['left']?:0;
        $right = $cfg['right']?:0;
        $div = "<div class='c_diypage diy-{$basename} diypath-{$_path} diyKey-{$_path}-{$tags}' data-path='{$_path}' data-tags='{$tags}' data-top='{$top}' data-bottom='{$bottom}' data-left='{$left}' data-right='{$right}'>
                    $div
                   </div>";
        return [$js,$div];
    }
    
    /**
     * 生成唯一固定数值
     * @param string $string
     * @return number
     */
    protected function str2num($string=''){
        $j = 0;
        $num = strlen($string);
        for($i=0;$i<$num;$i++){
            $j +=ord(substr($string, $i , 1));
        }
        return 10000+$j;
    }
    
    /**
     * 获取模块的标签
     * @param array $tag_array
     * @return string
     */
    public function get_label($tag_array=[]){
        $cfg = unserialize($tag_array['cfg']);
        
        $_tags = $this->str2num($cfg['tag_name']);
        
        if (strstr($cfg['where'],'model=$')) {
            $_array = explode(',', $cfg['model']);
        }else{
            $_array = explode(',', str_replace('model=', '', $cfg['where']));
        }
        
        foreach($_array AS $k=>$v){
            $v = trim($v," \r\n\t");
            if (empty($v)) {
                unset($_array[$k]);
            }else{
                $_array[$k] = $v;
            }
        }
        
        $js_warp = $div_warp =  '';
        if($tag_array['extend_cfg']!=''){ //数据库有记录
            $array = json_decode($tag_array['extend_cfg'],true)?:[];
            foreach ($array AS $rs){
                $detail = $this->make_wap($rs['path'],$rs['tags'],$rs);
                $js_warp .= $detail[0];
                $div_warp .= $detail[1];
            }
        }else{  //数据库没记录
            foreach ($_array AS $tpl){
                $detail = $this->make_wap($tpl,$_tags);
                $_tags++;
                $js_warp .= $detail[0];
                $div_warp .= $detail[1];
            }
        }

        $id = config('system_dirname')=='qun'?intval(input('id')):0; //避免CMS内容页也当作圈子处理
        $jsurl = STATIC_URL.'js/label_model.js';
        $index = 'index';
        if (class_exists("app\\".config('system_dirname')."\\index\\Labelmodels")) {
            $index = config('system_dirname');
        }elseif (class_exists("app\\common\\upgrade\\U25")){
            \app\common\upgrade\U25::up();
        }
        $label_model_url = urls($index.'/labelmodels/show');
        $label_model_saveurl = urls($index.'/labelmodels/save');
        $code = '';
        if (SHOW_SET_LABEL===true) {
            $code = '恢复(添加)模块<br><br>';
        }
        return $code."
                <script type=\"text/javascript\" src=\"{$jsurl}\"></script>
                <script type='text/javascript'>
                var label_model_url = '{$label_model_url}';
                var label_model_saveurl = '{$label_model_saveurl}';
                {$js_warp}
                </script>
                <div class='diy_pages {$cfg['tag_name']}' data-tagname='{$cfg['tag_name']}' data-pagename='{$cfg['page_name']}' data-id='{$id}'>{$div_warp}\r\n</div>";

    }
    
    /**
     * 显示标签数据
     * @param number $id
     * @param string $path
     * @param string $tags
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function show($id=0,$path='',$tags=''){
        if(strstr($path,'___')){
            $path = str_replace('___', '/', $path).'.'.config('template.view_suffix');
            if (is_file(TEMPLATE_PATH.'index_style/'.$path)) {
                $path = TEMPLATE_PATH.'index_style/'.$path;
            }else{
                $path = TEMPLATE_PATH.$path;
            }
        }else{
            $path = TEMPLATE_PATH.'model_style/default/'.$path.'.'.config('template.view_suffix');
        }
        
        if(!is_file($path)){
            return $this->ok_js(['content'=>"<script>layer.alert('".str_replace(TEMPLATE_PATH, '', $path)."碎片模板不存在!')</script>"]);
        }
        $qun = $id ? fun('qun@getByid',$id) : [];
        $this->assign('info',$qun);
        $this->assign('id',$id);
        $this->assign('hy_id',$id); //不在圈子目录的话,就必须要指定hy_id
        $this->assign('tags',$tags);
        $content = $this->fetch($path);
        return $this->ok_js(['content'=>$content]);
    }
    
    /**
     * 前台JS保存模块配置,并不是标签
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function save(){
        $data = $this->request->post();
        if (empty($data['id']) && empty($this->admin)) {
            return $this->err_js('你没权限');
        }
        if ($data['id']) {
            $qun = fun('qun@getByid',$data['id']);
            if ($qun['uid']!=$this->user['uid']) {
                return $this->err_js('不是你的圈子,你没权限');
            }
        }

        $map = [
                'ext_id'=>intval($data['id']),
                'name'=>$data['tagname'],
                'pagename'=>$data['pagename'],
        ];
        $result = false;
        $info = Model::where($map)->find();
        if ($info) {
            $array = ['extend_cfg'=>json_encode($data['model'])];
            $result = Model::where('id',$info['id'])->update($array);
        }else{
            $array = [
                    'name'=>$data['tagname'],
                    'pagename'=>$data['pagename'],
                    'class_cfg'=>'app\index\controller\Labelmodels@get_label',
                    'ext_id'=>intval($data['id']),
                    'ext_sys'=>M('id')?:0,
                    'type'=>'labelmodel',
                    'uid'=>$this->user['uid'],
                    'system_id'=>intval($data['id']),
                    'cfg'=>serialize([
                        'tag_name'=>$data['tagname'],
                        'page_name'=>$data['pagename'],                    
                    ]),
                    'extend_cfg'=>json_encode($data['model']),
            ];
            $result = Model::create($array);
        }
        if ($result) {
            return $this->ok_js([],'更新成功');
        }else{
            return $this->err_js('无效更新');
        }
    }
}
