<?php
//配置文件
return [
    // 默认控制器名
    'default_controller'     => 'Store',
    // 默认操作名
    'default_action'         => 'index',

    'wap_dispatch_success_tmpl'  => APP_PATH . 'wap' . DS . 'view/common/dispatch_success.tpl',
    'wap_dispatch_error_tmpl'    => APP_PATH . 'wap' . DS . 'view/common/dispatch_error.tpl',
];