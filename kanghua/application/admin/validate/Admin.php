<?php
namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        ['username', 'require|checkUsername|length:3,12|unique:admin', '用户名必须|只能输入a-zA-Z0-9_的组合|用户名长度须在3-12个字符之间|用户名被占用'],
        ['roleid', 'require', '请选择角色'],
    ];

    // 自定义验证规则
    protected function checkUsername($value,$rule,$data)
    {
        //如果用户名中有空格，不允许注册
        if (strpos($value, ' ') !== false) {
            return false;
        }
        preg_match("/^[a-zA-Z0-9_]{0,64}$/", $value, $result);
    
        if (!$result) {
            return false;
        }
        return true;
    }
}