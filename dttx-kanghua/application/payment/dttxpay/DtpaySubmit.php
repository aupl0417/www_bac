<?php
namespace app\payment\dttxpay;
use think\Config;

/**
 *
 * User: lirong
 * Date: 2017/7/11
 * Time: 15:13
 */
class DtpaySubmit{

    protected $config = NULL;

    protected $mode = NULL;

    function __construct($mode = 'Single')
    {
        //    echo dirname(__File__);

        $this->mode = $mode;
        $this->config = Config::get('dttx_pay_config');
    }

    function dtpaySubmit($mode = 'Single')
    {
        $this->mode = $mode;
        $this->config = Config::get('dttx_pay_config');
    }

    /**
     * 生成签名结果
     *
     * @param $para_sort array 已排序要签名的数组
     *
     * @return string 签名结果字符串
     */
    function buildRequestMysign($para_sort)
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = createLinkstring( $para_sort );

        $mysign = NULL;
        switch(strtoupper( trim( $this->config[ 'sign_type' ] ) ))
        {
            case "RSA" :
                $mysign = rsaSign( $prestr, $this->config[ 'private_key_path' ] );
                break;
            default :
                $mysign = "";
        }

        return $mysign;
    }

    /**
     * 生成要请求给大唐支付的参数数组
     *
     * @param $para_temp array 请求前的参数数组
     *
     * @return array 要请求的参数数组
     */
    function buildRequestPara($para_temp)
    {
        //除去待签名参数数组中的空值和签名参数
        isset($para_temp[ 'notifyUrl' ]) ? $para_temp[ 'notifyUrl' ]:$para_temp[ 'notifyUrl' ] = $this->config[ $this->mode ][ 'notify_url' ];//异步通知地址
        isset($para_temp[ 'returnUrl' ]) ? $para_temp[ 'returnUrl' ]:$para_temp[ 'returnUrl' ] = $this->config[ $this->mode ][ 'return_url' ];//同步通知地址
        $para_temp[ 'timestamp' ] = time();//商户服务器当前时间戳
        $para_filter = paraFilter( $para_temp );

        //对待签名参数数组排序
        $para_sort = argSort( $para_filter );

        //生成签名结果
        $mysign = $this->buildRequestMysign( $para_sort );

        //签名结果与签名方式加入请求提交参数组中
        $para_sort[ 'sign' ] = urlencode( $mysign );
        $para_sort[ 'sign_type' ] = strtoupper( trim( $this->config[ 'sign_type' ] ) );

        return $para_sort;
    }

    /**
     * 生成要请求给大唐支付的参数数组
     *
     * @param $para_temp array 请求前的参数数组
     *
     * @return string 要请求的参数数组字符串
     */
    function buildRequestParaToString($para_temp)
    {
        //待请求参数数组
        $para = $this->buildRequestPara( $para_temp );

        //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
        $request_data = createLinkstringUrlencode( $para );

        return $request_data;
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     *
     * @param $para_temp   array 请求参数数组
     *
     * @return string 提交表单HTML文本
     */
    function buildRequestForm($para_temp)
    {
        //待请求参数数组
        $para = $this->buildRequestPara( $para_temp );
        $sHtml = "<form action='{$this->config[$this->mode]['submit_gateway']}' method='post'>";
        while(list ( $key, $val ) = each( $para ))
        {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml .= '</form>';

        $sHtml .= "<script>document.forms[0].submit();</script>";

        return $sHtml;
    }









}