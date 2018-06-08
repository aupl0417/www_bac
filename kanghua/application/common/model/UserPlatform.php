<?php
namespace app\common\model;
use think\Db;
use think\Model;

/**
 *
 * User: lirong
 * Date: 2017/7/8
 * Time: 16:38
 */
class UserPlatform extends Model{

    protected $name ="user_platform";

    /**
     * 查找当前推荐人的所有上级人员nick
     * @param $upid
     */
    public function findUpperLevel($fcode,$platformId){
        static $list =[];
        if (empty($fcode) || empty($platformId)){
            return false;
        }

        $res = Db::name($this->name)->alias('up')
            ->field('u_code,up_fcode,u_nick,up_plateform_id')
            ->join('user u','up.up_uid=u.u_id','left')
            ->where(['u_code'=>$fcode,'up_plateform_id'=>$platformId])
            ->find();

        if (!empty($res)){
            array_push($list,$res['u_nick']);
            $this->findUpperLevel($res['up_fcode'],$res['up_plateform_id']);
        }
        return $list;
    }

    /**
     * 根据user_platfrom主键查找用户信息
     * @param $upid
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function findUserPlatformInfoByUpid($upid){

        if (empty($upid)){
            return false;
        }

        $res = Db::name($this->name)
            ->alias('up')->field('up.*,u.*,ul.ul_name as ulname,ch.c_name as cname')
            ->join('user u','up.up_uid=u.u_id','left')->join('user_level ul','up.up_user_level_id=ul.ul_id','left')->join('channel ch','up.up_user_agent_level=ch.c_id','left')
            ->where(['up_id'=>$upid])
            ->find();
        return $res;

    }

    /*
     * 通过用户的推广码，找到该用户所邀请的会员
     * */
    public function findInviterListByCode($code, $field = '*'){

        if(!$code){
            return false;
        }

        return Db::name($this->name)->alias('up')
                ->join('user u', 'up.up_uid=u.u_id', 'LEFT')
                ->where(array('up.up_fcode' => $code, 'up.up_isActive' => 1, 'up.up_plateform_id' => session('user.platformId')))
                ->field($field)
                ->select();

    }

    public function upgrade($userId, $level = 1){
        return Db::name($this->name)->where(['up_id' => $userId])->update(['up_user_level_id' => $level]);
    }



}