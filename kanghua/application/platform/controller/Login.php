<?php
namespace app\platform\controller;
use app\common\controller\Common;
use think\captcha\Captcha;
use think\Cookie;
use think\Db;
use think\Request;

/**
 *
 * User: lirong
 * Date: 2017/7/5
 * Time: 13:37
 */
class Login extends Common {

    /**
     * 用于首页数据展示
     * @return mixed
     */
    public function index(){
        $platform =new \app\admin\model\Platform();
        $data = $platform->findAllPlatform();
        if (!empty($data)){
            $this->assign('platformId',$data[0]['pl_id']);
        }
        return $this->fetch();
    }
    /**
     * 大唐账号登录
     */
    public function checklogin(){
        if (Request::instance()->isPost()){
            $input['username'] =input('post.username','','trim');
            $input['password'] =input('post.password','','trim');
            $input['captcha'] =input('post.captcha','','trim');

            $result =$this->validate($input,'LoginValidate');
            if (true!==$result){
                return $this->ajaxReturn(ajaxCallBack(300,$result));
            }
            $ischecked =input('post.isChecked','0','intval');
            $captche =new Captcha();
            $res =$captche->check($input['captcha']);
            if (!$res){
                $this->ajaxReturn(ajaxCallBack(300,'验证码错误，重新输入!'));
            }

            $platformId =input('post.platformId','','intval');

            $user =new \app\platform\model\User();
            $logindata = $user->findUserByNickAndPlatFormid($input['username'],$platformId);
            if (empty($logindata)){
                $this->ajaxReturn(ajaxCallBack(300,'该用户不存在，请确认是否激活本项目!'));
            }
            if ($logindata['up_states']==0){
                $this->ajaxReturn(ajaxCallBack(300,'该用户已被禁止登录，请联系项目管理员！'));
            }
            if ($logindata['up_roleid']==0){
                $this->ajaxReturn(ajaxCallBack(300,'您没有权限访问该页面！'));
            }

            $dttxdata =get_dttxLoginInfo($input['username'],$input['password']);
            if (false===$dttxdata){
                $this->ajaxReturn(ajaxCallBack(300,'验证超时，请重试!'));
            }
            if ($dttxdata['id']=='2199'){
                $this->ajaxReturn(ajaxCallBack(300,$dttxdata['info']));
            }else{
                $dttxdata=$dttxdata['info'];
                if ($dttxdata['u_state']!=1){
                    $this->ajaxReturn(ajaxCallBack(300,'该用户已被禁止登录，请联系项目管理员！'));
                }
                $user =new \app\common\model\User();
                $user->synchronizeDttxInfo($dttxdata,$logindata);
                $logininfo =[
                    'username'=>$logindata['u_nick'],
                    'projectId'=>$logindata['up_plateform_id'],
                    'userId'=>$logindata['up_id'],
                    'platformId'=>$logindata['up_plateform_id'],
                    'roleid'=>$logindata['up_roleid'],
                    'dttxId'=>$logindata['up_dttx_uid'],
                    'code'=>$logindata['u_code'],
                    'fcode'=>$logindata['up_fcode'],
                    'isActive'=>$logindata['up_isActive'],
                    'hasPower'=>1  //是否有执行权限
                ];
                $user->updateLastLoginInState($logindata['u_id']);
                session('user', $logininfo);
//                if ($ischecked){
//                    $cookie_time = 86400*30;
//                    if ($ischecked){
//                        Cookie::set('loginToken',serialize($logininfo),$cookie_time);
//                    }
//                }

                $this->ajaxReturn(ajaxCallBack(200,'登录成功!'));
            }
        }
    }
























    /**
     * 增加数据
     * @return mixed
     */
    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     *修改数据
     * @return mixed
     */
    public function edit()
    {
        // TODO: Implement edit() method.
    }

    /**
     * 移除数据
     * @return mixed
     */
    public function remove()
    {
        session('user',null); 	// 清空当前的session
        cookie('user',null);
        $this->redirect('login/index');
    }
}