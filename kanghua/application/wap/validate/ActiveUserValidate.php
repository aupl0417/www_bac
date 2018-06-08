<?php
namespace app\wap\validate;
use think\Validate;

/**
 *
 * User: lirong
 * Date: 2017/7/8
 * Time: 16:20
 */
class ActiveUserValidate extends Validate{


    protected $rule =[
        'up_provinceId'=>'require',
        'up_cityId'=>'require',
        'up_regionId'=>'require',
    //    'up_fcode'=>'require'
    ];

    protected $message =[
        'up_provinceId'=>'所在省不能为空!',
        'up_cityId'=>'所在市是不能为空!',
        'up_regionId'=>'所在区县不能为空!',
    //    'up_fcode'=>'推荐人不能为空!'
    ];




}