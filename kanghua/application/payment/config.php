<?php
//配置文件
return [
    'default_ajax_return'    => 'html',


    // 在线支付参数

   'dttx_pay_config'=>[
       //渠道号，16位
       'channelID'        => '0000000000000000',
       //商户的私钥
       'private_key_path' => APP_PATH.'payment/attachment/key/rsa_private_key.pem',
       //大唐支付的公钥
       'public_key_path'  => APP_PATH.'payment/attachment/key/rsa_public_key.pem',
       //签名方式：RSA/MD5/DES
       'sign_type'        => 'RSA',
       //MD5签名方式的盐值
       'salt'             => 'QE151SD1A1Q5W1E6565QE511A13A5W1A',
       //字符编码格式 目前支持 gbk 或 utf-8
       'input_charset'    => strtolower( 'utf-8' ),
       //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
       'transport'        => 'http',
       // 支付类型：pc/web/wap/app
       'payment_from'     => 'pc',

       'Single' => array(
           //单订单 提交地址
           'submit_gateway' =>\think\Config::get('dttxapi.orderSubmitGateway'),
           // 单订单 异步通知 路径。需外网可访问
           'notify_url'     => url('payment/online/notifyurl','','',true),
           // 单订单 同步返回 路径
           'return_url'     => url('payment/online/returnurl','','',true),
       ),
       'Vip' => array(
           //单订单 提交地址
           'submit_gateway' =>\think\Config::get('dttxapi.orderSubmitGateway'),
           // 单订单 异步通知 路径。需外网可访问
           'notify_url'     => url('payment/online/vipNotifyurl','','',true),
           // 单订单 同步返回 路径
           'return_url'     => url('payment/online/returnurl','','',true),
       ),
       'Multi'  => array(
           //组合订单 提交地址
           'submit_gateway' => 'http://cashier.datangbox.com/submit/Multiple',
           //组合订单 异步通知地址
           'notify_url'     => '',
           //组合订单同步返回地址
           'return_url'     => '',
       )
   ]


];