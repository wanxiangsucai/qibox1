<?php

//ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once dirname(__FILE__).'/lib/WxPay.Api.php';
require_once dirname(__FILE__).'/log.php';


/*


//初始化日志
$logHandler= new CLogFileHandler(ROOT_PATH.date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#f00;'>$key</font> : ".htmlspecialchars($value, ENT_QUOTES)." <br/>";
    }
}


try{
    $input = new WxPayRefund();
    $input->SetOut_trade_no('r220407183151314');
    $input->SetTotal_fee('2');
    $input->SetRefund_fee('2');
    
    $input->SetOut_refund_no("sdkphp".date("YmdHis"));
    $input->SetOp_user_id( config('webdb.weixin_payid') );
    print_r(WxPayApi::refund($input));exit;
    printf_info();
}catch(Exception $e){
    //Log::ERROR(json_encode($e));
    echo 'Message: ' .$e->getMessage();die('fff');
} 

*/