<?php
namespace app\platform\validate;
use think\Validate;

/**
 *
 * User: lirong
 * Date: 2017/6/28
 * Time: 16:50
 */
class UserLevelValidate extends Validate{

    protected $rule =[
        'ul_user_no'=>'require',
        'ul_name'=>'require',
//        'ul_ratio'=>'require',
        'ul_upgrade_require'=>'require',
        'ul_level_mark'=>'require',
    ];

    protected $message =[
        'ul_user_no'=>'等级编号不能为空!',
        'ul_name'=>'等级名称不能为空！',
//        'ul_ratio'=>'分润比例不能为空！',
        'ul_upgrade_require'=>'升级要求不能为空!',
        'ul_level_mark'=>'会员福利不能为空!',
    ];

}