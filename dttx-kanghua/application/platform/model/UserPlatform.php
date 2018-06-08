<?php
namespace app\platform\model;
use think\Db;
use think\Model;

/**
 *
 * User: lirong
 * Date: 2017/7/4
 * Time: 23:32
 */
class UserPlatform extends Model{

    protected $name ='user_platform';

    public function findDetailByUid($uid){

        if (empty($uid)){
            return false;
        }
        $map['up_id']=$uid;

        $res =Db::name($this->name)->alias('up')->field('up.*,u.u_nick,u.u_name,u.u_code,u.u_tel,a.a_name provinceName,b.a_name cityName,ur.ur_rolename,cl.c_name')
            ->join('user u','up.up_uid=u.u_id','left')
            ->join('area a','up.up_provinceId=a.a_id','left')
            ->join('area b','up.up_cityId=b.a_id','left')
            ->join('user_role ur','up.up_roleid=ur.ur_roleid','left')
            ->join('user_level ul','up.up_user_level_id=ul.ul_id','left')
            ->join('channel cl','up.up_user_agent_level=cl.c_id','left')
            ->where($map)->find();
        return $res;
    }


}