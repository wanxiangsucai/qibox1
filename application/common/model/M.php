<?php
namespace app\common\model;

use think\Model;
use think\Db;

abstract class M extends Model
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
        $this->table = self::$table_pre.self::$model_key.'_module';
    }
    
    public static function getTitleList($map = [])
    {
        static $list = NULL;
        if($list==NULL){
            $list = self::where($map)->order('list','desc')->column('id,title');
        }
        return $list;
    }
    
    public static function getList($map = [])
    {
        static $list = NULL;
        if($list==NULL){
            $list = self::where($map)->order('list desc,id asc')->column(true);
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
    
    //获取一个值，给某些地方没有指定MID的地方默认使用
    public static function getId()
    {
        $list = static::getTitleList();
        if($list){
            return current(array_flip($list));
        }        
    }
    
    //删除模型
    public static function deleteModule($mid = null)
    {
        if (empty($mid)) {
            return false;
        }
        
        //先删除字段表的对应记录
        Db::name(self::$model_key.'_field')->where(['mid' => $mid])->delete();
        
        //删除主表对应的数据
        Db::name(self::$base_table)->where('mid','=',$mid)->delete();
        
        Db::name(self::$model_key.'_sort')->where('mid','=',$mid)->delete();
        
        //删除分表
        return false !== Db::execute('DROP TABLE IF EXISTS `' . self::$table_pre . self::$base_table . $mid . '`');
    }
    
    //创建模型
    public static function createTable($id,$name)
    {
        $table = self::$base_table.$id;
        if (!is_table($table)) {
            // 新建独立扩展表
            $sql = "
            CREATE TABLE IF NOT EXISTS `" . self::$table_pre . $table . "` (
			  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
			  `mid` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
			  `fid` mediumint(6) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
			  `title` varchar(256) NOT NULL DEFAULT '' COMMENT '标题',
			  `ispic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否带组图',
			  `uid` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
			  `view` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
			  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态：0未审 1已审 2推荐',
			  `replynum` mediumint(5) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
			  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
			  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
			  `list` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
			  `picurl` text NOT NULL COMMENT '封面图',
			  `content` text NOT NULL COMMENT '文章内容',
			  `province_id` mediumint(5) NOT NULL COMMENT '省会ID',
			  `city_id` mediumint(5) NOT NULL COMMENT '城市ID',
			  `zone_id` mediumint(5) NOT NULL COMMENT '县级市或所在区ID',
			  `street_id` mediumint(5) NOT NULL COMMENT '乡镇或区域街道ID',
			  `ext_sys` smallint(5) NOT NULL COMMENT '扩展字段,关联的系统',
			  `ext_id` int(7) NOT NULL COMMENT '扩展字段,关联的ID',
			  PRIMARY KEY (`id`),
			  KEY `mid` (`mid`),
			  KEY `fid` (`fid`),
			  KEY `view` (`view`),
			  KEY `status` (`status`),
			  KEY `list` (`list`),
			  KEY `ispic` (`ispic`),
			  KEY `province_id` (`province_id`),
			  KEY `city_id` (`city_id`),
			  KEY `ext_id` (`ext_id`,`ext_sys`)
            
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='{$name}模型表' AUTO_INCREMENT=1 ;
            ";
        }
        
        try {
            Db::execute($sql);
        } catch(\Exception $e) {
            return false;
        }
        
        // 添加默认字段
        $data = [
                [
                        'name'        => 'title',
                        'title'       => '标题',
                        'field_type'      => 'varchar(256) NOT NULL',
                        'type'        => 'text',
                        'mid'        => $id,
                        'listshow'        => '1',
                        'ifsearch'        => '1',
                        'ifmust'        => '1',
                        'list'        => '100',
                ],
                [
                        'name'        => 'picurl',
                        'title'       => '组图',
                        'field_type'      => 'text NOT NULL',
                        'type'        => 'images',
                        'mid'        => $id,
                        'listshow'        => '0',
                        'ifsearch'        => '0',
                        'ifmust'        => '0',
                        'list'        => '99',
                ],
                [
                        'name'        => 'content',
                        'title'       => '内容介绍',
                        'field_type'      => 'text NOT NULL',
                        'type'        => 'ueditor',
                        'mid'        => $id,
                        'listshow'        => '0',
                        'ifsearch'        => '0',
                        'ifmust'        => '0',
                        'list'        => '-1',
                ],
        ];
        
        Db::name(self::$model_key.'_field')->insertAll($data);
        
        return true;
    }
}