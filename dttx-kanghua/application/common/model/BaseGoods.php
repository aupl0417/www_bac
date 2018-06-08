<?php
namespace app\common\model;

use think\Model;
use think\db;

class BaseGoods extends Model
{

    protected $table = 'base_goods';
    protected $prefix = 'db_';

    public function getGoodsList($field = '*', $condition, $order = 'bg_id asc', $limit = '', $distinct = false){
        $obj = db($this->table)->where($condition);
        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        if($distinct){
            $obj = $obj->distinct(true);
        }

        return $obj->order($order)->field($field)->select();
    }

    public function getBaseGoodsOne($field = '*', $where){
        if(!$where){
            return false;
        }

        return db($this->table)->where($where)->field($field)->find();
    }

    public function getAllGoodsInfoList($field = '*', $condition = array(), $join = array(), $order = 'bg_id asc', $limit = '', $distinct = false){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        if($distinct){
            $obj = $obj->distinct(true);
        }

        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->where($condition)->field($field)->order($order)->select();
    }

    /*
     * 获取商品型号
     * */
    public function getGoodsModel($condition = array(), $distinct = false){
        $where  = [ 'bg_isSale' => 1, 'bg_isDelete' => 0];
        if($condition){
            $where = array_merge($where, $condition);
        }

        $models = $this->getGoodsList('bg_model as model', $where, '', '', $distinct);
        return $models;
    }

    /*
     * 获取一条商品的所有数据
     * */
    public function getAllGoodsInfoOne($field = '*', $condition = array(), $join = array()){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        return $obj->where($condition)->field($field)->find();
    }

    public function getGoodsCount($where, $join = array()){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        return $obj->where($where)->count();
    }

    public function deleteBaseGoods($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['bg_id' => $id])->update(['bg_isDelete' => 1]);
    }

    public function recomendGoods($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['bg_id' => $id])->update(['bg_isRecommend' => 1]);
    }

    public function offShelf($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['bg_id' => $id])->update(['bg_isSale' => 0]);
    }

    public function onShelf($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['bg_id' => $id])->update(['bg_isSale' => 1]);
    }

}