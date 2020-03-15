<?php
namespace app\qun\index;

use app\common\controller\IndexBase;
use app\qun\model\Content;

/**
 * 用户选择菜单链接
 */
class GetLink extends IndexBase
{
    public function index(){
        return $this->fetch();
    }
    
    /**
     * 获取可用的链接
     * @param number $id 商铺ID
     * @return string[][]|unknown[][]
     */
    public function get_menu_link($id=0){
        $info = Content::getInfoByid($id,false);   //圈子信息
        if (empty($info)) {
            return [];
        }
        
        $menu = [];
        
        //处理频道
        foreach(modules_config() AS $rs){
            if($rs['keywords'] == 'qun'||$rs['keywords'] == 'weibo'||$rs['keywords'] == 'hy'||$rs['keywords'] == 'search'){
                continue ; //当前qun模型就不要显示在菜单里
            }
            $url = '';
            $have_showurl = $have_posturl = false;
            if (is_file(APP_PATH.$rs['keywords'].'/config.php')) {
                $cfg = include(APP_PATH.$rs['keywords'].'/config.php');
                if ($cfg['qun_url']) {  //指定了URL
                    $have_showurl = true;
                    if( is_array($cfg['qun_url'][0]) ){ //多个菜单的情况
                        foreach ($cfg['qun_url'] AS $vs){
                            $url = iurl('qun/'.$rs['keywords'].'/'.$vs[0] , $vs[1].'='.$id);
                            $menu[] = $this->menu_cfg('m',$url,$rs,$vs[2]);
                        }                        
                    }else{
                        $url = iurl('qun/'.$rs['keywords'].'/'.$cfg['qun_url'][0],$cfg['qun_url'][1].'='.$id);
                        $menu[] = $this->menu_cfg('m',$url,$rs,$cfg['qun_url'][2]);
                    }                   
                }elseif($cfg['qun_url']===false){
                    $have_showurl = true;
                }
                if ($cfg['post_url']) {  //指定了URL
                    $have_posturl = true;
                    if( is_array($cfg['post_url'][0]) ){ //多个菜单的情况
                        foreach ($cfg['post_url'] AS $vs){
                            $url = iurl('qun/'.$rs['keywords'].'/'.$vs[0] , $vs[1].'='.$id);
                            $menu[] = $this->menu_cfg('m',$url,$rs,$vs[2]);
                        }                        
                    }else{
                        $url = iurl('qun/'.$rs['keywords'].'/'.$cfg['post_url'][0],$cfg['post_url'][1].'='.$id);
                        $menu[] = $this->menu_cfg('m',$url,$rs,$cfg['post_url'][2]);
                    }
                }elseif($cfg['post_url']===false){
                    $have_posturl = true;
                }
            }
            if ( empty($url) && !is_file(APP_PATH.$rs['keywords'].'/model/Module.php') ) {
                continue ; //没有自定义模型的,就忽略
            }
            if (empty($have_showurl)) {
                $_model = model_config(null,$rs['keywords']);
                if (count($_model)>1) {
                    foreach($_model AS $qs){
                        $url = iurl('qun/'.$rs['keywords'].'/index',['mid'=>$qs['id'],'id'=>$id]);
                        $menu[] = $this->menu_cfg('m',$url,$rs,$qs['title']);
                    }
                }else{
                    $url = iurl('qun/'.$rs['keywords'].'/index',['id'=>$id]);
                    $menu[] = $this->menu_cfg('m',$url,$rs);
                }                
            }
            if (empty($have_posturl)) {
                $_model = model_config(null,$rs['keywords']);
                if (count($_model)>1) {
                    foreach($_model AS $qs){
                        $url = murl($rs['keywords'].'/content/add',['mid'=>$qs['id'],'ext_id'=>$id]);
                        $menu[] = $this->menu_cfg('m',$url,$rs,'发布'.$qs['title']);
                    }
                }else{
                    $url = murl($rs['keywords'].'/content/add',['ext_id'=>$id]);
                    $menu[] = $this->menu_cfg('m',$url,$rs,'发布'.$rs['name']);
                }               
            }            
        }
        
        //处理插件
        foreach(plugins_config() AS $rs){
            $url = '';
            $have_showurl = $have_posturl = false;
            if (is_file(PLUGINS_PATH.$rs['keywords'].'/config.php')) {
                $cfg = include(PLUGINS_PATH.$rs['keywords'].'/config.php');
                if ($cfg['qun_url']) {  //指定了URL
                    $have_showurl = true;
                    if( is_array($cfg['qun_url'][0]) ){ //多个菜单的情况
                        foreach ($cfg['qun_url'] AS $vs){
                            $url = iurl('qun/'.$rs['keywords'].'/'.$vs[0] , $vs[1].'='.$id,'index');
                            $menu[] = $this->menu_cfg('p',$url,$rs,$vs[2]);
                        }                        
                    }else{
                        $url = iurl('qun/'.$rs['keywords'].'/'.$cfg['qun_url'][0],[$cfg['qun_url'][1]=>$id]);
                        $menu[] = $this->menu_cfg('p',$url,$rs,$cfg['qun_url'][2]);
                    }                    
                }elseif($cfg['qun_url']===false){
                    $have_showurl = true;
                }
                if ($cfg['post_url']) {  //指定了URL
                    $have_posturl = true;
                    if( is_array($cfg['post_url'][0]) ){ //多个菜单的情况
                        foreach ($cfg['post_url'] AS $vs){
                            $url = iurl('qun/'.$rs['keywords'].'/'.$vs[0] , $vs[1].'='.$id);
                            $menu[] = $this->menu_cfg('p',$url,$rs,$vs[2]);
                        }                        
                    }else{
                        $url = iurl('qun/'.$rs['keywords'].'/'.$cfg['post_url'][0],$cfg['post_url'][1].'='.$id);
                        $menu[] = $this->menu_cfg('p',$url,$rs,$cfg['post_url'][2]);
                    }
                }elseif($cfg['post_url']===false){
                    $have_posturl = true;
                }
            }
            if ( empty($url) && !is_file(PLUGINS_PATH.$rs['keywords'].'/model/Module.php') ) {
                continue ; //没有自定义模型的,就忽略
            }
            if (empty($have_showurl)) {
                $url = iurl('qun/'.$rs['keywords'].'/index',['id'=>$id]);
                $menu[] = $this->menu_cfg('p',$url,$rs);
            }
            if (empty($have_posturl)) {
                $url = purl($rs['keywords'].'/content/add',['ext_id'=>$id],'member');
                $menu[] = $this->menu_cfg('p',$url,$rs,'发布'.$rs['name']);
            }
        }
        return $menu;
    }
    
    private function menu_cfg($type='m',$url='',$rs=[],$title=''){
        return [
            'type'=>$type,
            'title'=>$title?:$rs['name'],
            'icon'=>$rs['icon'],
            'keywords'=>$rs['keywords'],
            'config'=>$rs,
            'url'=>$url,
        ];
    }
}
