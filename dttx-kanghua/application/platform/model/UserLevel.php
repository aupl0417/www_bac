<?php
namespace app\platform\model;
use think\Db;
use think\Model;
use think\Session;

/**
 *
 * User: lirong
 * Date: 2017/6/28
 * Time: 15:21
 */
class UserLevel extends Model{


    protected $name ='user_level';

    public function findUserLevelList($data){

        if (empty($data) || !is_array($data)){
            return false;
        }
        $map['ul_isDelete']=0;
        $pagesize =empty($input['pageSize'])?"30":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);

        if (Session::has('admin_userid')){
            $platformId = isset($data['platformId'])?$data['platformId']:"0";
        }else{
            $platformId =Session::get('user.platformId');
        }

        $map['ul_platform_id']=$platformId;
        if (!empty($data['name'])){
            $map['ul_name']=$data['name'];
        }

        $data['list'] =Db::name($this->name)->where($map)->page($page,$pagesize)->order($data['orderField'],$data['orderDirection'])->select();
        $data['count']=Db::name($this->name)->where($map)->count();

        if (!empty($data)){
            return $data;
        }else{
            return false;
        }
    }

    public function findDetaliByid($id){

        if (empty($id)){
            return false;
        }

        $data =Db::name($this->name)->where(['ul_id'=>$id])->find();
        if (!empty($data)){
            return $data;
        }else{
            return false;
        }


    }

    /**
     * 根据id更新为删除状态
     * @param $id
     * @return bool
     */
    public function removeByid($id){
        if (empty($id)){
            return false;
        }
        $res = Db::name($this->name)->where(['ul_id'=>['in',$id]])->update(['ul_isDelete'=>1]);
        if (!empty($res)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 查找所属项目下所有信息
     * @param $platform_id
     * @return bool|false|\PDOStatement|string|\think\Collection
     */
    public function findlistByPlatformId($platform_id){
        if (empty($platform_id)){
            return false;
        }

        $res =Db::name($this->name)->where(['ul_platform_id'=>$platform_id,'ul_isDelete'=>0,'ul_status'=>1])->select();
        return $res;

    }


}