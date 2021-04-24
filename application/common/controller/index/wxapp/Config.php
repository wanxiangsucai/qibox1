<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
use app\common\model\Config as ConfigModel;


/**
 * 小程序或APP调用的配置参数数据
 *
 */
abstract class Config extends IndexBase
{
    
    protected function _initialize()
    {
        parent::_initialize();
    }
    
    /**
     * 常用接口参数
     * @param number $fid
     * @param string $type
     * @param number $rows
     * @return void|unknown|\think\response\Json
     */
    public function index($fid=0,$type='',$rows=10){
        $app_config = cache('app_config');
        if(empty($app_config)){
            $app_config = ConfigModel::getConfig('',null,1);
            cache('app_config',$app_config);
        }
        
        $dispatch = $this->request->dispatch();
        //把相应的插件或频道模块的二维数组插入到一维数组去使用
        if($dispatch['module'][1]=='plugin'&&$dispatch['module'][2]=='execute'){
            $plugin_name = input('plugin_name');
            if($plugin_name && is_array($app_config['P__'.$plugin_name])){
                $app_config = array_merge($app_config,$app_config['P__'.$plugin_name]);
            }
        }elseif($dispatch['module'][0] && $app_config['M__'.$dispatch['module'][0]]){
            $app_config = array_merge($app_config,$app_config['M__'.$dispatch['module'][0]]);
        }
        
        $array = [
            'webdb'=>$app_config,
            'group'=>getGroupByid(),
            'userInfo'=>$this->user ? \app\common\fun\Member::format($this->user,$this->user['uid']) : '',
        ];
        return $this->ok_js($array);        
    }
    
    

}













