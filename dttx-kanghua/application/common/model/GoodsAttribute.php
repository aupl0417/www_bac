<?php
namespace app\common\model;

use think\Model;
use think\Db;

class GoodsAttribute extends Model
{

    protected $table = 'goods_attribute';
    protected $prefix = 'db_';

    public function getGoodsAttributeList($field = '*', $condition, $order = 'ga_id asc', $limit = '1'){

        if(!$condition){
            return false;
        }

        return db($this->table)->where($condition)
                                ->order($order)
                                ->field($field)->limit($limit)->select();
    }

}