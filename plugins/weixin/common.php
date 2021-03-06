<?php


if (!function_exists('get_site_key')) {
    /**
     * 获取相应域名的公钥
     * @param string $url
     * @return mixed
     */
    function get_site_key($url=''){
        $code = config('webdb.wxmp_share_site');
        if ($code) {
            $array = json_decode($code,true);
            foreach ($array AS $rs){
                if ($rs['title1'] && preg_match("/^".str_replace('/', '\/',$rs['title1'])."/i", $url)) {
                    return $rs['title2'];
                }
            }            
        }
    }
}

 