<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-17 09:59:51
 */
namespace app\work\validate;
use think\Validate;
class ConfigCategory320 extends Validate
{
    protected $rule = array (
  'category_name' => 'require',
  'group_name' => 'require',
);

    protected $message = array (
  'category_name.require' => '名称必填',
  'group_name.require' => '分组参数名必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'category_name',
    1 => 'group_name',
  ),
  'edit' => 
  array (
    0 => 'category_name',
    1 => 'group_name',
  ),
);

}
