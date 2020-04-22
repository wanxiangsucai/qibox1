<?php
namespace app\index\controller;

use app\common\controller\IndexBase;

class Qrcode extends IndexBase
{
    public function index($url = 'http://www.baidu.com',$logo=''){        
        $url = get_url($url);        
        if($logo=$this->get_file($logo)){
        }elseif($logo=$this->get_file($this->webdb['logo'])){
        }elseif($logo=$this->get_file($this->webdb['qrcode_logo'])){
        }elseif($logo=$this->get_file('static/index/default/logo.png')){
        }
        include_once (ROOT_PATH.'vendor/phpqrcode/phpqrcode.php');
    }
    
    private function get_file($file=''){
        $logo = '';
        if($file!=''){
            if (strstr($file,$this->request->domain())) {
                $path = str_replace($this->request->domain().'/', ROOT_PATH, $file);
                if(is_file($path)){
                    $logo = $path;
                }
            }else{
                if(preg_match("/^http/", $file)){
                    if (!is_dir(UPLOAD_PATH.'temp')) {
                        mkdir(UPLOAD_PATH.'temp');
                    }
                    $name = preg_match("/\.(jpg|jpeg|png|gif)$/i", $file)?basename($file):(md5($file).'.jpg');
                    $path = UPLOAD_PATH.'temp/'.$name;
                    if (is_file($path)) {
                        $logo = $path;
                    }elseif($string = file_get_contents($file)?:http_curl($file)){
                        $logo = $path;
                        file_put_contents(UPLOAD_PATH.'temp/'.$name, $string);                        
                    }
                }else{
                    if (is_file(ROOT_PATH.$file)) {
                        $logo = ROOT_PATH.$file;
                    }elseif(is_file(PUBLIC_PATH.$file)){
                        $logo = PUBLIC_PATH.$file;
                    }
                }                
            }
        }
        return $logo;
    }
}

