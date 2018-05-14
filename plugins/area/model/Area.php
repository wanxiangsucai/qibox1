<?php
namespace plugins\area\model;
use think\Model;


//城市地区
class Area extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__AREA__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    public static function getTitleList($where=[])
    {
        return self::where($where)->order('list','desc')->column('id,name');
    }
	
}