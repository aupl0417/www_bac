<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Category extends Model
{

    protected $table = 'category';
    protected $prefix = 'db_';

    public function getCateList($field = '*', $condition, $join = false, $order = 'ca_id asc', $limit = ''){

        if(!$condition){
            return false;
        }

        $obj = Db::name($this->table)->where($condition)->alias('ca')
            ->order($order)
            ->field($field);

        if($join){
            $obj->join('db_category c','ca.ca_pid = c.ca_id', 'LEFT');
        }

        if($limit){
            $obj->limit($limit);
        }

        return $obj->select();
    }

    public function getCateCount($where){
        return Db::table($this->prefix . $this->table)
            ->alias('ca')
            ->join('db_category c','ca.ca_pid = c.ca_id')
            ->where($where)->count();
    }

    public function getCategoryById($id, $field = '*'){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return Db::name($this->table)->where(['ca_id' => $id])->field($field)->find();
    }

}