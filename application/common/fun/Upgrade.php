<?php
namespace app\common\fun;

/**
 * 系统升级要用到的函数
 */
class Upgrade{
    /**
     * 本地文件的的版本信息
     * 同步升级版本信息的判断
     * @return string
     */
    public function local_edition(){
        $data = [];
        $array = modules_config();
        foreach ($array AS $rs){
            list($time,$version) = explode("\t",$rs['version']);
            $data[] = $rs['keywords'] . '-' . $version . '-' . $rs['version_id'] . '-m';
        }
        $array = plugins_config();
        foreach ($array AS $rs){
            list($time,$version) = explode("\t",$rs['version']);
            $data[] = $rs['keywords'] . '-' . $version . '-' . $rs['version_id'] . '-p';
        }
        $_hook = [];
        $hook_plugins = cache('hook_plugins');
        foreach ($hook_plugins AS $rs){
            if($_hook[$rs['hook_class']] )continue;
            list($time,$version) = explode("\t",$rs['version']);
            $data[] = str_replace('\\', '__', $rs['hook_class']) . '-' . $version . '-' . $rs['version_id'] . '-h';
            $_hook[$rs['hook_class']] = true;
        }
        $style_array = \app\common\util\Style::get_style('index');
        foreach ($style_array AS $key=>$name){
            $version = $version_id = '';
            $data[] = $key . '-' . $version . '-' . $version_id . '-index_style';
        }
        $style_array = \app\common\util\Style::get_style('member');
        foreach ($style_array AS $key=>$name){
            $version = $version_id = '';
            $data[] = $key . '-' . $version . '-' . $version_id . '-member_style';
        }
        $style_array = \app\common\util\Style::get_style('admin');
        foreach ($style_array AS $key=>$name){
            $version = $version_id = '';
            $data[] = $key . '-' . $version . '-' . $version_id . '-admin_style';
        }
        $style_array = \app\common\util\Style::get_style('qun');
        foreach ($style_array AS $key=>$name){
            $version = $version_id = '';
            $data[] = $key . '-' . $version . '-' . $version_id . '-qun_style';
        }
        $array = \app\common\model\Market::get_list();
        foreach ($array AS $rs){
            if(in_array($rs['type'],['admin_style','index_style','member_style','qun_style'])&&!is_dir(TEMPLATE_PATH.$rs['type'].'/'.$rs['keywords'])){
                \app\common\model\Market::destroy($rs['id']);   //用户把目录删除的话,就把数据库的信息也清空掉
            }
            list($time,$version) = explode("\t",$rs['version']);
            $data[] = $rs['keywords'] . '-' . $version . '-' . $rs['version_id'] . '-'.$rs['type'];
        }
        return implode(',', $data);
    }
}