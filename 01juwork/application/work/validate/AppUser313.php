<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-14 16:21:13
 */
namespace app\work\validate;
use think\Validate;
class AppUser313 extends Validate
{
    protected $rule = array (
  'name' => 'require',
  'access_key' => 'require',
  'secret_key' => 'require',
  'sign_code' => 'require',
);

    protected $message = array (
  'name.require' => '应用名称必填',
  'access_key.require' => 'access key必填',
  'secret_key.require' => 'secret key必填',
  'sign_code.require' => '签名密码串必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'name',
    1 => 'access_key',
    2 => 'secret_key',
    3 => 'sign_code',
  ),
  'edit' => 
  array (
    0 => 'name',
    1 => 'access_key',
    2 => 'secret_key',
    3 => 'sign_code',
  ),
);

}
