<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-16 18:47:37
 */
namespace app\work\validate;
use think\Validate;
class AdminGroup318 extends Validate
{
    protected $rule = array (
  'group_name' => 'require',
);

    protected $message = array (
  'group_name.require' => '名称必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'group_name',
  ),
  'edit' => 
  array (
    0 => 'group_name',
  ),
);

}
