<?php
namespace tncode;

/**
 * APP专用行为验证码
 */
class Appcode
{
    protected $_fault = 4; //容错象素 越大体验越好，越小破解难道越高
    protected $error_num = 2; //验证图形验证码时，错误次数不能超过2次
    protected $yzm_time = 180; //设置失效时间 180=3分钟
    protected $sid = '';
    
    public function __construct($sid=''){
        $this->sid = $sid?:md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
        session_id($this->sid);//指定一个session 的id
        session_start();//开启session
    }
    
    /**
     * 刷新图片
     */
    public function refresh(){
        //【随机】裁剪的区域坐标【左上角】【并保存】
        $_SESSION['tuxing_yzm_x'] = rand(30,530);
        $_SESSION['tuxing_yzm_y'] = rand(50, 250);
        $_SESSION['tuxing_yzm_img'] = __DIR__.'/appbg/'.rand(1, 3).'.jpg';//原图要求是 1920x1080这个比例的，679x382【图片避免用白色背景的】
        $_SESSION['tuxing_yzm_moban'] = __DIR__.'/appbg/icon/'.rand(1, 4).'.png';
        $_SESSION['tuxing_yzm_opacity'] = rand(30, 80);//原图上空缺的位置的透明度【这个可以增加被破解的记录】
        $_SESSION['tuxing_yzm_time'] = time() + $this->yzm_time;
        $_SESSION['tuxing_yzm_error_cishu'] = $this->error_num;
        return $this->sid;
    }
    
    /**
     * 验证图片位置
     * @param string $offset 位置数值
     * @return string|boolean
     */
    public function check($offset=''){
        if(!(isset($_SESSION['tuxing_yzm_x']) && isset($_SESSION['tuxing_yzm_error_cishu']) && isset($_SESSION['tuxing_yzm_time']) )){
            return '请先获取图形验证码';
        }
        
        if($_SESSION['tuxing_yzm_time'] <= time()){
            return '验证码已过期，有效期为3分钟';
        }
        if(!preg_match('/^[0-9]{1,4}$/', $offset)){
            return '位置的字符类型有误';
        }
        if($offset <= $_SESSION['tuxing_yzm_x']+$this->_fault && $offset >= $_SESSION['tuxing_yzm_x']-$this->_fault){//左右两边都有4px的包容度
            return true;
        }else{
            $_SESSION['tuxing_yzm_error_cishu'] -= 1;
            if($_SESSION['tuxing_yzm_error_cishu'] == 0){
                return '验证码错误次数过多，请重新获取';
            }
            return '图形滑块验证码位置有误';
        }
    }
    
    /**
     * 获取背景图或缺口图
     * @param string $is_bgimg false为缺口图 true为背景图
     * @return string
     */
    public function img($is_bgimg=true){
        if(!( isset($_SESSION['tuxing_yzm_x']) && isset($_SESSION['tuxing_yzm_y']) && isset($_SESSION['tuxing_yzm_img']) && isset($_SESSION['tuxing_yzm_moban']) && isset($_SESSION['tuxing_yzm_opacity']) )){
            return '图形滑动验证码尚未生成';
        }
        $x = $_SESSION['tuxing_yzm_x'];//设置验证码
        $y = $_SESSION['tuxing_yzm_y'];//设置验证码
        $img = $_SESSION['tuxing_yzm_img'];
        $moban = $_SESSION['tuxing_yzm_moban'];
        $opacity = $_SESSION['tuxing_yzm_opacity'];
        //创建源图的实例
        $src = imagecreatefromstring(file_get_contents($img));
        //新建一个真彩色图像【尺寸 = 90x90】【目前是不透明的】
        $res_image = imagecreatetruecolor(90, 90);
        //创建透明背景色，主要127参数，其他可以0-255，因为任何颜色的透明都是透明
        $transparent = imagecolorallocatealpha($res_image, 255, 255, 255, 127);
        //指定颜色为透明（做了移除测试，发现没问题）
        imagecolortransparent($res_image, $transparent);
        //填充图片颜色【填充会将相同颜色值的进行替换】
        imagefill($res_image, 0, 0, $transparent);//左边的半圆
        
        //实现两个内凹槽【填补上纯黑色】
        $tempImg = imagecreatefrompng($moban);//加载模板图
        for($i=0; $i < 90; $i++){// 遍历图片的像素点
            for ($j=0; $j < 90; $j++) {
                if(imagecolorat($tempImg, $i, $j) !== 0){// 获取模板上某个点的色值【取得某像素的颜色索引值】【0 = 黑色】
                    $rgb = imagecolorat($src, $x + $i, $y + $j);// 对应原图上的点
                    imagesetpixel($res_image, $i, $j, $rgb);// 移动到新的图像资源上
                }
            }
        }
        if($is_bgimg){  //背景图
            //制作一个半透明白色蒙版
            $mengban = imagecreatetruecolor(90, 90);
            //先让蒙版变成透明的
            //指定颜色为透明（做了移除测试，发现没问题）
            imagecolortransparent($mengban, $transparent);
            //填充图片颜色【填充会将相同颜色值的进行替换】
            imagefill($mengban, 0, 0, $transparent);
            $huise = imagecolorallocatealpha($res_image, 255, 255, 255, $opacity);
            for($i=0; $i < 90; $i++){// 遍历图片的像素点
                for ($j=0; $j < 90; $j++) {
                    $rgb = imagecolorat($res_image, $i, $j); // 获取模板上某个点的色值【取得某像素的颜色索引值】
                    if($rgb !== 2147483647){// 获取模板上某个点的色值【取得某像素的颜色索引值】【0 = 黑色】
                        imagesetpixel($mengban, $i, $j, $huise);// 对应点上画上黑色
                    }
                }
            }
            //把修改后的图片，放回原本的位置
            imagecopyresampled(
                $src,//裁剪后的存放图片资源
                $res_image,//裁剪的原图资源
                $x, $y,//存放的图片，开始存放的位置
                0,0,//开始裁剪原图的位置
                90, 90,//存放的原图宽高
                90, 90//裁剪的原图宽高
                );
            //把蒙版添加到原图上去
            imagecopyresampled(
                $src,//裁剪后的存放图片资源
                $mengban,//裁剪的原图资源
                $x+1, $y+1,//存放的图片，开始存放的位置
                0,0,//开始裁剪原图的位置
                90-2, 90-2,//存放的原图宽高
                90, 90//裁剪的原图宽高
                );
            header('Content-Type: image/jpeg');
            imagejpeg($src);//浏览器 输出图片
            
        }else{ //缺口图
            
            //补上白色边框
            $tempImg = imagecreatefrompng($moban.'.png');//加载模板图
            $white = imagecolorallocatealpha($res_image, 255, 255, 255, 1);
            for($i=0; $i < 90; $i++){// 遍历图片的像素点
                for ($j=0; $j < 90; $j++) {
                    if(imagecolorat($tempImg, $i, $j) === 0){// 获取模板上某个点的色值【取得某像素的颜色索引值】【0 = 黑色】
                        imagesetpixel($res_image, $i, $j, $white);// 对应点上画上黑色
                    }
                }
            }
            //创建一个90x382宽高 且 透明的图片
            $res_image2 = imagecreatetruecolor(90, 382);
            //指定颜色为透明（做了移除测试，发现没问题）
            imagecolortransparent($res_image2, $transparent);
            //填充图片颜色【填充会将相同颜色值的进行替换】
            imagefill($res_image2, 0, 0, $transparent);//左边的半圆
            //把裁剪的图片，移到新图片上
            imagecopyresampled(
                $res_image2,//裁剪后的存放图片资源
                $res_image,//裁剪的原图资源
                0, $y,//存放的图片，开始存放的位置
                0, 0,//开始裁剪原图的位置
                90, 90,//存放的原图宽高
                90, 90//裁剪的原图宽高
                );
            header('Content-Type: image/png');
            imagepng($res_image2);//浏览器 输出图片
        }
    }
}
