<?php
namespace app\wap\validate;
use think\Validate;

/**
 *
 * User: lirong
 * Date: 2017/7/17
 * Time: 11:19
 */
class SaleValidate extends Validate{

    protected $rule =[
        'goodsNum'=>'require',
//        'model'=>'require',
        'number'=>'require|number',
//        'price'=>'require',
        'dttxnick'=>'require',
        'receiver'=>'require',
        'phone'=>'require|number',
        'provinceId'=>'require',
        'cityId'=>'require',
        'regionId'=>'require',
        'address'=>'require|length:6,50'
    ];

    protected $message =[
        'goodsNum'=>'请选择商品',
//        'model'=>'请选择型号',
        'number.require'=>'请输入商品数量',
        'number.number'=>'商品数量必须为数字',
//        'price'=>'请输入商品价格',
        'dttxnick'=>'请输入大唐账号或手机号码',
        'receiver'=>'请输入收货人姓名',
        'phone.require'=>'请输入收货人电话',
        'phone.number'=>'收货人电话必须为数字',
        'provinceId'=>'请输入所在省',
        'cityId'=>'请输入所在城市',
        'regionId'=>'请输入所在地区',
        'address.require'=>'请输入收货地址',
        'address.length'=>'请填入详细的的收货地址'
    ];



}