<?php

namespace app\common\model;
use think\Model;


//短网址
class Shorturl extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__SHORTURL__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	protected $pk = 'id';
	
	/**
	 * 根据网址获取对应的ID值
	 * @param string $url 网址
	 * @param number $type 0是URL跳转,1是公众号二维码,2是小程序码
	 * @param number $uid 生成用户的帐号
	 * @return unknown
	 */
	public static function getId($url='',$type=0,$uid=0,$time=0){
	    $map = [
	        'url'=>strlen($url)>255 ? md5($url) : $url,
	        'type'=>$type,
	        'wxapp_id'=>get_wxappAppid()?:'',
	    ];
	    $id = self::where($map)->value('id');
	    if (empty($id)) {
	        $map['uid'] = intval($uid);
	        $map['expire_time'] = $time ? $time+time() : 0;
	        if(strlen($url)>255){
	            $map['long_url'] = $url;
	        }
	        $reslut = self::create($map);
	        $id = $reslut->id;
	    }
	    self::where('expire_time','>',0)->where('expire_time','<',time())->delete();
	    return $id;
	}
	
	/**
	 * 根据ID获得相应的URL
	 * @param number $id
	 * @return unknown
	 */
	public static function getUrl($id=0){
	    $info = self::where('id',$id)->find();
	    if(!$info){
	        return '';
	    }
	    if(strlen($info['url'])==32 && preg_match("/^([a-z0-9]{32})$/", $info['url'])){
	        return $info['long_url'];
	    }else{
	        return $info['url'];
	    }	    
	}
}