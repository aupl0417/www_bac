<?php
namespace app\admin\model;
use think\Db;
use think\Model;

/**
 *
 * User: lirong
 * Date: 2017/6/27
 * Time: 20:50
 */
class User extends Model{

    protected $name ='user';
    protected $userPlatForm ='db_user_platform';

    /**
     * 根据UID和项目ID查找是否已经存在
     * @param $uid
     * @param $platformId
     * @return bool
     */
    public function existUserByuidAndPlatform($uid,$platformId){

        if (empty($uid) && empty($platformId)){
            return false;
        }

        $data =  Db::name($this->name)->field('u.*,up.*')->alias('u')->join("$this->userPlatForm as up",'up.up_dttx_uid=u.u_dttx_uid')->where(['up_plateform_id'=>$platformId,'up_dttx_uid'=>$uid])->find();

        if (!empty($data)){
              return true;
        }else{
              return false;
        }

    }

    /**
     * 检查当前UID是否已经存在
     * @param $uid
     * @return bool
     */
    public function existUserByUid($uid){
        if (empty($uid)){
            return false;
        }
        $data =Db::name($this->name)->where("u_dttx_uid='$uid'")->find();
//        echo $data;
        if (!empty($data)){
            return $data;
        }else{
            return false;
        }
    }

}