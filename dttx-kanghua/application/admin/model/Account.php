<?php
namespace app\admin\model;
use think\Db;
use think\Model;

/**
 * 用户账户表操作类
 * User: lirong
 * Date: 2017/6/27
 * Time: 20:08
 */
class Account extends Model{

    protected $name ='account';

    public function existAccount($uid,$platformId){
        $data = Db::name($this->name)->where(['a_dttx_uid'=>$uid,'a_platform_id'=>$platformId])->find();
        if (!empty($data)){
            return true;
        }else{
            return false;
        }
    }

}