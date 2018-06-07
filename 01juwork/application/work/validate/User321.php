<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-16 19:52:29
 */
namespace app\work\validate;
use think\Validate;
class User321 extends Validate
{
    protected $rule = array (
  'username' => 'require',
  'password' => 'require',
  'status' => 'require',
  'name' => 'require',
  'mobile' => 'require',
);

    protected $message = array (
  'username.require' => '账号必填',
  'password.require' => '密码必填',
  'status.require' => '状态必填',
  'name.require' => '姓名必填',
  'mobile.require' => '手机必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'username',
    1 => 'password',
    2 => 'status',
    3 => 'name',
    4 => 'mobile',
  ),
  'edit' => 
  array (
    0 => 'username',
    1 => 'password',
    2 => 'status',
    3 => 'name',
    4 => 'mobile',
  ),
);

}
