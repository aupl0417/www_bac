<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-17 11:27:00
 */
namespace app\work\validate;
use think\Validate;
class FormtplType322 extends Validate
{
    protected $rule = array (
  'type_name' => 'require',
  'formtype' => 'require',
  'status' => 'require',
);

    protected $message = array (
  'type_name.require' => '名称必填',
  'formtype.require' => '表单类型必填',
  'status.require' => '状态必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'type_name',
    1 => 'formtype',
    2 => 'status',
  ),
  'edit' => 
  array (
    0 => 'type_name',
    1 => 'formtype',
    2 => 'status',
  ),
);

}
