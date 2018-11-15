<?php
namespace app\common\fun;


class Wxapp{
    
    public static function mp_code($id=0){
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
}