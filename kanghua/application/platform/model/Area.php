<?php
namespace app\platform\model;
use think\Cache;
use think\Db;
use think\Model;

/**
 *
 * User: lirong
 * Date: 2017/6/29
 * Time: 18:45
 */
class Area extends Model{

    protected $name ='area';

    /**
     * 根据父id获取所属数据
     * @param $parentId
     * @return bool|false|\PDOStatement|string|\think\Collection
     */
    public function findListByParentId($parentId=null){

        if (!isset($parentId)){
            return false;
        }
        $cacheId='area_'.$parentId;
        if (Cache::has($cacheId)){
            return Cache::get($cacheId);
        }else{
            $data =Db::name($this->name)->where(['a_parenId'=>$parentId,'a_status'=>0])->select();
            if (!empty($data)){
                Cache::set($cacheId,$data,3600);
                return $data;
            }else{
                return false;
            }
        }
    }

    /**
     * 根据ID获取单条数据详情
     * @param $aid
     * @return bool
     */
    public function findAreaByid($aid){
        if (empty($aid)){
            return false;
        }
        Db::name($this->name)->where(['a_id'=>$aid])->find();
    }




}