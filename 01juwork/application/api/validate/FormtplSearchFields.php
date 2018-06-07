<?php
namespace app\api\validate;
use think\Validate;
class FormtplSearchFields extends Validate
{
    protected $rule = [
        'label'         =>  'require',
        'name'          =>  'require',
        'formtype'      =>  'require',
    ];

    protected $message = [
        'label.require'         =>  '字段标签必填',
        'name.require'          =>  '字段名必填',
        'formtype.require'      =>  '请选择表单类型',
    ];


}
