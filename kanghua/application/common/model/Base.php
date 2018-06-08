<?php
namespace app\common\model;

use think\Model;

class Base extends Model
{

    protected $table = 'base';

    public function getBaseList($field = '*', $condition, $order = 'ba_id asc', $limit = ''){
        $obj = db($this->table)->where($condition)->order($order)->field($field);
        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->select();
    }

    public function getBaseCount($condition){
        return db($this->table)->where($condition)->count();
    }

    public function getBaseById($id, $field = '*'){

        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(array('ba_id' => $id))->field($field)->find();
    }

    public function getBaseOne($field = '*', $where){
        if(!$where){
            return false;
        }

        return db($this->table)->where($where)->field($field)->find();
    }

    public function saveData($data, $where){

        if(!is_array($data) || empty($where)){
            return false;
        }

        return db($this->table)->where($where)->update($data);
    }

    public function createData($data){
        if(!is_array($data)){
            return false;
        }

        return db($this->table)->insert($data);
    }

    public function getRow($where, $field = '*', $order = 'ba_id desc'){
        if(!$where){
            return false;
        }

        return db($this->table)->where($where)->order($order)->field($field)->find();
    }

    public function deleteBase($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['ba_id' => $id])->update(['ba_isDelete' => 1]);
    }



}