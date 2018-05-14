<?php
namespace app\common\util;

class Sms{
    
    /**
     * 更换其它短信接口的话,请修改下面的方法,而不建议直接修改/application/common.php里的函数send_sms(),因为common.php这个文件会经常升级
     * @param string $phone 手机号码
     * @param string $msg 短信内容
     * @return boolean|string
     */
    public function send($phone='',$msg=''){
        // 如果$msg长度大于6位的话,判断是营销信息的话,可以在这里更换其它接口
        return $this->aliyun($phone,$msg);
    }
    
    /**
     * 阿里云短信接口
     * @param string $phone
     * @param string $msg
     * @return string|boolean
     */
    private function aliyun($phone='',$msg=''){
        if(!class_exists("\\plugins\\smsali\\Api")){
            return '短信接口不存在';
        }
        $obj = new \plugins\smsali\Api(config('webdb.sms_access_id'),config('webdb.sms_access_key'));
        $signName = config('webdb.sms_sign_name');        //签名,比如齐博
        $templateCode = config('webdb.sms_template');     //使用的模板,比如SMS_16830430
        $phoneNumbers = $phone;
        $templateParam = ['code'=>$msg,'name'=>$msg];
        $result = $obj->sendSms($signName, $templateCode, $phoneNumbers, $templateParam);
        if($result['Code']=='OK'){
            return true;
        }else{
            return $result['Message'] . ' ' . $result['Code'];
        }
    }
    
    //其它短信接口
    private function other_sms(){        
    }
	
}