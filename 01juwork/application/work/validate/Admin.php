<?php
namespace app\work\validate;
use think\Validate;
class Admin extends Validate
{
    protected $rule = [
        'password'              => 'require|length:6,20|alphaDash',
        'password2'             => 'require|confirm:password',
        'old_password'          => 'require',
    ];

    protected $message = [
        'password.require'              => '新密码必填',
        'password.length'               => '密码长度必须在6~20个字符之间',
        'password.alphaDash'            => '密码格式错误，必须由字母和数字，下划线_及破折号-组成',
        'password2.require'             => '请再次输入新密码',
        'password2.confirm'             => '两次新密码不一致',
        'old_password.require'          => '请输入旧密码',
    ];

    protected $scene = [
        'password'  => ['password','password2','old_password'],
    ];
}
