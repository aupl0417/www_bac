<?php
/**
 *  订单超时自动关闭脚本
 * Author: lirong
 * Date: 2017/7/27
 * Time: 10:54
 */
define('ROOT',str_replace('\\','/',dirname(__FILE__)));

require_once ROOT.'/config.php';
require_once $jobQuene_path.'/function.php';
$day =date('Ymd');

echo "start...";
while (true){
    $res =curl_get($closeTimeOutOrderUrl);
    $result =json_decode($res,true);

    if (!empty($result)){
        if ($result['statusCode']==300){
            file_put_contents($jobQuene_path.'/log_closeTimeOutOrder_'.$day.'.txt',$res.PHP_EOL,FILE_APPEND);
            sleep(5);
        }
        if ($result['statusCode']==301){
            file_put_contents($jobQuene_path.'/log_closeTimeOutOrderError_'.$day.'.txt',$res.PHP_EOL,FILE_APPEND);
            sleep(5);
        }
    }

}
