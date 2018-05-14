<?php

class JSSDK {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // ע�� URL һ��Ҫ��̬��ȡ������ hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    //$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
		$PHP_SELF_TEMP=$_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
		$_SERVER['QUERY_STRING'] && $PHP_SELF_TEMP .= "?".$_SERVER['QUERY_STRING'];
		$PHP_SELF=$_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:$PHP_SELF_TEMP;

    $url = "$protocol$_SERVER[HTTP_HOST]$PHP_SELF";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // ���������˳��Ҫ���� key ֵ ASCII ����������
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
	  global $webdb;
	$mymd5 = $webdb['WXFmymd5'] ? $webdb['WXFmymd5'] : $webdb[mymd5] ;
	$path = $webdb['WXFweb_dir'] ? $webdb['WXFweb_dir'] : $webdb[web_dir] ;
    // jsapi_ticket Ӧ��ȫ�ִ洢����£����´�����д�뵽�ļ�����ʾ��
    $data = json_decode(file_get_contents(ROOT_PATH."cache{$path}/jsapi_ticket_{$mymd5}.json"));
    if ($data->expire_time < time()) {
      $accessToken = $this->getAccessToken();
      // �������ҵ�������� URL ��ȡ ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 3600;
        $data->jsapi_ticket = $ticket;
        $fp = fopen(ROOT_PATH."cache{$path}/jsapi_ticket_{$mymd5}.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

  private function getAccessToken() {
    // access_token Ӧ��ȫ�ִ洢����£����´�����д�뵽�ļ�����ʾ��
	global $webdb;
	$mymd5 = $webdb['WXFmymd5'] ? $webdb['WXFmymd5'] : $webdb[mymd5] ;
	$path = $webdb['WXFweb_dir'] ? $webdb['WXFweb_dir'] : $webdb[web_dir] ;
    $data = json_decode(file_get_contents(ROOT_PATH."cache{$path}/access_token{$mymd5}.json"));
    if ($data->expire_time < time()) {
      // �������ҵ��������URL��ȡaccess_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 3600;
        $data->access_token = $access_token;
        $fp = fopen(ROOT_PATH."cache{$path}/access_token{$mymd5}.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }
}

