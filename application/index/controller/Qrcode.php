<?php
namespace app\index\controller;

use app\common\controller\IndexBase;

class Qrcode extends IndexBase
{
    public function index($url = 'http://www.baidu.com',$logo=''){
        
        $url = get_url($url);
        if ($logo) {
            if (strstr($logo,$this->request->domain())) {
                $logo = str_replace($this->request->domain().'/', '', $logo);
                $logo = ROOT_PATH.$logo;
                if (!is_file($logo)) {
                    $logo='';
                }
            }elseif(!strstr($logo,'http')){                
                if (is_file(UPLOAD_PATH.$logo)) {
                    $logo = UPLOAD_PATH.$logo;
                }elseif( is_file(PUBLIC_PATH.$logo) ){
                    $logo = PUBLIC_PATH.$logo;
                }else{
                    $logo = '';
                }
            }else{
                $name = md5($logo).'.jpg';
                if (!is_dir(RUNTIME_PATH.'qrcode')) {
                    mkdir(RUNTIME_PATH.'qrcode');
                }
                if(is_file(RUNTIME_PATH.'qrcode/'.$name)){
                    $logo = RUNTIME_PATH.'qrcode/'.$name;
                }elseif(($string = file_get_contents($logo))||($string = http_curl($logo))){
                    $logo = RUNTIME_PATH.'qrcode/'.$name;
                    file_put_contents($logo, $string);
                }else{
                    $logo = '';
                }
            }
        }
        if ($logo=='') {
            if ($this->webdb['qrcode_logo'] && is_file(ROOT_PATH.$this->webdb['qrcode_logo'])) {
                $logo = ROOT_PATH.$this->webdb['qrcode_logo'];
            }elseif( is_file(STATIC_PATH.'index/default/logo.png') ){
                $logo = STATIC_PATH.'index/default/logo.png';
            }
        }
        include_once (ROOT_PATH.'vendor/phpqrcode/phpqrcode.php');
    }    
}

