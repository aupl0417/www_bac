<?php
namespace app\api\validate;
use think\Validate;
class FormtplGroup extends Validate
{
    protected $rule = [
        'group_name'  =>  'require',
    ];

    protected $message = [
        'group_name.require'  =>  '分组名必填',
    ];


}
