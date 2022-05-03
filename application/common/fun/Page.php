<?php
namespace app\common\fun;


class Page{
    
    /**
     * wap底部菜单 有可能是 商铺的菜单.
     * @param array $menu_array 频道强制的底部菜单
     * @return \app\common\fun\unknown[]|\app\common\fun\unknown
     */
    public function foot_menu($menu_array=[]){
        //频道强制的底部菜单开始
        if (defined('FIRST_LOAD') && FIRST_LOAD===true && $menu_array && is_array($menu_array)) {
            set_cookie('foot_menu',json_encode($menu_array));
            return ;
        }
        $foot_menu = get_cookie('foot_menu');
        if ($foot_menu) {
            $menu = json_decode($foot_menu,true);
            if ($menu) {
                return $menu;
            }
        }
        //频道强制的底部菜单结束
        
        $menu = [];        
        if (get_wxappAppid()) {
            $hyid = wxapp_cfg(get_wxappAppid())['aid'];
        }else{
            $hyid = config('webdb.sys_mode_type')==1 ? get_cookie('last_qun_id') : get_cookie('HYID');
        }
        if ($hyid) {
            $menu = cache('qun_menu_1_'.$hyid);
            if( !is_array($menu) ){
                $menu = get_sons(model('qun/menu')->getTreeList(['aid'=>$hyid,'ifshow'=>1,'type'=>1]));
                cache('qun_menu_1_'.$hyid,$menu?:[]);
            }
        }
        $menu || $menu = $this->get_web_menu('wapfoot');
        return $menu;
    }
    
    /**
     * 获取网站头部菜单数据
     * @param string $type 可以取值pc或wap
     * @return unknown[]|unknown
     */
    public function get_web_menu($type=''){
        if($type == 'wap'){
            $_type = [0,2];
        }elseif($type == 'wapfoot'){
            $_type = [3];
        }else{
            $type = 'pc';
            $_type = [0,1];
        }
        $sysname = config('system_dirname');
        if ($sysname) { //查找频道菜单
            $array = cache('web_menu_'.$type.'_'.$sysname);
            if (!is_array($array)) {
                $array = model('admin/webmenu')->getTreeList([
                    'ifshow'=>1,
                    'type'=>['in',$_type],
                    'sysname'=>$sysname,
                ]);
                $array = $array?get_sons($array):[];
                cache('web_menu_'.$type.'_'.$sysname,$array);
            }
            if ($array) { //存在频道菜单
                return $array;
            }            
        }
        
        if (get_wxappAppid()) {
            return [];   //小程序集群不使用系统菜单
        }
        
        $array = cache('web_menu_'.$type);
        if (empty($array)) {
            $array = get_sons(model('admin/webmenu')->getTreeList(['ifshow'=>1,'type'=>['in',$_type],'sysname'=>'_sys_']));
            cache('web_menu_'.$type,$array);
        }
        return $array;
    }
    
    /**
      * PC面包屑导航
      * @param string $link_name
      * @param string $link_url
      * @param number $fid
      */
     public function getNavigation($link_name='',$link_url='',$fid=0){
         if($link_name&&$link_url){
             if(strpos($link_url,'/')!==0&&strpos($link_url,'http')!==0){
                 list($_path,$_parameter) = explode('|',$link_url);
                 $link_url = iurl($_path,$_parameter);
             }
         }
         $template = getTemplate('index@nav');
         if(is_file($template)){
             include($template);             
         }
//          $path = dirname(config('index_style_layout'));
//          if(IN_WAP===true){
//              if(is_file($file = $path.'/wap_nav.htm')){
//                  include($file);
//              }else{
//                  @include($path.'/nav.htm');
//              }          
//          }else{
//              if(is_file($file = $path.'/pc_nav.htm')){
//                  include($file);
//              }else{
//                  @include($path.'/nav.htm');
//              }
//          }
     }
     
}