<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/14 0014
 * Time: 9:43
 */
namespace app\common\validate;
use think\Validate;


class ShopKeeper extends Validate{

    protected $rule = [
        's_userTrueName' => 'require',
        's_userDttxNick' => 'require',
//        's_name'         => 'require',
        's_provinceCode' => 'require',
        's_cityCode'     => 'require',
        's_regionCode'   => 'require',
//        's_address'      => 'require',
    ];

    protected $message = [
        's_userTrueName' => '请输入经销商姓名',
        's_userDttxNick' => '请输入大唐会员名',
//        's_name'         => '请输入店铺名称',
        's_provinceCode' => '请选择省份',
        's_cityCode'     => '请选择城市',
        's_regionCode'   => '请选择区县',
//        's_address'      => '请输入店铺地址/网址',
    ];

}