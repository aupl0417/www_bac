<?php
/**
 * 此文件由表单生成器创建，所以格式会有点凌乱
 * day:2017-06-14 17:31:27
 */
namespace app\work\validate;
use think\Validate;
class Controller314 extends Validate
{
    protected $rule = array (
  'controller_name' => 'require',
  'controller' => 'require',
  'type' => 'require',
  'formtpl_id' => 'require',
);

    protected $message = array (
  'controller_name.require' => '控制器名称必填',
  'controller.require' => '控制器必填',
  'type.require' => '类别必填',
  'formtpl_id.require' => '表单模板ID必填',
);

    protected $scene = array (
  'add' => 
  array (
    0 => 'controller_name',
    1 => 'controller',
    2 => 'type',
    3 => 'formtpl_id',
  ),
  'edit' => 
  array (
    0 => 'controller_name',
    1 => 'controller',
    2 => 'type',
    3 => 'formtpl_id',
  ),
);

}
