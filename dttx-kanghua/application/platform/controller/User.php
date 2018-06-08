<?php
namespace app\platform\controller;
use app\common\controller\Platform;
use app\common\tools\Logs;
use app\platform\model\Area;
use think\Db;
use think\Exception;
use think\Request;
use think\Session;
use app\platform\model\UserPlatform;


/**
 *
 * User: lirong
 * Date: 2017/6/28
 * Time: 14:19
 */
class User extends Platform {

    public function index()
    {
        $input['nick'] =input('ipt_nick','','trim');
        $input['name'] =input('post.ipt_name','','trim');
        $input['tel'] =input('post.ipt_tel','','trim');
        $input['provinceId'] =input('post.ipt_provinceId','','intval');
        $input['cityId'] =input('post.ipt_cityId','','intval');
        $input['pageSize'] =input('post.pageSize','30','trim');
        $input['pageCurrent'] =input('post.pageCurrent','1','intval');
        $input['orderField'] =input('post.orderField','up_create_time','trim');
        $input['orderDirection'] =input('post.orderDirection','desc','trim');
        $input['beginDate'] =input('post.beginDate','','trim');
        $input['endDate'] =input('post.endDate','','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');
        $user =new \app\platform\model\User();
        $data =$user->findUserList($input);

        $areaModel = new Area();
        $area =$areaModel->findListByParentId(0);
        $this->assign('area',$area);
        $this->assign('list',$data['list']);
        $this->assign('count',$data['count']);
        $this->assign('input',$input);
        return $this->fetch();
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    public function edit()
    {

        if (Request::instance()->isPost()){
            $rid =input('post.rid','','intval');
            $roleid =input('post.roleid','0','intval');
        //    $agent_level =input('post.agent_level','0','intval');
            $userlevel =input('post.userlevel','0','intval');

            $res =Db::name('user_platform')->where(['up_id'=>$rid])->update([
                'up_user_level_id'=>$userlevel,
        //        'up_user_agent_level'=>$agent_level,
                'up_roleid'=>$roleid
            ]);

            if ($res){
                $this->ajaxReturn(ajaxCallBack(200,'修改成功!',true,'platform_User_index'));
            }else{
                $this->ajaxReturn(ajaxCallBack(300,'修改失败,请重试!'));
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

            $channel =new \app\common\model\Channel();
            $chanels =$channel->getChannelList("c_id,c_name",['c_projectId'=>$data['up_plateform_id'],'c_isDelete'=>0]);

            $userlevel =new \app\platform\model\UserLevel();
            $userlevels = $userlevel->findlistByPlatformId($data['up_plateform_id']);

            $this->assign('userlevels',$userlevels);
            $this->assign('chanels',$chanels);
            $this->assign('roles',$roles);
            $this->assign('data',$data);
            return $this->fetch();
        }

    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }

    /**
     * 冻结解冻
     */
    public function changeopen(){
        if(Request::instance()->isPost()){
            $id =Request::instance()->param('id','0','intval');
            $platfromid =Session::get('user.platformId');
            $res =Db::name('user_platform')->where(['up_plateform_id'=>$platfromid,'up_id'=>$id])->update(['up_states'=>1]);
            if ($res){
                return $this->ajaxReturn(ajaxCallBack(200,'操作成功！'));
            }else{
                return $this->ajaxReturn(ajaxCallBack(300,'操作失败!'));
            }
        }
        return $this->fetch();

    }

    /**
     * 冻结账户
     * @return mixed|void
     */
    public function changestop(){

        if (Request::instance()->isPost()){
            $platfromid =Session::get('user.platformId');
            $id =Request::instance()->param('id','0','intval');
            $reason =Request::instance()->param('reason','','trim');
            Db::startTrans();
            try{
                $res =Db::name('user_stoplog')->insert([
                    'us_platformId'=>$platfromid,
                    'us_uid'=>$id,
                    'us_create_uid'=>Session::get('user.userId'),
                    'us_reason'=>$reason,
                    'us_create_time'=>time()
                ]);
                if (!$res){
                    throw new Exception('冻结日志记录错误');
                }
                $res =Db::name('user_platform')->where(['up_id'=>$id])->update(['up_states'=>0]);
                if (!$res){
                    throw new Exception('账号冻结失败!');
                }
                Db::commit();
                $this->ajaxReturn(ajaxCallBack('200','操作成功!',true,'platform_User_index'));
            }catch (\Exception $e){
                Db::rollback();
                Logs::writeMongodb(300000,'db_user_platform',$id,'账号冻结解冻失败',$e->getMessage());
                $this->ajaxReturn(ajaxCallBack('300','操作失败!'));
            }
        }else{
            $id =Request::instance()->param('id','0','intval');
            if (empty($id)){
                return $this->ajaxReturn(ajaxCallBack('300','参数错误，请刷新后重试!'));
            }

            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    /**
     * 关系树
     */
    public function relational(){

        $uid =Request::instance()->param('id','0','intval');
        $platfromid =Request::instance()->param('p','0','intval');
        $user =new \app\platform\model\User();
        $data = $user->findrelationalByPlatFromIdAndUid($uid,$platfromid);
        if (!$data){
            $this->ajaxReturn(ajaxCallBack(300,'该用户没有在当前平台激活!',true));
        }
        $this->assign('json_priv',json_encode($data,JSON_UNESCAPED_UNICODE));
        return $this->fetch();
    }

    /**
     * 查看用户手机号
     */
    public function lookmobile(){

    }

    public function userprofile(){

        $platformId=Session::get('user.platformId');
        $username=Session::get('user.username');
        $userid =Session::get('user.userId');
        if (empty($userid)){
            return $this->ajaxReturn(ajaxCallBack('300','登录超时，请重新登录！',true,'','','',url('loign/index')));
        }

        $userplatform =new UserPlatform();
        $logindata = $userplatform->findDetailByUid($userid);
        if (empty($logindata)){
            $this->ajaxReturn(ajaxCallBack(300,'该用户信息不存在!'));
        }

        $logindata['u_nick'] =$username;
        $this->assign('data',$logindata);
        return $this->fetch();
    }

    public function grantlogin(){

        $uid=Request::instance()->param('id','0','intval');
    //    $pid =Request::instance()->param('pid','0','intval');

        if (empty($uid)){
            $this->ajaxReturn(ajaxCallBack(300,'参数错误!'));
        }

        $userplatform =new UserPlatform();
        $logindata = $userplatform->findDetailByUid($uid);

        if (empty($logindata)){
            $this->ajaxReturn(ajaxCallBack(300,'该用户信息不存在!'));
        }

        session(null); 	// 清空当前的session
        cookie(null);

        $logininfo =[
            'username'=>$logindata['u_nick']."(授权中)",
            'projectId'=>$logindata['up_plateform_id'],
            'userId'=>$logindata['up_id'],
            'platformId'=>$logindata['up_plateform_id'],
            'roleid'=>$logindata['up_roleid'],
            'dttxId'=>$logindata['up_dttx_uid'],
            'code'=>$logindata['u_code'],
            'fcode'=>$logindata['up_fcode'],
            'isActive'=>$logindata['up_isActive'],
            'hasPower'=>0,
        ];

        Session::set('user',$logininfo);
    //    $this->redirect('platform/index');
        $this->ajaxReturn(ajaxCallBack(200,'切换['.$logindata['u_nick'].']登录成功，点击确定后跳转!',true,'','','',url('/platform/index')));

    }


}