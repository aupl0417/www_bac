<?php
/**
 * 脚本参数配置文件
 * Author: lirong
 * Date: 2017/7/27
 * Time: 10:58
 */
date_default_timezone_set('PRC');
$domain ='https://ys.dttx.com';
$jobQuene_path =str_replace('\\','/',dirname(__FILE__));

//自动收货接口
$goodsOrderUrl =$domain.'/payment/online/orderautoreceive';
$closeTimeOutOrderUrl =$domain.'/payment/online/closeTimeOutOrder';
