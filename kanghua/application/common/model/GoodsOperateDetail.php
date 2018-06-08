<?php
namespace app\common\model;

use think\Model;
use think\Db;

class GoodsOperateDetail extends Model
{

    protected $table = 'goods_operate_detail';
    protected $prefix = 'db_';

    public function getOperateDetailList($field = '*', $condition, $order = 'od_id asc', $limit = ''){
        if(!$condition){
            return false;
        }

        $obj = db($this->table)->where($condition);
        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->order($order)->field($field)->select();
    }

    public function getOperateDetailOne($field = '*', $where){
        if(!$where){
            return false;
        }

        return db($this->table)->where($where)->field($field)->find();
    }

    public function getOperateDetailAll($field = '*', $condition = array(), $join = array(), $order = 'od_id asc', $limit = ''){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->where($condition)->field($field)->order($order)->select();
    }

    public function getAllOperateDetailOne($field = '*', $condition = array(), $join = array()){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        return $obj->where($condition)->field($field)->find();
    }

    public function getOperateDetailCount($where, $join = array()){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        return $obj->where($where)->count();
    }

    public function deleteOrder($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['od_id' => $id])->update(['od_isDelete' => 1]);
    }

    public function updateOrder($data, $where){

        if(!$data || !$where){
            return false;
        }

        return db($this->table)->where($where)->update($data);
    }

}