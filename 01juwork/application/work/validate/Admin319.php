<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-16 19:09:08
 */
namespace app\work\validate;
use think\Validate;
class Admin319 extends Validate
{
    protected $rule = array (
  'group_id' => 'require',
  'status' => 'require',
  'username' => 'require',
  'password' => 'require',
  'name' => 'require',
);

    protected $message = array (
  'group_id.require' => '角色分组必填',
  'status.require' => '状态必填',
  'username.require' => '账号必填',
  'password.require' => '密码必填',
  'name.require' => '姓名必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'group_id',
    1 => 'status',
    2 => 'username',
    3 => 'password',
    4 => 'name',
  ),
  'edit' => 
  array (
    0 => 'group_id',
    1 => 'status',
    2 => 'username',
    3 => 'password',
    4 => 'name',
  ),
);

}
