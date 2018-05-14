<?php
namespace app\common\model;
use think\Db;
use think\Model;

abstract class F extends Model
{
    // 设置当前模型对应的完整数据表名称
    public $table; // '__FORM_FIELD__';
    
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
        $this->table = self::$table_pre.self::$model_key.'_field';
    }
    
    public static function getFields($map=[])
    {
        return self::where($map)->order('list desc,id asc')->column(true);
    }
    
    
    /**
     * 创建字段
     */
    public function newField($mid,$data = [])
    {
        if (empty($mid) || empty($data)) {
            $this->error = '缺少参数';
            return false;
        }
        
        $table = self::$base_table.$mid;
        
        if (is_table($table)) {
            $sql = "
            ALTER TABLE `" . self::$table_pre  . $table . "`
            ADD COLUMN `{$data['name']}` {$data['field_type']} COMMENT '{$data['title']}';
            ";
        }
        try {
            Db::execute($sql);
        } catch(\Exception $e) {
            $this->error = '字段添加失败';
            return false;
        }
        
        return true;
    }
    
    /**
     * 更新字段
     * @param null $field 字段数据
     * @return bool
     */
    public function updateField($id,$array = [])
    {
        
        if (empty($array)) {
            return false;
        }
        
        // 获取原字段名
        $field_array = self::get($id);  //;where('id', $id)->value('name');
        $table = self::$base_table.$field_array['mid'];
        
        if($array['field_type']==$field_array['field_type'] && $array['name']==$field_array['name'] ){
            return true;
        }
        if (is_table($table)) {

            $sql = "
            ALTER TABLE `" . self::$table_pre . $table."`
            CHANGE COLUMN `{$field_array['name']}` `{$array['name']}` {$array['field_type']} COMMENT '{$array['title']}';
            ";
            try {
                Db::execute($sql);
            } catch(\Exception $e) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 删除字段
     * @param null $field 字段数据
     * @return bool
     */
    public function deleteField($field = null)
    {
        
        if ($field === null) {
            return false;
        }
        if(empty($field['mid']) || empty($field['name'])){
            return false;
        }
        
        $table = self::$base_table.$field['mid'];
        
        if (is_table($table)) {
            $sql ="
            ALTER TABLE `" . self::$table_pre . $table ."`
            DROP COLUMN `{$field['name']}`;
            ";
            try {
                Db::execute($sql);
            } catch(\Exception $e) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}