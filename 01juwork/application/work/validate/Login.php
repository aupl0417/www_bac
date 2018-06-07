<?php
namespace app\work\validate;
use think\Validate;
class Login extends Validate
{
    protected $rule = array (
        'username'  => 'require',
        'password'  => 'require',
        'vcode'     => 'require',
    );

    protected $message = array (
        'username.require'  => '账号必填',
        'password.require'  => '密码必填',
        'vcode.require'     => '验证码必填',
    );

}
