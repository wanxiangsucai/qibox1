<?php
namespace app\common\model;
use think\Model;
use think\Db;

abstract class C extends Model
{
    //模型关键字，也即目录名
    public static $model_key;
    //索引表
    public static $base_table;
    
    //前置方法
//     protected $beforeActionList = [
//             'Init_Key' =>  ['except'=>'initialize,scopeInitKey'],
//     ];
    
    //为了调用initialize初始化,生成数据表前缀$model_key
    protected static function scopeInitKey(){}
    protected function initialize()
    {
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        self::$model_key = $array[0][1];
        self::$base_table = $array[0][1].'_content';        
    }
    
    
    /**
     * 获取数据表关键字,也即程序目录名
     * @return unknown
     */
    public static function getKey(){
        empty(self::$model_key) && self::InitKey();
        return self::$model_key;
    }
    
    /**
     * 获取索引表
     * @return string
     */
    public static function getTable(){
        empty(self::$model_key) && self::InitKey();
        return self::$base_table;
    }
    
    /**
     * 获取其它数据表
     * @param string $name 比如可以是 sort 或 field 等等
     * @return string
     */
    public static function getTableBy($name=''){
        empty(self::$model_key) && self::InitKey();
        return self::$model_key.'_'.$name;
    }
    
    
    /**
     * 通过模型MID得到对应的数据主表名 注意:不是索引表,索引表是 self::base_table
     * @param number $mid 内容ID
     * @return string
     */
    public static function getTableByMid($mid=0){
        empty(self::$model_key) && self::InitKey();
        return self::$base_table.$mid;
    }
    
    /**
     * 查询用户的记录条数
     * @param unknown $uid 用户ID
     * @param number $mid 模型ID 为0的时候,查询所有模型
     * @return number|number|string
     */
    public static function user_info_num($uid,$mid=1){
        if($mid){
            $table = self::getTableByMid($mid);
            if (empty($table)) {
                return 0;
            }
        }else{
            $table = self::$base_table;
        }        
        return Db::name($table)->where('uid',$uid)->count('id');
    }
    
    /**
     * 删除单条内容
     * @param number $id 内容ID
     * @param number $mid 模型ID,可为空
     * @return boolean
     */
    public static function deleteData($id=0,$mid=0){
        self::InitKey();
        if (empty($id)) {
            return false;
        }
        if (empty($mid)) {
            $mid = self::getMidById($id);
        }        
        $table = self::getTableByMid($mid);
        $info = static::getInfoByid($id);
        
        //先删除主表记录
        try {
            hook_listen('cms_model_delete_begin',$info,$id); //删除信息的钩子,在这里可以做判断是否允许删除,或者做附件删除处理
            Db::name(self::$base_table)->where('id',$id)->delete();
        } catch(\Exception $e) {
            return false;
        }
        
        //删除内容表
        try {
            $result = Db::name($table)->where('id',$id)->delete();
        } catch(\Exception $e) {
            return false;
        }
        if ($result) {
            hook_listen('cms_model_delete_end',$info,$id); //删除信息后的钩子,可以做一些记录,或者做附件删除处理
            return true;
        }
    }
    
    /**
     * 更新单条内容,是editData的另一种简便用法,可以直接设置ID,第二项是要修改内容数组
     * @param number $id 主题ID,也可以是数组
     * @param array $data 修改的内容数组
     * @return boolean
     */
    public static function updates($id=0,$data=[]){
        self::InitKey();
        if (is_array($id)&&empty($data)) {
            $data = $id;
            $id = $data['id'];
        }else{
            $data['id'] = $id;
        }
        if (empty($data['mid'])) {
            $data['mid'] = self::getMidById($id);
        }
        return static::editData($data['mid'],$data);
    }
    
    /**
     * 更新单条内容信息
     * @param number $mid 模型ID可以为空
     * @param array $data 要更新的数据,id是必须的
     * @return boolean
     */
    public static function editData($mid=0,$data=[]){
        self::InitKey();        
        if (empty($mid)) {
            $mid = self::getMidById($data['id']);
            $table = self::getTableByMid($mid);
            //$table = self::getTableById($data['id']);
        }else{
            $table = self::getTableByMid($mid);     //内容主表
        }
        if(empty($table)){
            return false;
        }
        if (isset($data['picurl'])) {
            $data['ispic'] = $data['picurl'] ? 1 : 0;
        }
        $data['update_time'] = time();        
        //try {
            hook_listen('cms_model_edit_begin',$data,$mid);   //修改信息前的钩子,可以设置禁止修改或者是把修改内容做替换处理
            $result = Db::name($table)->update($data);            
       // } catch(\Exception $e) {
        //    return false;
       // }
        
        if ($result) {
            hook_listen('cms_model_edit_end',$data,$mid);  //成功修改信息后的钩子
            return true;
        }
    }
    
    /**
     * 新增加内容 mid 参数是必须的,不然不知道是哪个模型
     * @param number $mid 模型ID是必须的.
     * @param array $data 要插入的数据
     * @return boolean|unknown 若插入成功,会返回ID值, 否则返回报错信息
     */
    public static function addData($mid=0,&$data=[]){
        self::InitKey();
        if (empty($mid)) {
            return 'mid不存在';
        }
        $data['mid'] = $mid;
        $data['uid'] || $data['uid'] = intval(login_user('uid'));
        //先要往索引表插一条记录做索引用 , 模型表的ID以主表的ID为标准 
        try {
            hook_listen('cms_model_add_begin',$data,$mid);    //入库前的钩子,可以在这里设置禁止发布信息
            $data['id'] = Db::name( self::$base_table )->insertGetId($data);
        } catch(\Exception $e) {
            return '索引表插入失败';
        }        
        if( empty($data['id']) ){
            return '新增ID不存在';
        }
        $data['create_time'] || $data['create_time'] = time();
        $data['list'] = time();
        $data['ip'] = get_ip();
        $data['picurl'] && $data['ispic'] = 1 ;
        
        $table = self::getTableByMid($mid); //内容主表
        try {
            $result = Db::name($table)->insert($data);  //insert 成功只返回true 不会返回ID值 insertGetId才返回ID值,或者给insert补全其它参数
        } catch(\Exception $e) {
            Db::name( self::$base_table )->where('id',$data['id'])->delete();
            return '内容表数据插入失败';
        }
        
        if ($result) {
            hook_listen('cms_model_add_end',$data,$data['id']);  //成功发表信息后的钩子
            return $data['id'];
        } else {
            return false;
        }
    }
    
    /**
     * 点赞
     * @param number $id
     * @return boolean
     */
    public static function addAgree($id=0){
        self::InitKey();
        $table = self::getTableById($id);
        if($table){
            $info = static::getInfoByid($id);
            hook_listen('cms_model_agree_begin',$info,$id);   //点赞前的钩子,可以做是否允许点赞处理
            if( Db::name($table)->where('id','=',$id)->setInc('agree',1) ){
                hook_listen('cms_model_agree_end',$info,$id);   //成功点赞后的钩子,可以做信息通知处理
                return true;
            }
        }
    }
    
    /**
     * 更新浏览量
     * @param unknown $id 内容ID
     */
    public static function addView($id){
        self::InitKey();
        $table = self::getTableById($id);
        if($table){
            return Db::name($table)->where('id','=',$id)->setInc('view',1);
        }        
    }
    
    /**
     * 更新回复数
     * @param unknown $id 内容ID
     * @param string $is_add 增加还是减少
     */
    public static function addReply($id,$is_add=true){
        self::InitKey();
        $table = self::getTableById($id);
        if($table){
            if($is_add==true){
                Db::name($table)->where('id','=',$id)->setInc('replynum',1);
            }else{
                Db::name($table)->where('id','=',$id)->setDec('replynum',1);                
            }            
        }
    }
    
    /**
     * 对某个字段进行加减数
     * @param unknown $id 内容ID
     * @param string $field 字段名
     * @param string $is_add 默认是增加,也可以设置 false 做减少
     */
    public static function addField($id,$field='',$is_add=true){
        self::InitKey();
        $table = self::getTableById($id);
        if($field==''){//table_field($table,$field)
            return ;
        }
        if($table){
            if($is_add==true){
                Db::name($table)->where('id','=',$id)->setInc($field,1);
            }else{
                Db::name($table)->where('id','=',$id)->setDec($field,1);
            }
        }
    }
    
    /**
     * 通过内容ID得到模型的mid
     * @param unknown $id 内容ID
     * @return mixed|PDOStatement|string|boolean|number
     */
    public static function getMidById($id){
        self::InitKey();
        static $mids = [];
        if(empty($mids[$id])){
            $mids[$id] = Db::name(self::$base_table)->where('id','=',$id)->value('mid');
        }
        return $mids[$id];
    }
    
    /**
     * 通过栏目ID获取模型的mid
     * @param unknown $fid 栏目ID
     * @return mixed|PDOStatement|string|boolean|number
     */
    public static function getMidByFid($fid){
        self::InitKey();
        return Db::name(self::$model_key.'_sort')->where('id','=',$fid)->value('mid');
    }
    
    /**
     * 通过内容ID获取栏目的fid
     * @param unknown $id 内容ID
     * @return void|mixed|PDOStatement|string|boolean|number
     */
    public static function getFidById($id){
        self::InitKey();
        $mid = self::getMidById($id);
        if (empty($mid)) {
            return ;
        }
        return Db::name(self::getTableByMid($mid))->where('id','=',$id)->value('fid');
    }
    
    
    /**
     * 获取所有模型的内容
     * @return \think\Paginator|array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function getAll($map=[],$order="id desc",$rows=0,$pages=[]){
        self::InitKey();        
        $array = Db::name(self::$base_table)->where($map)->order($order)->paginate(
                empty($rows)?null:$rows,    //每页显示几条记录
                empty($pages[0])?false:$pages[0],
                empty($pages[1])?['query'=>input('get.')]:$pages[1]
                );
        foreach ($array AS $key=>$ar){
            //因为是跨表，所以一条一条的读取，效率不太高
            $info = Db::name(self::getTableByMid($ar['mid']))->where('id','=',$ar['id'])->find();
            if ($info) {
                $array[$key] = $info;
            }
        }
        return $array;
    }
    
    
    /**
     * 根据UID获取所有模型的内容,主要用在会员中心
     * @param number $uid
     * @param number $rows
     * @return \think\Paginator|\app\common\model\unknown|array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function getListByUid($uid=0,$rows=20,$pages=[]){
        self::InitKey();
        $array = Db::name(self::$base_table)->where('uid',$uid)->order('id','desc')->paginate(
                empty($rows)?null:$rows,    //每页显示几条记录
                empty($pages[0])?false:$pages[0],
                empty($pages[1])?['query'=>input('get.')]:$pages[1]
                );
        foreach ($array AS $key=>$ar){
            //因为是跨表，所以一条一条的读取，效率不太高
            $info = Db::name(self::getTableByMid($ar['mid']))->where('id','=',$ar['id'])->find();
            if ($info) {
                $info = static::format_data($info,$cfg=[],$dirname='',$sort_array=[]);
                $array[$key] = $info;
            }
        }
        return $array;
    }
    
    /**
     * 根据UID获取所有模型的内容索引,主要用在SNS推送
     * @param number $uid
     * @param number $rows
     * @param number $min
     * @return array
     */
    public static function getIndexByUid($uid=0,$rows=50,$min=0){
        self::InitKey();
        $array = Db::name(self::$base_table)->where('uid',$uid)->order('id','asc')->limit($min,$rows)->column(true);
        return $array;
    }
    
    /**
     * 通过ID获取某条内容数据
     * @param number $id 内容ID
     * @param string $format 是否转义,比如修改内容就不要转义
     * @return void|\app\common\model\unknown|array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function getInfoByid($id=0,$format=FALSE){
        self::InitKey();
        $mid = self::getMidById($id);
        if (empty($mid)) {
            return ;
        }
        $info = Db::name(self::getTableByMid($mid))->where('id','=',$id)->find();
        if($info){
            return $format ? static::format_data($info) : $info;
        }        
    }
    
    /**
     * 通过ID获取下一条内容数据 也可以获取上一条内容
     * @param unknown $id 内容ID
     * @param string $type 为sort值即同栏目的下一条数据,为model值即同一个模型的下一条数据,为空值即不限
     * @param array $info 把栏目ID或模型ID传进来的话 ,就可以减少查询数据库
     * @param array $map 可以限制条件查询
     * @return mixed|PDOStatement|string|boolean|number
     */
    public static function getNextByid($id,$type='sort',$info=[],$map=[],$next=true){
        self::InitKey();
        $id = intval($id);
        if ($next==true) {
            $mod = '<';
            $order = 'id DESC';
        }else{
            $mod = '>';
            $order = 'id ASC';
        }
        if($type=='sort' && ($id||$info) ){
            if(empty($info['fid'])){
                $info = static::getInfoByid($id);
            }
            $mid = $info['mid'];
            return Db::name(self::getTableByMid($mid))->where($map)->where('id',$mod,$id)->where('fid','=',$info['fid'])->order($order)->limit(1)->value('id');
        }elseif($type=='model' && ($id||$info) ){
            if(empty($info['mid'])){
                $mid = self::getMidById($id);
            }else{
                $mid = $info['mid'];
            }
            return Db::name(self::getTableByMid($mid))->where($map)->where('id',$mod,$id)->order($order)->limit(1)->value('id');    
        }else{
            return Db::name(self::$base_table)->where($map)->where('id',$mod,$id)->order($order)->limit(1)->value('id');
        }        
    }
    
    /**
     * 下一个随机ID
     * @param unknown $id
     * @param string $type
     * @param array $info
     * @param array $map
     * @return mixed|PDOStatement|string|boolean|number
     */
    public static function getNextRandByid($id=0,$type='sort',$info=[],$map=[]){
        self::InitKey();
        $id = intval($id);
        $mod = '<>';
        if($type=='sort' && ($id||$info)){  //在某个栏目里获取下一条内容
            if(empty($info['fid'])){
                $info = static::getInfoByid($id);
            }
            $mid = $info['mid'];
            return Db::name(self::getTableByMid($mid))->where($map)->where('id',$mod,$id)->where('fid','=',$info['fid'])->orderRaw('rand()')->limit(1)->value('id');
        }elseif($type=='model' && ($id||$info)){
            if(empty($info['mid'])){
                $mid = self::getMidById($id);
            }else{
                $mid = $info['mid'];
            }
            return Db::name(self::getTableByMid($mid))->where($map)->where('id',$mod,$id)->orderRaw('rand()')->limit(1)->value('id');
        }else{
            return Db::name(self::$base_table)->where($map)->where('id',$mod,$id)->orderRaw('rand()')->limit(1)->value('id');
        }
    }
    
    /**
     * 根据MID 设置条件查找数据条数
     * @param number $mid
     * @param array $map
     * @return number|number|string
     */
    public static function getNumByMid($mid=0,$map=[]){
        self::InitKey();
        if (empty($mid)) {
            return 0;
        }
        return Db::name(self::getTableByMid($mid))->where($map)->count('id');
    }
    
    /**
     * 通过ID得到对应的数据表名
     * @param number $id 内容ID
     * @return string
     */
    public static function getTableById($id=0){
        self::InitKey();
        return self::getTableByMid(self::getMidById($id));
    }
    
    /**
     * 按地图位置远近获取数据
     * @param number $mid 模型ID
     * @param array $map    查询条件
     * @param string $point    地图点坐标
     * @param number $rows
     * @param array $pages
     * @param string $format
     * @return \think\Paginator
     */
    public static function getListByMap($mid=0,$map=[],$point='100,20',$rows=0,$pages=[],$format=true){
        self::InitKey();
        list($x,$y) = explode(',',$point);
        $x = (float)$x;
        $y = (float)$y;
        $data_list = Db::name(self::getTableByMid($mid))->where($map)->field("*,(POW( `map_x`-$x,2 )+POW(`map_y`-$y,2)) AS map_point")->order('map_point asc')->paginate(
                empty($rows)?null:$rows,    //每页显示几条记录
                empty($pages[0])?false:$pages[0],
                empty($pages[1])?['query'=>input('get.')]:$pages[1]
                );
        
        if($format){
            $data_list->each(function($rs,$key){
                return static::format_data($rs);
            });
        }
        return $data_list;
    }
    

    /**
     * 按模型获取数据
     * @param number $mid 模型ID
     * @param array $map 查询条件,数组
     * @param string $order 排序方式
     * @param number $rows 每页取几条值
     * @param array $pages 
     * @return \think\Paginator
     */    
    public static function getListByMid($mid=0,$map=[],$order='',$rows=0,$pages=[],$format=true)
    {
        self::InitKey();
        $order  = trim($order);
        if(empty($order)){
            $order = 'list desc ,id desc';
        }elseif($order == 'list desc'){
            $order .= ',id desc';
        }
        $data_list = Db::name(self::getTableByMid($mid))->where($map)->order($order)->paginate(
                empty($rows)?null:$rows,    //每页显示几条记录
                empty($pages[0])?false:$pages[0],
                empty($pages[1])?['query'=>input('get.')]:$pages[1]
                );
        
        if($format){
            $data_list->each(function($rs,$key){
                return static::format_data($rs);
            });
        }
        return $data_list;
    }
    
    /**
     * 取出的数据进行转义处理
     * @param array $rs 数据库取出的数据
     * @param array $cfg 定义标题长度或内容长度
     * @param string $_dirname 频道所在目录
     * @param array $_sort_array 栏目数据
     * @return unknown
     */
    protected static function format_data($info=[] , $cfg=[] , $_dirname='' , $_sort_array=[]) {
        //self::InitKey(); //2018-5-19日修改,有的服务器会报错Cannot instantiate abstract class app\common\model\C
        if($_dirname){
            $dirname = $_dirname;
        }else{
            //                 preg_match_all('/([_a-z]+)/',get_called_class(),$array);
            //                 $dirname = $array[0][1];
            $dirname = self::$model_key;
        }
        
        static $m_or_p = [];
        if( empty($m_or_p[$dirname]) ){
            $m_or_p[$dirname] = modules_config($dirname) ? 'module' : 'plugin';
        }
        
//         static $field_db = [];
//         if(empty($field_db[$dirname])){
//             $field_db[$dirname] = get_field($info['mid'],$dirname);
//         }
//         //字段转义
//         foreach ($field_db[$dirname] AS $_field=>$rs){
//             if(!isset($info[$_field])){
//                 continue ;
//             }
//             if($rs['type']=='radio'||$rs['type']=='select'||$rs['type']=='checkbox'){
//                 $_farray = str_array($rs['options']);
//                 if($rs['type']=='radio'||$rs['type']=='select'){
//                     $info[$_field] = $_farray[$info[$_field]];
//                 }else{
//                     $_far = [];
//                     $_fv = explode(',',$info[$_field]);
//                     foreach($_fv AS $_fvs){
//                         if($_fvs===''){
//                             continue;
//                         }
//                         $_far[] = $_farray[$_fvs];
//                     }
//                     $info[$_field] = implode(',', $_far);
//                 }
//             }elseif($rs['type']=='date'){
//                 $info[$_field] = date('Y-m-d',$info[$_field]);
//             }elseif($rs['type']=='datetime'){
//                 $info[$_field] = date('Y-m-d H:i',$info[$_field]);
//             }
//         }
        
//         static $sort_array = [];    //用数组的原因是考虑到像主页同时会调用多个频道的数据
//         if(empty($sort_array[$dirname])){    //避免反复执行
//             if(!empty($_sort_array)){
//                 $sort_array[$dirname] = $_sort_array;
//             }else{
//                 $sort_array[$dirname] = sort_config($dirname);    //获取栏目数据
//             }
//         }
        $sort_array = sort_config($dirname);    //获取栏目数据
        
        $info = fun('field@format',$info,'','list',$dirname);     //对原始数据进行转义前台显示
        
        if($info['pics']){    //CMS图库模型特别处理
        //if(empty($info['picurl']) && $info['pics']){    //CMS图库模型特别处理
//             $_picarray = [];
//             $info['pics'] = json_decode($info['pics'],true);
//             foreach ($info['pics'] AS $ps){
//                 $_picarray[] = $ps['picurl'];
//             }
//             $info['picurl'] = implode(',', $_picarray);
            $info['picurl'] = $info['pics'];
        }
        
        if($info['picurl']){
//             $detail = explode(',',$info['picurl']);
//             $info['picurl'] = tempdir($detail[0]);
//             foreach($detail AS $value){
//                 $value && $info['picurls'][] = tempdir($value);
//             }
            if(is_array($info['picurl'])){
                $value = $info['picurl'];
                unset($info['picurl']);
                $info['picurl'] = $value[0]['picurl'];
                $info['picurls'] = $value;
            }
        }
        
        if(count($info['picurls'])>1){
            $info['image_type']=2;
        }elseif($info['picurl']){
            $info['image_type']=1;
        }else{
            $info['image_type']=0;
        }
        
        
        $cfg['leng'] && $info['title'] = get_word($info['full_title'] = $info['title'], $cfg['leng']);
        $info['full_content'] = $info['content'];   //原始内容数据
        $info['content'] = preg_replace('/<([^<]*)>/is',"",$info['content']);	//把HTML代码过滤掉
        $cfg['cleng'] && $info['content'] = get_word($info['content'], $cfg['cleng']);
        $info['DIR'] = $dirname;
        $info['url'] = iurl($dirname.'/content/show',['id'=>$info['id']],true,false,$m_or_p[$dirname]);
        $info['sort_name'] = $sort_array[$info['fid']]['name'];
        $info['mid_name'] = model_config($info['mid'],$dirname)['title'];
        $info['sort_url'] = iurl($dirname.'/content/index',['fid'=>$info['fid']],true,false,$m_or_p[$dirname]);
        $info['time'] = date('Y-m-d H:i',$info['full_time'] = $info['create_time']);
        $info['username'] = get_user_name($info['uid']);
        $info['user_icon'] = get_user_icon($info['uid']);
        $info['user_url'] = get_url('user',$info['uid']);
        return $info;
    }
    
    /**
     * 标签调用数据
     * 特别提醒!! 标签设置了mid=-1 不按模型取数据的话,同时设置了where条件,有可能取出的数据比期望的要少,甚至为0条
     * @param string $tag_array 标签配置参数
     * @param number $page 页码
     * @return void|\app\common\model\unknown[]|array[]|\think\db\false[]|PDOStatement[]|string[]|\think\Model[]|array
     */
    public static function labelGetList($tag_array=[] , $page=0){
        self::InitKey();
        $cfg = unserialize($tag_array['cfg']);

        $mid = $cfg['mid'];     //self::$model_key
        if ($mid>1&&empty(model_config($mid,self::$model_key))) {   //模型不存在,可能已被删除
            $mid = 1;
        }
        $rows = $cfg['rows'] ? $cfg['rows'] : 5;
        $page = intval($page);
        if ($page<1) {
            $page=1;
        }        
        $min = ($page-1)*$rows;
//         $order = $cfg['order_name'] ? $cfg['order_name'] : 'id';
//         $by = $cfg['order_by'] ? $cfg['order_by'] : 'desc';
        $order = $cfg['order'] ? $cfg['order'] : 'id';
        $by = $cfg['by'] ? $cfg['by'] : 'desc';
        $data = [];
        $map = [];
//         preg_match_all('/([_a-z]+)/',get_called_class(),$array);
//         $dirname = $array[0][1];
        $dirname = self::$model_key;
        $sort_array = sort_config($dirname);    //获取栏目数据
        
        if(is_numeric($cfg['fid'])){
            $mid = $sort_array[$cfg['fid']]['mid'];
            //$map['fid'] = $cfg['fid'];
            $map['fid'] = ['in',array_values(get_sort($cfg['fid'],'sons'))];    //把所有子栏目也读取出来
        }
        
        if($cfg['fidtype']==2){ //跟随栏目动态变化
            $fids = input('fid');
            $mid = $sort_array[$fids]['mid'];
            $map['fid'] = $fids;
        }elseif($cfg['fids']){
            $fids = is_array($cfg['fids'])?$cfg['fids']:explode(',', $cfg['fids']);
            if(count($fids)>1){
                $map['fid'] = ['in',$fids];
            }elseif($fids && count($fids)==1){
                $map['fid'] = $fids[0];
            }
        }        
        
        //只调用自己的数据,一般只适合用在会员中心
        static $uid = null;
        if($cfg['onlymy']){
            if($uid===null){
                $uid = login_user('uid');
            }
            $map['uid'] = intval($uid);
        }elseif(is_numeric($cfg['uid'])){
            $map['uid'] = $cfg['uid'];
        }
        
        $cfg['status'] && $map['status'] = ['>=',$cfg['status']];       //1是已审,2是推荐,已审要把推荐一起调用,所以要用>=
        $cfg['ispic'] && $map['ispic'] = 1; //只取有图片的数据,如果没有指定模型的话,不能处理
//         static $model_list = null;
//         if($mid && $model_list === null){
//             $model_list = model_config($mid,self::$model_key);  //模型配置文件
//         }
//         if($model_list){
        if($mid){   //指定模型取数据,效率更高,并且也能准确取出想要的指定数量
            if(is_numeric($cfg['ext_id'])){
                $map['ext_id'] = $cfg['ext_id'];
            }
            if($cfg['where']){  //用户自定义的查询语句
                $_array = fun('label@where',$cfg['where'],$cfg);
                if($_array){
                    $map = array_merge($map,$_array);
                }
            }
            $whereor = [];
            if($cfg['whereor']){  //用户自定义的查询语句
                $_array = fun('label@where',$cfg['whereor'],$cfg);
                if($_array){
                    $whereor = $_array;
                }
            }
            //$data = Db::name(self::getTableByMid($mid))->where($map)->whereOr($whereor)->limit($min,$rows)->order($order,$by)->column(true);
            if(strstr($order,'rand()')){
                $data = Db::name(self::getTableByMid($mid)) -> where($map) -> whereOr($whereor) -> orderRaw('rand()') -> paginate($rows,false,['page'=>$page]);
            }else{
                $data = Db::name(self::getTableByMid($mid)) -> where($map) -> whereOr($whereor) -> order($order,$by) -> paginate($rows,false,['page'=>$page]);
           }
           $array = getArray($data);
        }else{  //全频道全模型取数据, 如果有不符合条件的话, 就会导致取出的数据量比指定的要少,甚至有可能取出0条数据,不能精准
            //务必要先选择模型，跨表查询效率非常低            
//             $list = Db::name(self::$base_table)->limit($min,$rows)->order('id',$by)->column('id,mid');
//             foreach($list AS $id=>$mid){
//                 $data[$id] = Db::name(self::getTableByMid($mid))->where(['id'=>$id])->find();
//             }

            if($cfg['where']){  //用户自定义的查询语句
                $_array = fun('label@where',$cfg['where'],$cfg);
                if($_array){
                    $map = array_merge($map,$_array);
                }
            }
            $_map = [];
            foreach($map AS $key=>$value){
                if (in_array($key, ['id','uid'])) {
                    $_map[$key] = $value;
                }
            }
            
            $data = Db::name(self::$base_table)->where($_map)->field('id,mid')->order('id',$by)->paginate($rows,false,['page'=>$page]);
//             $data->each(function($rs,$key){
//                 $vs = Db::name(self::getTableByMid($rs['mid']))->where(['id'=>$rs['id']])->find();
//                 return $vs;
//             });
            $array = getArray($data);//print_r($array) ;exit;
            foreach($array['data'] AS $key=>$rs){
                $vs = Db::name(self::getTableByMid($rs['mid']))->where(['id'=>$rs['id']])->where($map)->find();
                if (empty($vs)) {
                    unset($array['data'][$key]);
                }else{
                    $array['data'][$key] = $vs;
                }                
            }
        }
        foreach($array['data'] AS $key=>$rs){
            $array['data'][$key] = static::format_data($rs,$cfg,$dirname,$sort_array);
        }
        return $array;
        
//         if(empty($data)){
//             return ;
//         }
//         foreach ($data AS $key=>$rs){
//             $data[$key] = self::format_data($rs,$cfg,$dirname,$sort_array);
//         }
//         return $data;
    }
    
    /**
     * 辅栏目的标签
     * @param array $tag_array
     * @param number $page
     * @return array|NULL[]|unknown|\app\common\model\unknown
     */
    public static function labelGetCategoryList($tag_array=[] , $page=0){
        self::InitKey();
        $cfg = unserialize($tag_array['cfg']);
        
        $rows = $cfg['rows']>0 ? $cfg['rows'] : 5;
        $page = intval($page);
        if ($page<1) {
            $page=1;
        }
        //$min = ($page-1)*$rows;
        $order = $cfg['order'] ?: 'id';
        $by = $cfg['by'] ?: 'desc';
        $map = [];
        $dirname = self::$model_key;
        $sort_array = sort_config($dirname);    //获取栏目数据
        
        if($cfg['fidtype']==1){ //跟随栏目动态变化
            $map['cid'] = input('fid') ?: 0;
        }elseif($cfg['fid']){
            $map['cid'] = $cfg['fid'];
        }
        
        if($cfg['where']){  //用户自定义的查询语句
            $_array = fun('label@where',$cfg['where'],$cfg);
            if($_array){
                $map = array_merge($map,$_array);
            }
        }
        
        if(strstr($order,'rand()')){
            $data = Db::name(self::$model_key.'_info') -> where($map) -> orderRaw('rand()') -> paginate($rows,false,['page'=>$page]);
        }else{
            $data = Db::name(self::$model_key.'_info') -> where($map) -> order("$order $by") -> paginate($rows,false,['page'=>$page]);
        }        
        $data->each(function(&$rs,$key){
            $vs = static::getInfoByid($rs['aid']);
            $rs = $vs;
            return $vs;
        });        
        $array = getArray($data);
        foreach($array['data'] AS $key=>$rs){
            $array['data'][$key] = static::format_data($rs,$cfg,$dirname,$sort_array);
        }
        return $array;
    }
    

    
}