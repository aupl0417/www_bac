<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Channel extends Model
{

    protected $table = 'channel';
    protected $prefix = 'db_';

    public function getChannelList($field = '*', $condition, $order = 'c_id asc', $limit = ''){

        if(!$condition){
            return false;
        }
        $obj = db($this->table)->alias('c')->join('user_role ur','c.c_roleIds=ur.ur_roleid','left')->where($condition)->order($order)->field($field.',ur_rolename as rolename');

        if(!$limit){
            $obj = $obj->limit($limit);
        }

        return $obj->select();
    }


    public function getChannelOne($field = '*', $condition){

        if(!$condition){
            return false;
        }

        return db($this->table)->where($condition)->field($field)->find();
    }

    public function getChannelCount($where){
        return Db::name($this->table)->where($where)->count();
    }

    public function deleteChannel($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['c_id' => $id])->update(['c_isDelete' => 1]);
    }

}