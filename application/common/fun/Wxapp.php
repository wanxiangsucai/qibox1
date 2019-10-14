<?php
namespace app\common\fun;

use app\common\model\Shorturl AS ShorturlModel;

class Wxapp{
    
    public static function mp_code($id=0){
        if( config('webdb.weixin_appid')=='' || config('webdb.weixin_appsecret')==''){
            return '系统没有配置公众号';
        }
        $path = config('upload_path') . '/mp_code/';
        $img_path  = $path.$id.'.png';
        if (is_file($img_path)) {
            return tempdir("uploads/mp_code/{$id}.png");
        }
        if (!is_dir($path)) {
            mkdir($path);
        }
        $access_token = wx_getAccessToken(true,false);
        if (empty($access_token)) {
            return 'access_token不存在!';
        }
        
        $str = http_curl('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token,[
                'action_name'=>'QR_LIMIT_STR_SCENE',
                'action_info'=>[
                       'scene'=>[
                               'scene_str'=>$id,
                       ] 
                ],
        ],'json');
        $res = json_decode($str,true);
        $tick = $res['ticket'];
        if (empty($access_token)) {
            return 'ticket不存在!';
        }
        $code = http_Curl("https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$tick");        
        if (strlen($code)>500) {
            write_file($img_path, $code);
            return tempdir("uploads/mp_code/{$id}.png");
        }else{
            return $code;
        }
    }
    
    
    /**
     * 圈子微信二维码小程序入口
     * @param int $id
     * $id 取值 最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * 加前缀处理的方法是 qun/Error.php/_initialize 务必用_下画线做分隔符,比如 bbs_123
     */
    public static function qun_code($id=0){
        if( config('webdb.wxapp_appid')=='' || config('webdb.wxapp_appsecret')==''){
            return 'http://x1.php168.com/public/static/qibo/nowxapp.jpg';
        }
        $path = config('upload_path') . '/qun_code/';
        $img_path  = $path.$id.'.png';
        if (is_file($img_path)) {
            return tempdir("uploads/qun_code/{$id}.png");
        }
        if (!is_dir($path)) {
            mkdir($path);
        }
        $access_token = wx_getAccessToken(true,true);
        if (empty($access_token)) {
            return 'access_token不存在!';
        }
        $code = http_curl('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token,[
                'scene'=>$id,
                'page'=>'pages/hy/web/index',
                'width'=>'430',
        ],'json');
        if (strlen($code)>500) {
            write_file($img_path, $code);
            return tempdir("uploads/qun_code/{$id}.png");
        }else{
            return $code;
        }
    }
    
    
    /**
     * 通用小程序二维码入口
     * @param string $url 要生成小程序二维码的URL网址
     * @param int $uid 当前用户UID
     * 小程序的关键字 取值 最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * 加前缀处理的方法是 qun/Error.php/_initialize 务必用_下画线做分隔符,比如 bbs_123
     */
    public static function wxapp_codeimg($url='',$uid=0){
        if( config('webdb.wxapp_appid')=='' || config('webdb.wxapp_appsecret')==''){
            if (!is_file(PUBLIC_PATH."static/images/nowxapp.jpg")&&is_writable(PUBLIC_PATH."static/images/")) {
                copy('http://x1.php168.com/public/static/qibo/nowxapp.jpg',PUBLIC_PATH."static/images/nowxapp.jpg");                
            }
            if(is_file(PUBLIC_PATH."static/images/nowxapp.jpg")){
                return PUBLIC_URL.'/static/qibo/nowxapp.jpg';
            }else{
                return 'http://x1.php168.com/public/static/qibo/nowxapp.jpg';
            }            
        }
        if ($uid===0) {
            $uid = login_user('uid');
        }
        $url = get_url($url);   //补全http
        $id = ShorturlModel::getId($url,2,$uid);
        $path = config('upload_path') . '/wxapp_codeimg/';
        $img_path  = $path.$id.'.png';
        if (is_file($img_path)) {
            return tempdir("uploads/wxapp_codeimg/{$id}.png");
        }
        if (!is_dir($path)) {
            mkdir($path);
        }
        $access_token = wx_getAccessToken(true,true);
        if (empty($access_token)) {
            return 'access_token不存在!';
        }
        $code = http_curl('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token,[
            'scene'=>$id,
            'page'=>'pages/wap/iframe/index',
            'width'=>'430',
        ],'json');
        if (strlen($code)>500) {
            write_file($img_path, $code);
            return tempdir("uploads/wxapp_codeimg/{$id}.png");
        }else{
            return $code;
        }
    }
    
}