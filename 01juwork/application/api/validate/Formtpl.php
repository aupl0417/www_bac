<?php
namespace app\api\validate;
use think\Validate;
class Formtpl extends Validate
{
    protected $rule = [
        'tpl_name'          => 'require',
        'tables'            => 'require',
        'list_fields'       => 'require',
        'view_model'        => 'require',
        'relation_model'    => 'require',
    ];

    protected $message = [
        'tpl_name.require'      => '模板名称必填',
        'tables.require'        => '数据表名必填',
        'list_fields.require'   => '列表字段必填',
        'view_model.require'    => '列表字段必填',
        'relation_model.require'=> '列表字段必填',
    ];

    protected $scene = [
        'base'          => ['tpl_name','tables'],
        'listfields'    => ['list_fields'],
        'view'          => ['view_model'],
        'relation'      => ['relation_model'],
    ];

}
