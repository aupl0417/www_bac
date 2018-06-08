<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 项目管理验证类
 * User: lirong
 * Date: 2017/6/26
 * Time: 23:24
 */
class Project extends Validate{

    protected $rule =[
        'name' =>'require',
        'companyname'=>'require',
        'contact'=>'require',
        'dttxnick'=>'require',
        'servicenick'=>'require',
        'serviceratio'=>'require',
//        'pl_provinceCode'=>'require',
//        'pl_cityCode'=>'require',
//        'pl_regionCode'=>'require',
    ];

    protected $message = [
        'name'=>'项目名称不能为空！',
        'companyname'=>'所属公司不能为空！',
        'contact'=>'联系方式不能为空！',
        'dttxnick'=>'大唐账号不能为空！',
        'servicenick'=>'服务费账号不能为空！',
        'serviceratio'=>'服务费比例不能为空！',
//        'pl_provinceCode'=>'请选择所在省',
//        'pl_cityCode'=>'请选择所在市',
//        'pl_regionCode'=>'请选择所在区',
    ];




}