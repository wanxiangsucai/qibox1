<?php
namespace app\index\model;
use think\Model;

/**
 * 系统标签模型, 不是用户自定义的标签模型
 */
class Label extends Model
{
    protected $table = '__LABEL__';
    protected $autoWriteTimestamp = true;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';
    
    /**
     * 保存系统设置的标签参数
     * @param unknown $data
     * @return string|boolean
     */
    public static function save_data($data){
//         if(empty($data['pagename'])){
//             return '缺少pagename参数';
//         }else
        if(empty($data['name'])){
            return '缺少name参数';
        }elseif(empty($data['class_cfg'])){
            return '缺少class_cfg参数';
        }elseif(empty($data['type'])){
            return '缺少type参数';
        }
        
        $info = self::get(['name'=>$data['name']]);
        unset($data['id']);
        if($info){
            if(self::update($data,['id'=>$info['id']])){
                return true;
            }
        }else{
            if(self::create($data)){
                return true;
            }
        }
    }
    
    /**
     * 站外调用标签数据,比如APP或小程序
     * @param string $tag_name 变量名
     * @param number $page_num 页码
     * @return array|unknown|void
     */
    public static function app_get_data($tag_name='' , $page_num=0){        
        //获取站外标签所有配置参数
        $page_tags = cache('config_app_tags');
        if(empty($page_tags)){
            $page_tags = self::where(['if_js'=>1])->column(true,'name');
            cache('config_app_tags',$page_tags);
        }        
        $tag_config = $page_tags[$tag_name];        
        if(empty($tag_config)){
            return [];
        }
        //具体某个标签从数据库取数据
        $tag_data = self::run_tag_class($tag_config , $page_num);

        if(is_array($tag_data)&&$tag_data['format_data']){   //同时存在HTML数据，比如图片<img src=$pic>
            $tag_config['data'] = $tag_data['data'];
        }else{
            $tag_data = getArray($tag_data);
            if(is_array($tag_data['data'])){
                $tag_config['data'] = $tag_data['data'];
                unset($tag_data['data']);
                $tag_config['pages'] = $tag_data;   //分页数据
            }else{
                $tag_config['data'] = $tag_data;
            }
        }
        return $tag_config;
    }
    
    /**
     * 取得某个标签的具体数据及相关配置参数，同时也会把整个页面的标签配置参数缓存起来。
     * @param string $tag_name 标签名
     * @param string $page_name 模板页
     * @param number $page_num 第几页，AJAX显示更多用到
     * @param array $live_parameter 跟随页面变化的动态参数
     * @return void|void|unknown|array|NULL[]
     */
    public static function get_tag_data_cfg($tag_name='' , $page_name='' , $page_num=0 , $live_parameter=[]){
        
        //获取当前页面的所有标签的数据库配置参数，如果一个页面有很多标签的时候，比较有帮助，如果标签只有一两个就帮助不太大。
        $page_tags = cache('config_page_tags_'.$page_name);
        if(empty($page_tags)){
            //比如头尾公共标签多处调用的话，$page_name值是反复变化的,所以要用OR查询
            $page_tags = self::where(['pagename'=>$page_name])->whereOr(['name'=>$tag_name])->column(true,'name');
            //cache('config_page_tags_'.$page_name,$page_tags,3600);
        }
        
        //取得具体某个标签的配置数据
        if(!empty($page_tags)&&!empty($page_tags[$tag_name])){
            $tag_config = $page_tags[$tag_name];
        }
        //else{
            //比如头尾公共标签多处调用的话，$page_name值是反复变化的
            //$tag_config = getArray(self::where(['name'=>$tag_name])->find());
       // }
        if(empty($tag_config)){
            return ;    //新标签，不存在配置参数，所以也不用执行下面的数据取值
        }
        
        if($live_parameter){    //跟随页面变化的动态参数
            $array = unserialize($tag_config['cfg']);
            $array = array_merge($array,$live_parameter);
            $tag_config['cfg'] = serialize($array);
        }
        
        if($tag_config['class_cfg']=='@'){
            return $tag_config;                     //列表页标签不需要在这里处理数据
        }
        //具体某个标签从数据库取数据
        $tag_data = self::run_tag_class($tag_config , $page_num);
        if(!empty($tag_data)){
            if(is_array($tag_data)&&$tag_data['format_data']){   //同时存在HTML数据，比如图片<img src=$pic>
                $tag_config['format_data'] = $tag_data['format_data'];
                $tag_config['data'] = $tag_data['data'];
            }else{
                $tag_data = getArray($tag_data);
                if ($tag_data['s_data']) {
                    $tag_config['s_data'] = $tag_data['s_data'];    //要单独定义，不能使用data数据，避免一些密码字段也暴露出来。
                }
                if(is_array($tag_data['data'])){
                    $tag_config['data'] = $tag_data['data'];
                    unset($tag_data['data'],$tag_data['s_data']);
                    $tag_config['pages'] = $tag_data;   //分页数据
                }else{
                    $tag_config['data'] = $tag_data;
                }                
            }
        }
        return $tag_config;
    }
    
    /**
     * 通过标签类得到相应的数据，有可能是数组也有可能是HTML数据
     * @param unknown $tag_config 标签配置参数
     * @param number $page_num 页码,第几页
     * @return void|unknown
     */
    protected static function run_tag_class($tag_config , $page_num=0){
        static $class_array = [];   //同一个类就没必要重复实例化
        if (empty($tag_config['class_cfg'])) {
            return ;
        }
        list($class_name,$action) = explode('@',$tag_config['class_cfg']);
        if(empty($class_name) || empty($action)){
            return ;
        }elseif (!class_exists($class_name)){
            return ;
        }elseif (!method_exists($class_name,$action)){
            return ;
        }
        if(empty( $class_array[$class_name] )){
            $obj = new $class_name;
        }else{
            $obj = $class_array[$class_name];
        }
        return $obj->$action($tag_config,$page_num);
    }
}






