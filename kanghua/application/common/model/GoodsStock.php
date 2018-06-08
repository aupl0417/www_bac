<?php
namespace app\common\model;

use think\Model;
use think\db;

class GoodsStock extends Model
{

    protected $table = 'goods_stock';
    protected $prefix = 'db_';

    public function getGoodsStockList($field = '*', $condition, $order = 'gs_id asc', $limit = ''){
        $obj = db($this->table)->where($condition);
        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->order($order)->field($field)->select();
    }

    public function getGoodsStockOne($field = '*', $where){
        if(!$where){
            return false;
        }

        return db($this->table)->where($where)->field($field)->find();
    }

    public function getGoodsStockAll($field = '*', $condition = array(), $join = array(), $order = 'gs_id asc', $limit = ''){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->where($condition)->field($field)->order($order)->select();
    }

    public function getAllGoodsStockOne($field = '*', $condition = array(), $join = array()){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        return $obj->where($condition)->field($field)->find();
    }

    public function getGoodsStockCount($where, $join = array()){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        return $obj->where($where)->count();
    }

    public function deleteGoodsStock($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['bg_id' => $id])->update(['bg_isDelete' => 1]);
    }

}