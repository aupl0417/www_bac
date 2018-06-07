<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-20 21:15:45
 */
namespace app\work\validate;
use think\Validate;
class Channel324 extends Validate
{
    protected $rule = array (
  'category_id' => 'require',
  'name' => 'require',
  'status' => 'require',
);

    protected $message = array (
  'category_id.require' => '分类必填',
  'name.require' => '标题必填',
  'status.require' => '状态必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'category_id',
    1 => 'name',
    2 => 'status',
  ),
  'edit' => 
  array (
    0 => 'category_id',
    1 => 'name',
    2 => 'status',
  ),
);

}
