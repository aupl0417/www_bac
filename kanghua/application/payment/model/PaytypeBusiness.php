<?php
namespace app\payment\model;
use think\Db;
use think\Model;

/**
 *
 * User: lirong
 * Date: 2017/7/3
 * Time: 20:22
 */
class PaytypeBusiness extends Model{

    protected $name='paytype_business';

    public function findAllPayTypeList($filed="*"){
        $data= Db::name($this->name)->field($filed)->cache(true)->select();
        if (!empty($data)){
            return $data;
        }else{
            return false;
        }
    }






}