<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/14 0014
 * Time: 13:39
 */
namespace app\wap\validate;
use think\Validate;


class Address extends Validate{

    protected $rule = [
        'ad_userNick'   => 'require',
        'ad_phone'      => 'require|1[34578]\d{9}',
        'ad_provinceId' => 'require|integer',
        'ad_cityId'     => 'require|integer',
        'ad_regionId'   => 'require|integer',
//        'ad_postage'    => 'require|^[0-9][0-9]{5}$',
        'ad_address'    => 'require',
    ];

    protected $message = [
        'ad_userNick'   => '请输入收货人姓名',
        'ad_phone'      => '请输入收货人手机号码',
        'ad_provinceId' => '请选择省份',
        'ad_cityId'     => '请选择城市',
        'ad_regionId'   => '请选择区县',
//        'ad_postage'    => '请输入正确格式的邮政编码',
        'ad_address'    => '请输入详细地址',
    ];

}