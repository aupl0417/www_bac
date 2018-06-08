<?php
namespace app\platform\controller;
use app\common\controller\Platform;
use app\platform\model\UserPlatform;
use think\Cache;
use think\Db;
use think\Request;
use think\Session;

/**
 * 用户列表
 * User: lirong
 * Date: 2017/7/4
 * Time: 18:12
 */
class Userlist extends Platform{

    /**
     * 用于首页数据展示
     * @return mixed
     */
    public function index()
    {
        $input['nick']=input('post.nick','','trim');
        $input['name']=input('post.name','','trim');
        $input['roleid']=input('post.roleid','','trim');

        $input['pageSize']=input('post.pageSize','30','intval');
        $input['pageCurrent']=input('post.pageCurrent','1','intval');
        $input['orderField']=input('post.orderField','up_create_time','trim');
        $input['orderDirection']=input('post.orderDirection','desc','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');
        $user =new \app\platform\model\User($input);
        $data = $user->findRoleUserList($input);

        $userRole =new \app\platform\model\UserRole();
        $roles =$userRole->findAllUserRole();

        $this->assign('roles',$roles);
        $this->assign('input',$input);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 增加数据
     * @return mixed
     */
    public function create(){}

    /**
     *修改数据
     * @return mixed
     */
    public function edit()
    {
        if (Request::instance()->isPost()){
            $roleid =input('roleid','','trim');
            $rid =input('rid','','trim');
            $res = Db::name('user_platform')->where(['up_id'=>$rid])->update(['up_roleid'=>$roleid]);
            if ($res){
                $this->ajaxReturn(ajaxCallBack('200','修改成功!',true,'platform_Userlist_index'));
            }else{
                $this->ajaxReturn(ajaxCallBack('300','修改失败!'));
            }
        }else{
            $uid =Request::instance()->param('id','0','intval');

            $userPlatform =new UserPlatform();
            $data =$userPlatform->findDetailByUid($uid);
            if (empty($data)){
                $this->ajaxReturn(ajaxCallBack(300,'用户数据不存在或已被删除，请重试!'));
            }


            $userRole =new \app\platform\model\UserRole();
            $roles =$userRole->findAllUserRole($data['up_plateform_id']);
            $this->assign('roles',$roles);
            $this->assign('data',$data);
            return $this->fetch();
        }

    }

    /**
     * 移除数据
     * @return mixed
     */
    public function remove(){}


    public function change(){

        $id =Request::instance()->param('id','0','intval');
        $state =Request::instance()->param('state','0','intval');

        $res =Db::name('user_platform')->where(['up_id'=>$id])->update(['up_states'=>$state]);
        if ($res){
            $this->ajaxReturn(ajaxCallBack(200,'操作成功!'));
        }else{
            $this->ajaxReturn(ajaxCallBack(300,'操作失败!'));
        }

    }
}