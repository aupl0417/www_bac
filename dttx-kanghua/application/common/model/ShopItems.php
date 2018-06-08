<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Cache;

class ShopItems extends Model
{

    protected $table = 'shop_items';
    protected $prefix = 'db_';


    public function getShopItemsOne($field = '*', $where, $join = array(), $order = ''){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!$where){
            return false;
        }

        if(!empty($join)){
            $obj = $obj->join($join);
        }

        if(!empty($order)){
            $obj = $obj->order($order);
        }

        return $obj->where($where)->field($field)->find();
    }

    public function getShopItemsAll($field = '*', $condition = array(), $join = array(), $order = 'si_id asc', $limit = '' , $distinct = false){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        if($distinct){
            $obj = $obj->distinct(true);
        }

        return $obj->where($condition)->field($field)->order($order)->select();
    }

    public function getShopItemsCount($where, $join = array()){
        $obj =  Db::table($this->prefix . $this->table)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        return $obj->where($where)->count();
    }

    /*
     * 获取商品型号
     * */
    public function getGoodsModel($condition = array(), $distinct = false){
        $cacheKey = md5(json_encode(func_get_args()));
        if(!($model = Cache::get($cacheKey))){
            $where  = [ 'si_isSale' => 1, 'si_isDelete' => 0];
            if($condition){
                $where = array_merge($where, $condition);
            }

            $join = [
                ['db_base_goods bg', 'bg.bg_id=a.si_goodsId', 'LEFT']
            ];

            $models = $this->getShopItemsAll('bg_model as model', $where, $join, '', '', $distinct);
            Cache::set($cacheKey, $model, 3600);
        }

        return $models;
    }

    /**
     * 获取同一个创建者所有同编号商品
     * @param $number
     * @param $createId
     * @return bool|false|\PDOStatement|string|\think\Collection
     */
    public function getSameGoodsModelByNumberAndCreateID($number,$createId){
        if (empty($number) || empty($createId)){
            return false;
        }
        $res =Db::name($this->table)->alias('si')->field('si_id,si_goodsId,bg_number,bg_price,bg_model')->join('base_goods bg','si.si_goodsId=bg.bg_id','left')->where(['bg_number'=>$number,'si_createId'=>$createId,'si_isDelete'=>0,'si_isSale'=>1])->select();
//        echo Db::name($this->table)->alias('si')->field('si_id,si_goodsId,bg_number,bg_price,bg_model')->join('base_goods bg','si.si_goodsId=bg.bg_id','left')->where(['bg_number'=>$number,'si_createId'=>$createId,'si_isDelete'=>0,'si_isSale'=>1])->buildSql();
        return $res;
    }








    public function deleteShopItems($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['si_id' => $id])->update(['si_isDelete' => 1]);
    }

    public function recomendShopItems($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['si_id' => $id])->update(['si_isRecommend' => 1]);
    }

    public function offShelf($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['si_id' => $id])->update(['si_isSale' => 0]);
    }

    public function onShelf($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return db($this->table)->where(['si_id' => $id])->update(['si_isSale' => 1]);
    }

    public function addRecommendedGoods($userId, $operateId){
        if(!$userId || !$operateId){
            return ajaxCallBack(300, '用户id或操作者id非法');
        }

        //查询所有推荐商品
        $where = array('bg_isSale' => 1, 'bg_isRecommend' => 1, 'bg_isDelete' => 0);
        $goodsIds = model('BaseGoods')->getGoodsList('bg_id as id', $where);
        if(!$goodsIds){
            return ajaxCallBack(200, '操作成功');
        }

        $goodsIds = array_column($goodsIds, 'id');

        //经销商的平台id
        $userPlatform = Db::name('user_platform')->where(array('up_uid' => $userId, 'up_isDelete' => 0))->field('up_plateform_id')->find();
        if(!$userPlatform){
            return ajaxCallBack(300, '用户不存在');
        }

        $data = array();
        foreach($goodsIds as $key => $val){
            $data[$key] = array(
                'si_goodsId' => $val,
                'si_createId' => $userId,
                'si_model'      => '',
                'si_channel'    => '',
                'si_stock'      => 0,
                'si_isSale'     => 1,
                'si_createTime' => time(),
                'si_operateId'  => $operateId,
                'si_updateTime' => time(),
                'si_projectId'  => $userPlatform['up_plateform_id'],
            );
        }

        $res = db($this->table)->insertAll($data);
        if(!$res){
            return ajaxCallBack(300, '添加默认推荐商品失败');
        }else{
            return ajaxCallBack(200, '操作成功');
        }
    }

}