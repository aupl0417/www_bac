<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8 0008
 * Time: 9:55
 */

namespace app\common\Model;

use think\Model;
use think\Db;
use think\cache;

class Bank extends Model{

    protected $name = 'bank';

    public function getBankList($field = '*', $type = 0){
        $cacheKey = md5('bank_list_' . json_encode($field) . $type);
        $data = Cache::get($cacheKey);
        if(!$data){
            $data = Db::name($this->name)->where(['bank_type' => $type])->field($field)->order('bank_id asc')->select();
            Cache::set($cacheKey, $data, 86400);
        }

        return $data;
    }

//    public function getBankByCode($code){
//        $bank = $data = Cache::get($cacheKey);
//    }

}