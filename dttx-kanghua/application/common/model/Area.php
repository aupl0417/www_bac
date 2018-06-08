<?php
namespace app\common\model;

use think\Cache;
use think\Db;
use think\Model;

class Area extends Model
{
    protected $table = 'area';

    public function getAreaByParentId($field = '*', $parentId = 0){

        if(!is_numeric($parentId)){
            return false;
        }

        return db($this->table)->where(['a_parenId' => $parentId])->field($field)->select();
    }

    public function getAreaByCode($field = '*', $code){
        if(!$code || !is_numeric($code)){
            return false;
        }

        return db($this->table)->where(['a_code' => $code])->field($field)->select();
    }

    public function getAreaById($field = '*', $id){
        $cacheKey = md5(json_encode(func_get_args()));
        if(!($data = Cache::get($cacheKey))){
            if(!$id || !is_numeric($id)){
                return false;
            }

            $data = db($this->table)->where(['a_id' => $id])->field($field)->find();
            Cache::set($cacheKey, $data, 86400);
        }

        return $data;
    }

    /**
     * 根据id获取名称
     * @param $aid
     * @return mixed
     */
    public function getAreaNameById($aid){
        $res =Db::name($this->name)->field('a_name')->where('a_id',$aid)->find();
        if (!empty($res)){
            return $res['a_name'];
        }
    }

}