<?php

namespace plugins\weixin\model;
use think\Model;


//微信订阅/模板消息模板
class WeixinNotice extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__WEIXINNOTICE__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	//protected $pk = 'id';


	
}