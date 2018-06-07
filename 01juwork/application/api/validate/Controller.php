<?php
namespace app\api\validate;
use think\Validate;
class Controller extends Validate
{
    protected $rule = [
        'type'              => 'require',
        'controller_name'   => 'require',
        'controller'        => 'require',
        'formtpl_id'        => 'require',
    ];

    protected $message = [
        'type.require'              => '控制器类型必填',
        'controller_name.require'   => '控制器标题必填',
        'controller.require'        => '控制器名必填',
        'formtpl_id.require'        => '表单模板必填',
    ];


}
