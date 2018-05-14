<?php
namespace app\common\model;
use think\Model;
use util\Tree;
use think\Db;

//辅栏目内容表
abstract class Info extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table;// = '__FORM_MODULE__';
    
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';
    
    protected static $base_table;
    protected static $model_key;
    protected static $table_pre;
    
    //为了调用initialize初始化,生成数据表前缀$model_key
    protected static function scopeInitKey(){}
    protected function initialize()
    {
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        self::$model_key = $array[0][1];
        self::$base_table = $array[0][1].'_content';
        self::$table_pre = config('database.prefix');
        //字段表，带数据表前缀如qb_form_field
        $this->table = self::$table_pre.self::$model_key.'_info';
    }
    
    public static function save_data($ids=[],$fid){
        $data = [];
        foreach($ids AS $id){
            if(Db::name(self::$model_key.'_info')->where('aid','=',$id)->where('cid','=',$fid)->select()){
                continue;
            }
            $data[] = ['aid'=>$id,'cid'=>$fid];
        }
        if ($data && Db::name(self::$model_key.'_info')->insertAll($data) ) {
            return count($data);
        }
        return false;
    }
    
    /**
     * 通过内容ID获取下一条内容数据
     * @param unknown $aid 内容ID
     * @param unknown $fid 分类ID
     * @return unknown
     */
    public static function getNextAidByAid($aid,$fid){
        self::InitKey();
        $ck = 0;
        $listdb = Db::name(self::$model_key.'_info')->where('cid','=',$fid)->order('list DESC,id DESC')->column('id,aid');
        foreach($listdb AS $key=>$value){
            if($value==$aid){
                $ck++;
            }elseif($ck){
                return $value;
            }
        }
    }
    
    //获取所有栏目的名称及ID
    public static function getTitleList($where=[])
    {
        static $list = NULL;
        if($list==NULL){
            $list = self::where($where)->column('id,name');
        }
        return $list;
    }
    
    //通过ID得到相应的标题名称
    public static function getNameById($id)
    {
        if (empty($id)) {
            return ;
        }
        $list = static::getTitleList();
        if($list){
            return $list[$id];
        }
    }
    
    //获取一个值值给某些地方没有指定MID的地方默认使用
    public static function getId()
    {
        $list = static::getTitleList();
        if($list){
            return current(array_flip($list));
        }
    }
    
    //第一项，指定ID及其子ID不要显示，比如创建栏目的时候容易造成死循环，第二项发布文章的时候，不能选择其它模型的栏目
    public static function getTreeTitle($id = 0, $mid = 0,$default_title = '请选择...')
    {
        $where = [];
        $result = [];
        if ($default_title != '') {
            $result[0] = $default_title;
        }
        
        if ($id !== 0) {
            $hide_ids    = array_merge([$id], static::getSonsId($id));
            $where['id'] = ['notin', $hide_ids];
        }
        
        if ($mid !== 0) {
            $where['mid'] = $mid;
        }
        
        $data_list = Tree::config(['title' => 'name'])->toList(self::where($where)->order('list desc,id desc')->column('id,cid,aid'));
        foreach ($data_list as $item) {
            $result[$item['id']] = $item['title_display'];
        }
        
        if ($default_title === false) {
            unset($result[0]);
        }
        
        return $result;
    }
    
    public static function getTreeList($id = 0, $mid = 0)
    {
        $where = [];
        $result = [];
        
        if ($id !== 0) {
            $hide_ids    = array_merge([$id], static::getSonsId($id));
            $where['id'] = ['notin', $hide_ids];
        }
        if ($mid !== 0) {
            $where['mid'] = $mid;
        }
        
        $data_list = Tree::config(['title' => 'name'])->toList(self::where($where)->order('list desc,id desc')->column('id,cid,aid'));
     
        return $data_list;
    }
    
    public static function getSonsId($id = 0)
    {
        $array = $id_array = self::where('pid', $id)->column('id');
        foreach ($id_array AS $id_value) {
            $array = array_merge($array, static::getSonsId($id_value));
        }
        return $array;
    }
}