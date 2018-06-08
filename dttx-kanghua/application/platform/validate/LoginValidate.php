<?php
namespace app\platform\validate;
use think\Validate;

/**
 * 登录验证类
 * User: lirong
 * Date: 2017/7/5
 * Time: 15:15
 */
class LoginValidate extends Validate{

    protected $rule =[
        'username'=>'require',
        'password'=>'require',
        'captcha'=>'require'
    ];

    protected $message=[
        'username'=>'登录用户名不能为空!',
        'password'=>'登录密码不能为空!',
        'captcha'=>'验证码不能为空!'
    ];



}