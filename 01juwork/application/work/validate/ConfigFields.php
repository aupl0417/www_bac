<?php
namespace app\work\validate;
use think\Validate;
class ConfigFields extends Validate
{
    protected $rule = [
        'group_id'      =>  'require',
        'label'         =>  'require',
        'name'          =>  'require',
        'formtype'      =>  'require',
    ];

    protected $message = [
        'group_id.require'      =>  '请选择字段分组',
        'label.require'         =>  '字段标签必填',
        'name.require'          =>  '字段名必填',
        'formtype.require'      =>  '请选择表单类型',
    ];


}
