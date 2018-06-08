<?php
namespace app\common\model;

use think\Model;
use think\Db;

class ShopKeeper extends Model
{

    protected $table = 'db_shopkeeper';
    protected $name= 'shopkeeper';


    public function getGoodsStockList($field = '*', $condition, $order = 'bg_id asc', $limit = ''){
        if(!$condition){
            return false;
        }

        $obj = db($this->name)->where($condition);
        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->order($order)->field($field)->select();
    }

    public function getShopKeeperOne($field = '*', $where){
        if(!$where){
            return false;
        }

        return db($this->name)->where($where)->field($field)->find();
    }

    public function getShopKeeperAll($field = '*', $condition = array(), $join = array(), $order = 's_id asc', $limit = ''){
        $obj =  Db::name($this->name)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        if(!empty($limit)){
            $obj = $obj->limit($limit);
        }

        return $obj->where($condition)->field($field)->order($order)->select();
    }

    public function getShopKeeperCount($where, $join = array()){
        $obj =  Db::name($this->name)->alias('a');
        if(!empty($join)){
            $obj = $obj->join($join);
        }

        return $obj->where($where)->count();
    }

    public function deleteShopKeeper($id){
        if(!$id || !is_numeric($id)){
            return false;
        }

        return Db::name($this->name)->where(['s_id' => $id])->update(['s_isDelete' => 1]);
    }

    public function blockShopKeeper($id, $act){
        return Db::name($this->name)->where(['s_id' => $id])->update(array( 's_isBlocked' => $act == 'block' ? 1 : 0));
    }

    public function createShopKeeper($userInfo = array()){
        $platformId =session('user.platformId');
        $data = array(
            's_name'     => input('post.shopName', '', 'htmlspecialchars,strip_tags,trim'),
            's_delivery' => input('post.delivery', 0, 'intval'),
            's_type'     => input('post.type', 0, 'intval'),
            's_userTrueName' => input('post.trueName',  '', 'htmlspecialchars,strip_tags,trim'),
            's_userDttxNick' => input('post.dttxnick', '', 'htmlspecialchars,strip_tags,trim'),
            's_provinceCode' => input('post.provinceCode', '', 'htmlspecialchars,strip_tags,trim'),
            's_cityCode'     => input('post.cityCode', '', 'htmlspecialchars,strip_tags,trim'),
            's_regionCode'   => input('post.regionCode', '', 'htmlspecialchars,strip_tags,trim'),
            's_description'  => input('post.description', '', 'htmlspecialchars,strip_tags,trim'),
            's_content'      => input('post.content', '', 'htmlspecialchars,strip_tags,trim'),
            's_state'        => $userInfo['state'],  //? 'pass' : 'none',如果是后台操作，则直接不用审核
            's_createId'     => $userInfo['createId'],
            's_operateId'    => $userInfo['operateId'],
            's_createTime'   => time(),
            's_updateTime'   => time(),
            's_projectId'    => $platformId,
        );

        $realAddress = input('post.realAddress', '', 'htmlspecialchars,strip_tags,trim');
        $webAddress  = input('post.webAddress', '', 'htmlspecialchars,strip_tags,trim');

        $where = array('s_userDttxNick' => $data['s_userDttxNick'], 's_state' => array('neq', 'deny'), 's_isDelete' => 0,'s_projectId'=>$platformId);
        $res   = Db::name('shopkeeper')->where($where)->field('s_id as id,s_state as state,s_isBlocked as isBlecked')->find();
        if($res){
            if($res['state'] == 'none'){
                return array('code' => 300, 'message' => '经销商资格在审核中，请耐心等待');
            }

            if($res['state'] == 'pass'){
                return array('code' => 300, 'message' => '已通过经销商资格审核，不能重复申请');
            }

            if($res['isBlocked'] == 1){
                return array('code' => 300, 'message' => '经销商资格已被冻结，请联系管理员');
            }
        }

//        $where = [ 'u_nick' => $data['s_userDttxNick'], 'u_name' => $data['s_userTrueName']];
        $where = [ 'u_nick' => $data['s_userDttxNick'],'up_plateform_id'=>$platformId];
//        if($userInfo){
//            $where['up_id'] = $userInfo['userId'];
//        }

        $user  = Db::name('user_platform up')->where($where)->field('up_id')->join('db_user u', 'up.up_uid=u.u_id')->find();
        if(!$user){
            return array('code' => 300, 'message' => '大唐会员名不存在');
        }

//        if(Db::name('shopkeeper')->where(['s_name' => $data['s_name'], 's_state' => array('neq', 'deny'),'s_projectId'=>$platformId])->count()){
//            return array('code' => 300, 'message' => '店铺名称已经存在，请重新输入');
//        }

        $data['s_address']  = $data['s_type'] == 0 ? $realAddress : $webAddress;
        $data['s_userId']   = $user['up_id'];

        if($data['s_type'] == 1){
            if(false === filter_var($webAddress, FILTER_VALIDATE_URL)){
                return array('code' => 300, 'message' => '店铺网址格式不正确');
            }
        }

        $result = self::validate('ShopKeeper')->save($data);
        if(!$result){
            return array('code' => 300, 'message' => self::getError());
        } else{
            if($userInfo['state']=='pass'){//如果是后台添加，则直接通过审核，并赋予权限
                Db::name('user_platform')->where(['up_id'=>$user['up_id']])->update(['up_roleid'=>3]);
            }
            return array('code' => 200, 'message' => '添加经销商成功');
        }
    }


    public function createShopKeeper_bak($userInfo = array(), $operator = array()){

        $data = array(
            's_name'     => input('post.shopName', '', 'htmlspecialchars,strip_tags,trim'),
            's_delivery' => input('post.delivery', 0, 'intval'),
            's_type'     => input('post.type', 0, 'intval'),
            's_userTrueName' => input('post.trueName',  '', 'htmlspecialchars,strip_tags,trim'),
            's_userDttxNick' => input('post.dttxnick', '', 'htmlspecialchars,strip_tags,trim'),
            's_provinceCode' => input('post.provinceCode', '', 'htmlspecialchars,strip_tags,trim'),
            's_cityCode'     => input('post.cityCode', '', 'htmlspecialchars,strip_tags,trim'),
            's_regionCode'   => input('post.regionCode', '', 'htmlspecialchars,strip_tags,trim'),
            's_description'  => input('post.description', '', 'htmlspecialchars,strip_tags,trim'),
            's_content'      => input('post.content', '', 'htmlspecialchars,strip_tags,trim'),
            's_state'        => $operator ? 'pass' : 'none',//如果是后台操作，则直接不用审核
            's_createId'     => $operator ? $operator['userId'] : 0,
            's_operateId'    => $operator ? $operator['userId'] : 0,
            's_createTime'   => time(),
            's_updateTime'   => time(),
            's_projectId'    => $operator ? $operator['platformId'] : $userInfo['platformId'],
        );

        $realAddress = input('post.realAddress', '', 'htmlspecialchars,strip_tags,trim');
        $webAddress  = input('post.webAddress', '', 'htmlspecialchars,strip_tags,trim');

        $where = array('s_userDttxNick' => $data['s_userDttxNick'], 's_state' => array('neq', 'deny'), 's_isDelete' => 0,'s_projectId'=>$operator['platformId']);
        $res   = Db::name('shopkeeper')->where($where)->field('s_id as id,s_state as state,s_isBlocked as isBlecked')->find();
        if($res){
            if($res['state'] == 'none'){
                return array('code' => 300, 'message' => '经销商资格在审核中，请耐心等待');
            }

            if($res['state'] == 'pass'){
                return array('code' => 300, 'message' => '已通过经销商资格审核，不能重复申请');
            }

            if($res['isBlocked'] == 1){
                return array('code' => 300, 'message' => '经销商资格已被冻结，请联系管理员');
            }
        }

//        $where = [ 'u_nick' => $data['s_userDttxNick'], 'u_name' => $data['s_userTrueName']];
        $where = [ 'u_nick' => $data['s_userDttxNick'],'up_plateform_id'=>$operator['platformId']];
//        if($userInfo){
//            $where['up_id'] = $userInfo['userId'];
//        }

        $user  = Db::name('user_platform up')->where($where)->field('up_id')->join('db_user u', 'up.up_uid=u.u_id')->find();
        if(!$user){
            return array('code' => 300, 'message' => '大唐会员名不存在');
        }

        if(Db::name('shopkeeper')->where(['s_name' => $data['s_name'], 's_state' => array('neq', 'deny'),'s_projectId'=>$operator['platformId']])->count()){
            return array('code' => 300, 'message' => '店铺名称已经存在，请重新输入');
        }

        $data['s_address']  = $data['s_type'] == 0 ? $realAddress : $webAddress;
        $data['s_userId']   = $user['up_id'];

        if($data['s_type'] == 1){
            if(false === filter_var($webAddress, FILTER_VALIDATE_URL)){
                return array('code' => 300, 'message' => '店铺网址格式不正确');
            }
        }

        $result = self::validate('ShopKeeper')->save($data);
        if(!$result){
            return array('code' => 300, 'message' => self::getError());
        } else{
            if($operator){//如果是后台添加，则直接通过审核，并赋予权限
                Db::name('user_platform')->where(['up_id'=>$user['up_id']])->update(['up_roleid'=>3]);
            }
            return array('code' => 200, 'message' => '添加经销商成功');
        }
    }

}