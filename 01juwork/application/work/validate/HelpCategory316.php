<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-16 15:18:07
 */
namespace app\work\validate;
use think\Validate;
class HelpCategory316 extends Validate
{
    protected $rule = array (
  'category_name' => 'require',
);

    protected $message = array (
  'category_name.require' => '名称必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'category_name',
  ),
  'edit' => 
  array (
    0 => 'category_name',
  ),
);

}
