<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-16 16:38:11
 */
namespace app\work\validate;
use think\Validate;
class Menu317 extends Validate
{
    protected $rule = array (
  'name' => 'require',
);

    protected $message = array (
  'name.require' => '菜单名称必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'name',
  ),
  'edit' => 
  array (
    0 => 'name',
  ),
);

}
