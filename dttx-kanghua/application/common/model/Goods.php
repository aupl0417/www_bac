<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Goods extends Model
{

    protected $table = 'goods';
    protected $prefix = 'db_';

    public function getGoodsList($field = '*', $condition, $order = 'g_id asc', $limit = '1'){

        if(!$condition){
            return false;
        }

        return db($this->table)->where($condition)
                                ->order($order)
                                ->field($field)->limit($limit)->select();
    }

    public function getAllGoodsInfoList($field = '*', $condition = array(), $order = 'g_id asc', $limit = array()){
        return Db::table($this->prefix . $this->table)
            ->alias('a')
            ->join($this->prefix . 'base_goods w','a.g_baseGoodsId = w.bg_id')
            ->join($this->prefix . 'category b','w.bg_cateId=b.ca_id')
            ->where($condition)
            ->field($field)
            ->order($order)
            ->select();
    }

    public function getGoodsCount($where){
        return Db::table($this->prefix . $this->table)
            ->alias('a')
            ->join($this->prefix . 'base_goods w','a.g_baseGoodsId = w.bg_id')
            ->join($this->prefix . 'category b','w.bg_cateId=b.ca_id')
            ->where($where)->count();
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

        return db($this->table)->where(['g_id' => $id])->update(['g_isRecommend' => 1]);
    }

    public function offShelf($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['g_id' => $id])->update(['g_isSale' => 2]);
    }

}