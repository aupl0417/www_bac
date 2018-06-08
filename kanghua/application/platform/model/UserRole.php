<?php
namespace app\platform\model;
use think\Db;
use think\Model;
use think\Session;

/**
 *
 * User: lirong
 * Date: 2017/7/4
 * Time: 15:36
 */
class UserRole extends Model{

    protected $name ='user_role';
    protected $debug= false;  //调试开关

    /**
     * 查找用户角色列表
     */
    public function findUserRoleList($input){

        $pagesize =empty($input['pageSize'])?"30":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);

        if (Session::has('admin_userid')){
            $platformId = isset($input['platformId'])?$input['platformId']:"0";
        }else{
            $platformId =Session::get('user.platformId');
        }

        $rolename =input('post.rolename',"",'trim');

        $map['ur_is_delete']=0;
        $map['ur_platform_id']=$platformId;
        if (!empty($rolename)){
            $map['ur_rolename']=$rolename;
        }

        $data['list'] =Db::name($this->name)->where($map)->whereOr('ur_platform_id',0)->page($page,$pagesize)->order($input['orderField'],$input['orderDirection'])->fetchSql($this->debug)->select();
        $data['count']=Db::name($this->name)->where($map)->fetchSql($this->debug)->count();
        if ($this->debug){
            print_r($data);
        }
        return $data;

    }

    /**
     * 根据id获取角色详细信息
     * @param $id
     * @param bool $plateform
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function findRoleByid($id){

        if (empty($id)){
            return false;
        }
        $map['ur_roleid']=$id;

        $res = Db::name($this->name)->where($map)->fetchSql($this->debug)->find();
        if ($this->debug){
            print_r($res);
        }
        return $res;
    }


    /**
     * 带检查角色用户版移除角色
     * @param $roleid
     * @param $platformId
     * @return array
     */
    public function removeRoleByid($roleid,$platformId){
        if (empty($roleid) || empty($platformId)){
            return ['code'=>300,'message'=>'参数错误!'];
        }

        $count =Db::name('user_platform')->where(['up_plateform_id'=>$platformId,'up_roleid'=>$roleid])->count();
        if ($count>0){
            return ['code'=>300,'message'=>'该角色下有'.$count.'位用户正在使用，不能删除!'];
        }else{
            $res = Db::name($this->name)->where(['ur_roleid'=>$roleid])->update(['ur_is_delete'=>1]);
            if ($res){
                return ['code'=>200,'message'=>'移除角色成功！'];
            }else{
                return ['code'=>300,'message'=>'移除角色失败，请重试!'];
            }
        }


    }

    public function changeStatus($roleid,$state,$platformId=true){
        if (empty($roleid)){
            return false;
        }
        if ($platformId===true){
            $platformId=Session::get('user.platformId');
        }else{
            $platformId =intval($platformId);
        }
        $map['ur_platform_id']=$platformId;
        $map['ur_roleid']=$roleid;

        $res = Db::name($this->name)->where($map)->update(['ur_status'=>$state]);
        return $res;
    }

    /**
     * 查找所有角色列表用于下拉选择
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function findAllUserRole($platformId=''){
        if (empty($platformId)){
            $platformId=Session::get('user.platformId');
        }
        $res = Db::name($this->name)->where(['ur_platform_id'=>$platformId,'ur_status'=>1,'ur_is_delete'=>0])->whereOr('ur_platform_id',0)->order('ur_roleid asc')->select();
        return $res;
    }




}