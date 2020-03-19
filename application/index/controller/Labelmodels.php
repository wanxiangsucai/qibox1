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
        $id = intval(input('id'));
        $hyid = config('system_dirname')=='qun'?$id:0; //避免CMS内容页也当作圈子处理
        $js="label_model_init('{$_path}','{$tags}',{$hyid},$id);\r\n";
        if (SHOW_SET_LABEL===true) {
            $div = "
                <div class='headle'>
                        <a href='javascript:' class='up glyphicon glyphicon-arrow-up'>上移</a>  
                        <a href='javascript:' class='down glyphicon glyphicon-arrow-down'>下移</a> 
                        <a href='javascript:' class='margin-size glyphicon glyphicon-resize-vertical'>边距</a>
                        <a href='javascript:' class='delete fa fa-times-circle'> 删除</a> 
                        <a href='javascript:' class='copy fa fa-copy'> 复制</a>                        
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
        
        $_path = '';
        foreach($_array AS $k=>$v){
            $v = trim($v," \r\n\t");
            if (empty($v)) {
                unset($_array[$k]);
            }else{
                if(strstr($v,'/')){
                    $_path = dirname($v);
                }elseif(!strstr($v,'/') && $_path){
                    $v = $_path.'/'.$v;
                }
                $_array[$k] = $v;
            }
        }
        
        static $model_num = 0;
        
        $js_warp = $div_warp =  '';
        if($tag_array['extend_cfg']!=''){ //数据库有记录
            $array = json_decode($tag_array['extend_cfg'],true)?:[];
            foreach ($array AS $rs){
                $model_num++;
                $detail = $this->make_wap($rs['path'],$rs['tags'],$rs);
                $js_warp .= $detail[0];
                $div_warp .= $detail[1];
            }
        }else{  //数据库没记录
            foreach ($_array AS $tpl){
                $model_num++;
                $detail = $this->make_wap($tpl,$_tags);
                $_tags++;
                $js_warp .= $detail[0];
                $div_warp .= $detail[1];
            }
        }

        $id = config('system_dirname')=='qun'?intval(input('id')):0; //避免CMS内容页也当作圈子处理
        
        $code = '';
        if (SHOW_SET_LABEL===true) {
            $code = '<div class="diy-page-model-btn">恢复(添加)模块<br><br></div>';
        }
        static $if_loadjs = false;
        if($if_loadjs==false){
            $if_loadjs = true;
            $index = 'index';
            if (class_exists("app\\".config('system_dirname')."\\index\\Labelmodels")) {
                $index = config('system_dirname');
            }elseif (class_exists("app\\common\\upgrade\\U25")){
                \app\common\upgrade\U25::up();
            }
            $label_model_url = urls($index.'/labelmodels/show');
            $label_model_saveurl = urls($index.'/labelmodels/save');
            $code .="<script type=\"text/javascript\" src=\"".STATIC_URL.'js/label_model.js'."?d\"></script>
                <script type='text/javascript'>
                var label_model_url = '{$label_model_url}';
                var label_model_saveurl = '{$label_model_saveurl}';
                var label_model_num = 0;
                </script>"; 
        }
        return $code."
                <script type='text/javascript'>
                {$js_warp}
                label_model_num = {$model_num};
                </script>
                <div class='diy_pages {$cfg['tag_name']}' data-tagname='{$cfg['tag_name']}' data-pagename='{$cfg['page_name']}' data-id='{$id}'>{$div_warp}\r\n</div>";

    }
    
    /**
     * 显示标签数据
     * @param number $id 圈子ID,内容ID不用ID,避免跟圈子冲突
     * @param string $path
     * @param string $tags
     * @param number $ids 内容ID
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function show($id=0,$path='',$tags='',$ids=0){
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
        $info = [];
        if ($ids) { //内容ID,也有可能是圈子ID
            $info = cache('tag_info-'.config('system_dirname').'-'.$ids);
        }elseif($id){   //这个是圈子ID,不是内容ID
            $info = fun('qun@getByid',$id);
        }
        $this->assign('info',$info);
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
