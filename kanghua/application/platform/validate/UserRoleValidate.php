<?php
namespace app\platform\validate;
use think\Validate;

/**
 *
 * User: lirong
 * Date: 2017/7/4
 * Time: 16:14
 */
class UserRoleValidate extends Validate{

    protected $rule =[
        'ur_rolename' =>'require',
        'ur_description'=>'require'
    ];

    protected $message =[
        'ur_rolename' =>'角色名称不能为空!',
        'ur_description' =>'角色描述不能为空!'
    ];

}