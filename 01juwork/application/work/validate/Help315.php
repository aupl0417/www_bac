<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-16 19:59:47
 */
namespace app\work\validate;
use think\Validate;
class Help315 extends Validate
{
    protected $rule = array (
  'category_id' => 'require',
  'name' => 'require',
  'content' => 'require',
);

    protected $message = array (
  'category_id.require' => '分类必填',
  'name.require' => '标题必填',
  'content.require' => '内容必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'category_id',
    1 => 'name',
    2 => 'content',
  ),
  'edit' => 
  array (
    0 => 'category_id',
    1 => 'name',
    2 => 'content',
  ),
);

}
