<?php
/**
 * Mongodb 配置文件
 * User: lirong
 * Date: 2017/7/15
 * Time: 9:52
 */

return [
    // 数据库类型
    'type'           => 'think\\mongo\\Connection',
    // 服务器地址
    'hostname'       => '192.168.3.205',
    // 数据库名
    'database'       => 'tang',
    // 用户名
    'username'       => 'tang',
    // 密码
    'password'       => 'tang',
    // 端口
    'hostport'       => '27017',
    // 连接dsn
    'dsn'            => '',
    // 数据库连接参数
    'params'         => [],
    // 数据库编码默认采用utf8
    'charset'        => 'utf8',
    // 数据库表前缀
    'prefix'         => 'db_',
    //    命令前缀
    'cmd' =>'$',

    'query'  =>  'think\\mongo\\Query',


];