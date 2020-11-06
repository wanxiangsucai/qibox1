<?php
namespace app\common\fun;

//播放器
class Player{
    
    /**
     * 播放器总入口
     * @param string $url
     * @param number $width
     * @param number $height
     * @param string $bgpic 背景图
     * @param string $autoplay 是否自动播放 false true
     * @param string $video_type 视频格式 auto 自动识别 mp4 m3m8 flv
     * @param string $payertype 哪种播放器
     * @return string
     */
    public function play($url='',$width=600,$height=400,$bgpic='',$autoplay=false,$video_type='auto',$payertype=''){
        $width || $width=600;
        $height || $height=400;
        if(IN_WAP===true && is_numeric($width) && $width>=400){
            $width = '100%';
            $height = '250';
        }
        if(is_numeric($width)){
            $width .= 'px';
        }
        if(is_numeric($height)){
            $height .= 'px';
        }
        $_url = $this->iframe_player($url,$width,$height);
        if ($_url!='') {
            return $_url;
        }
        
        if (!preg_match('/^(http|\/public)/i', $url)) {
            $url = tempdir($url);
        }
        
        if (strstr($url,'.swf') || $payertype=='swf' || $payertype=='flash') {
            return $this->swfpay($url,$width,$height);
        }elseif( $payertype=='aliplayer' ){
            return $this->aliplayer($url,$width,$height,$bgpic,$autoplay);
        }elseif( 
            ( ($video_type==='flv' || strstr($url,'.flv')) && !in_wap() && $payertype!=='dplayer') //PC中如果没有强制指定dplayer的话,FLV用CKplayer兼容性更好
            || ($video_type!=='m3u8' && !strstr($url,'.m3u8') && $payertype=='ckplayer') //ckplayer不能播放m3u8
        ){
            return $this->ckplayer($url,$width,$height,$bgpic,$autoplay); 
        }
        return $this->dplayer($url,$width,$height,$bgpic,$autoplay,$video_type);
    }
    
    /**
     * 站外视频只能用框架处理
     * @param unknown $url
     * @param unknown $width
     * @param unknown $height
     * @return void|string
     */
    public static function iframe_player($url,$width,$height){
        if (!preg_match('/^(http)/i', $url)) {
            return ;
        }elseif( preg_match('/^(http|https):\/\/([\w\.-]+)(\.qq\.com|\.youku\.com)\//i', $url) ){
            if (!preg_match('/player\./i', $url)) {
                $url = self::get_iframe_url($url);
            }
            static $array_id = 0;
            $array_id++;
            $url = str_replace('.','x@01x@01',urlencode($url));
            return "<iframe class='play_iframe player_{$array_id}' src='about:blank' height='$height' width='$width' frameborder='0' allowfullscreen></iframe>
                    <script type='text/javascript'>
                    $('.player_{$array_id}').attr('src', decodeURIComponent('{$url}'.replace(/x@01x@01/g,'.')).replace(/\+/g,' ') );
                    </script>";
        }
    }
    
    /**
     * 把普通网址转为播放器的框架网址
     * @param unknown $url
     * @return mixed
     */
    public static function get_iframe_url($url){
        $array_a = [
            "/v\.youku\.com\/v_show\/id_([\w=]+)\.html\?([^\?]+)/",
            "/v\.qq\.com\/([\w]+)\/([\w]+)\/([\w]+)\.html/",
            "/v\.qq\.com\/([\w]+)\/([\w]+)\/([\w]+)\/([\w]+)\.html/",
        ];
        $array_b = [
            "player.youku.com/embed/\\1",
            "v.qq.com/txp/iframe/player.html?vid=\\3",
            "v.qq.com/txp/iframe/player.html?vid=\\4",
        ];
        $url = preg_replace($array_a, $array_b, $url);
        return $url;
    }
    
    /**
     * CK播放器
     * @param string $url
     * @param string $width
     * @param string $height
     * @return string
     */
    private function ckplayer($url='',$width='',$height='',$bgpic='',$autoplay=false){
        static $array_id = 0;
        $array_id++;
        $js = '';
        if($array_id==1){
            $js = '<script type="text/javascript">if(typeof(ckplayer)=="undefined"){document.write(\'<script type="text/javascript" src="'.config('view_replace_str.__STATIC__').'/libs/ckplayer/ckplayer.js"><\/script>\');}</script>';
        }
        if(!$bgpic){
            $bgpic = config('webdb.video_player_bgpic');
        }
        if ($bgpic!='') {
            $bgpic=tempdir($bgpic);
        }
        $url = str_replace('.','x@01x@01',urlencode($url));
        return "{$js}<center><div class='video{$array_id} video-player' style='width: {$width};height: {$height};'></div></center>
                <script type='text/javascript'>
                	var videoObject = {
                		container: '.video{$array_id}', //“#”代表容器的ID，“.”或“”代表容器的class
                		variable: 'player{$array_id}',  //该属性必需设置，值等于下面的new chplayer()的对象
                		poster:'{$bgpic}',//封面图片
                        loaded: 'loadedHandler{$array_id}', //当播放器加载后执行的函数	
                		video:decodeURIComponent('{$url}'.replace(/x@01x@01/g,'.')).replace(/\+/g,' ')   //视频地址
                        
                	};
                	var player{$array_id} = new ckplayer(videoObject);
                	".($autoplay?"$(function(){player{$array_id}.videoPlay();});":""). "
                </script>";
    }
    
    /**
     * DPlayer播放器
     * @param string $url
     * @param string $width
     * @param string $height
     * @param string $bgpic 背景图
     * @param string $autoplay 是否自动播放
     * @param string $video_type 是否自动获取视频格式,还是特别指定格式
     * @return string
     */
    private function dplayer($url='',$width='',$height='',$bgpic='',$autoplay=false,$video_type=true){
        static $array_id = 0;
        $array_id++;
        $js = '';
        if(!$bgpic){
            $bgpic = config('webdb.video_player_bgpic');
        }
        if ($bgpic!='') {
            $bgpic=tempdir($bgpic);
        }
        if($video_type===true||$video_type==='auto'){
            if (strstr($url,'.m3u8')) {
                $video_type='hls';
            }elseif(strstr($url,'.flv')){
                $video_type='flv';
            }else{
                $video_type='mp4';
            }
        }        
        $autoplay = $autoplay?'true':'false';
        if($array_id==1){
            $js = '<script type="text/javascript">if(typeof(DPlayer)=="undefined"){document.write(\'<script type="text/javascript" src="'.config('view_replace_str.__STATIC__').'/libs/bui/pages/zhibo/dplayer/flv.min.js"><\/script><script type="text/javascript" src="'.config('view_replace_str.__STATIC__').'/libs/bui/pages/zhibo/dplayer/hls.min.js"><\/script><script type="text/javascript" src="'.config('view_replace_str.__STATIC__').'/libs/bui/pages/zhibo/dplayer/DPlayer.min.js?v=f32"><\/script><link rel="stylesheet" href="'.config('view_replace_str.__STATIC__').'/libs/bui/pages/zhibo/dplayer/DPlayer.min.css">\');}</script>';
        }
        $url = str_replace('.','x@01x@01',urlencode($url));
        return "{$js}<center><div class='video{$array_id} video-player' style='width: {$width};height: {$height};'><div id='d_player{$array_id}' style='width:100%;height:100%;'></div></div></center>
        <script type='text/javascript'>
            var Dplayer{$array_id} = new DPlayer({
                    container: document.getElementById('d_player{$array_id}'),
                    live: false,
                    volume: 1,
                    autoplay: {$autoplay},
                    video: {
                        url: decodeURIComponent('{$url}'.replace(/x@01x@01/g,'.')).replace(/\+/g,' '),
                        type: '{$video_type}',
                        pic: '{$bgpic}',
                    },
           });
    </script>";
    }
    
    /**
     * 阿里云播放器
     * @param string $url
     * @param string $width
     * @param string $height
     * @return string
     */
    private function aliplayer($url='',$width='',$height='',$bgpic='',$autoplay=false){
        static $array_id = 0;
        $array_id++;
        if(!$bgpic){
            $bgpic = config('webdb.video_player_bgpic');
        }
        if ($bgpic!='') {
            $bgpic=tempdir($bgpic);
        }
        $autoplay = $autoplay?'true':'false';
        if($array_id==1){
            $js = '<script type="text/javascript">if(typeof(Aliplayer)=="undefined"){document.write(\'<link rel="stylesheet" href="https://g.alicdn.com/de/prismplayer/2.9.1/skins/default/aliplayer-min.css" /><script type="text/javascript" charset="utf-8" src="https://g.alicdn.com/de/prismplayer/2.9.1/aliplayer-min.js"><\/script>\');}</script>';
        }
        return <<<EOT
$js
<center><div class="prism-player" id="player-con{$array_id}"></div></center>
<script type="text/javascript">
var ali_player{$array_id} = new Aliplayer({
		"id": "player-con{$array_id}",
		"source": "$url",
		"width": "$width",
		"height": "$height",
		"autoplay": $autoplay,
        "cover": "$bgpic",
		"isLive": false,	//直播与点播的开关
		"rePlay": false,
		"playsinline": true,
		"preload": true,
		"controlBarVisibility": "hover",
		"useH5Prism": true,
		//"x5_type":"h5",
		//"useH5Prism":true,
		//"playsinline":true,
	}, function (player) {
				console.log("The player is created");
	}
);
</script>
EOT;
    }
    
    /**
     * FLASH需要点击激活，所以要使用原始播放器
     * @param string $url
     * @param string $width
     * @param string $height
     * @return string
     */
    private function swfpay($url='',$width='',$height=''){
        return "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' width='{$width}' height='{$height}'>
<param name=movie value='{$url}' ref>
<param name=quality value=High>
<param name='_cx' value='12383'>
<param name='_cy' value='1588'>
<param name='FlashVars' value>
<param name='Src' ref value='{$url}'>
<param name='WMode' value='Window'>
<param name='Play' value='-1'>
<param name='Loop' value='-1'>
<param name='SAlign' value>
<param name='Menu' value='-1'>
<param name='Base' value>
<param name='AllowScriptAccess' value='always'>
<param name='Scale' value='ShowAll'>
<param name='DeviceFont' value='0'>
<param name='EmbedMovie' value='0'>
<param name='BGColor' value>
<param name='SWRemote' value>
<param name='MovieData' value>
<embed src='{$url}' quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' width='{$width}' height='{$height}'>
</embed>
</object>";
    }
     
}