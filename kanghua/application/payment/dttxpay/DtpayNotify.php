<?php
namespace app\payment\dttxpay;
use think\Config;

/**
 *
 * User: lirong
 * Date: 2017/7/11
 * Time: 15:20
 */
class DtpayNotify{
    protected $config = NULL;

    protected $mode = NULL;

    function __construct($mode = 'Single')
    {
        $this->mode = $mode;
        $this->config = Config::get('dttx_pay_config');
    }

    function dtpaySubmit($mode = 'Single')
    {
        $this->mode = $mode;
        $this->config = Config::get('dttx_pay_config');
    }

    //验签
    public function verifySign($data)
    {
        //获取公钥
        $publicKey = $this->config[ 'public_key_path' ];

        //对待验签参数数组排序
        $filtedData = paraFilter( $data );
        $filtedData = argSort( $filtedData );
        //将数组拼成字符串
        $string = createLinkstring( $filtedData );

        return rsaVerify( $string, $publicKey, urldecode( $data[ 'sign' ] ) );
    }

















}