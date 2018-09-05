<?php
namespace app\common\fun;


class Wxapp{
    /**
     * 圈子微信二维码小程序入口
     * @param int $id
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