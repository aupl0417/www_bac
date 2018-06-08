<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Agent extends Model
{

    protected $table = 'agent';
    protected $prefix = 'db_';


    public function getAgentList($field = '*', $condition, $order = 'a_id asc', $limit = ''){
        if(!$condition){
            return false;
        }

        $obj = db($this->table)->where($condition);
        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->order($order)->field($field)->select();
    }

    public function getAgentOne($field = '*', $where){
        if(!$where){
            return false;
        }

        return db($this->table)->where($where)->field($field)->find();
    }

    public function getAgentAll($field = '*', $condition = array(), $join = array(), $order = 'a_id asc', $limit = ''){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->where($condition)->field($field)->order($order)->select();
    }


    public function getAllAgentOne($field = '*', $condition = array(), $join = array()){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->where($condition)->field($field)->find();
    }


    public function getAgentCount($where, $join = array()){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        return $obj->where($where)->count();
    }

    public function deleteShopKeeper($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['a_id' => $id])->update(['a_isDelete' => 1]);
    }

    public function blockAgent($id, $act){
        return db($this->table)->where(['a_id' => $id])->update(array( 'a_isBlocked' => $act == 'block' ? 1 : 0));
    }

}