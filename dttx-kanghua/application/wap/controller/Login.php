<?php
namespace app\wap\controller;
use app\admin\model\Platform;
use app\common\controller\Wap;
use app\common\model\UserPlatform;
use app\common\tools\Logs;
use app\platform\model\Area;
use think\Db;
use think\Request;
use think\Session;
use think\Cache;

/**
 *
 * User: lirong
 * Date: 2017/7/7
 * Time: 20:51
 */
class Login extends Wap {


    public function index(){
        if(session('?user')){//如果已经登录，则直接跳转
//            $this->redirect(Cache::get(md5(Session::get('user.userId') . '_callback')));
            $this->redirect('wap/ucenter/index');
        }
        $platform =new Platform();
        $data =$platform->findAllPlatform();
        if (!empty($data)){
            $this->assign('data',$data[0]);
            $this->assign('title', $data[0]['pl_name']);
        }
        $fcode =Request::instance()->param('code','','intval');
        if (!empty($fcode)){
            Session::set('url_fcode',$fcode);
        }
        return $this->fetch();
    }

    public function register(){

    }

    public function active(){

        if (Request::instance()->isPost()){
            $input['up_provinceId'] =input('post.provinceId','','intval');
            $input['up_cityId'] =input('post.cityId','','intval');
            $input['up_regionId'] =input('post.regionId','','intval');
            $input['up_fcode'] =input('post.fcode','','trim');
            $url =!empty(Session::has('pre_url'))?Session::pull('pre_url'):url('ucenter/index');
            $result =$this->validate($input,'ActiveUserValidate');
            if (true!==$result){
                $this->ajaxReturn(ajaxCallBack(300,$result));
            }
            $id =Session::get('user.userId');
            $platfromId =Session::get('user.platformId');
            $active =Db::name('user_platform')->where(['up_id'=>$id])->find();
            if ($active['up_isActive']==1){
                $this->redirect($url);
            }
            if (!empty($input['up_fcode'])){
                $user =Db::name('user')->alias('u')->field('u_code,up_isActive')->join('user_platform up','u.u_id=up.up_uid','left')->where(['up_plateform_id'=>$platfromId])->where('u_nick|u_tel','=',$input['up_fcode'])->find();
                if (!empty($user)){
                    if ($user['up_isActive']!=1){
                        $this->ajaxReturn(ajaxCallBack(300,'该推荐人尚未激活，请确认!'));
                    }
                    $input['up_fcode'] =$user['u_code'];
                }else{
                    $this->ajaxReturn(ajaxCallBack(300,'推荐人不存在，请确认!'));
                }
            }else{
                //如果没有指定推荐人则归属项目管理人

                $platform = Db::name('platform')->alias('p')->where(['pl_id'=>$platfromId])->find();
                if (!empty($platform)){
                    $input['up_fcode']= $platform['pl_dttx_code'];
                }
            }

            $input['up_isActive']=1;
            $res =Db::name('user_platform')->where(['up_id'=>$id])->update($input);
            if ($res){
                $code =Session::get('user.code');
                session('url_fcode', $code);
                Session::set('user.isActive',1);
                $this->ajaxReturn(ajaxCallBack(200,'保存成功!',$url));
            }else{
                $this->ajaxReturn(ajaxCallBack(300,'保存失败，请重试!'));
            }

        }else{
            if (!Session::has('user')){
                $url =!empty(Session::has('pre_url'))?Session::pull('pre_url'):url('ucenter/index');
                $this->redirect($url);
            }
            $userid =Session::get('user.userId');
            $userPlatform =new UserPlatform();
            $res = $userPlatform->findUserPlatformInfoByUpid($userid);

            if ($res['up_isActive']==1){
                $url =!empty(Session::has('pre_url'))?Session::pull('pre_url'):url('ucenter/index');
                $this->redirect($url);
            }

            if (Session::has('url_fcode')){
                $code =Session::get('url_fcode');
                $user =new \app\common\model\User();
                $url_code =$user->getUserinfoByCode($code,'u_nick,u_name,u_tel');
                $uname ="*".mb_substr($url_code['u_name'],1, mb_strlen($url_code['u_name'], 'utf-8'), 'utf-8');
                $this->assign('url_info','姓名:'.$uname.',电话:'.hidtel($url_code['u_tel']));
                $this->assign('url_nick',$url_code['u_nick']);
            }

            $areaModel = new Area();
            $area =$areaModel->findListByParentId(0);
            $this->assign('area', $area);
            $this->assign('userid',$userid);

            $this->assign('title','登录激活');
            return $this->fetch();
        }

    }


    public function checklogin(){
        if (Request::instance()->isPost()){
            $input['username'] =input('post.username','','trim');
            $input['password'] =input('post.password','','trim');
            $platformId =input('post.platformId','','intval');

//            $user =new \app\platform\model\User();
//            $logindata = $user->findUserByNickAndPlatFormid($input['username'],$platformId);
//            if (empty($logindata)){
//                $this->ajaxReturn(ajaxCallBack(300,'该用户不存在，请确认是否激活本项目!'));
//            }
//            if ($logindata['up_states']==0){
//                $this->ajaxReturn(ajaxCallBack(300,'该用户已被禁止登录，请联系项目管理员！'));
//            }

            $dttxdata =get_dttxLoginInfo($input['username'],$input['password']);
            if (empty($dttxdata)){
                Logs::writeMongodb(500000,'dttx_user',$input['username'],'账户接口登录超时',$input['username'],'Ym','fenxiao_user');
                $this->ajaxReturn(ajaxCallBack(300,'验证超时，请重试!'));
            }
            if ($dttxdata['id']=='2199'){
                $this->ajaxReturn(ajaxCallBack(300,$dttxdata['info']));
            }else{
                $dttxdata=$dttxdata['info'];
                if ($dttxdata['u_state']!=1){
                    $this->ajaxReturn(ajaxCallBack(300,'该用户已被大唐天下禁用，请更换账号！'));
                }

                $userplatform =new \app\platform\model\User();
                $usercommon =new \app\common\model\User();
                $logindata = $userplatform->findUserByNickAndPlatFormid($input['username'],$platformId);

                if (empty($logindata)){
                    $usermodel =new \app\common\model\User();
                    $res =$usermodel->activeDttxUser($dttxdata,$platformId,0);
                    if ($res['statusCode']!=200){
                        Logs::writeMongodb(500000,'dttx_user',$input['username'],"激活新用户失败",$res,'Ym');
                        $this->ajaxReturn(ajaxCallBack(300,'账号登录失败，请重新登录!'));
                    }
                    $logindata = $userplatform->findUserByNickAndPlatFormid($input['username'],$platformId);
                }else{
                    $usercommon->synchronizeDttxInfo($dttxdata,$logindata);
                    if ($logindata['up_states']==0){
                        $this->ajaxReturn(ajaxCallBack(300,'该账号已被系统禁用，请更换！'));
                    }
                }
                $logininfo =[
                    'username'=>$logindata['u_nick'],
                    'projectId'=>$logindata['up_plateform_id'],
                    'userId'=>$logindata['up_id'],
                    'platformId'=>$logindata['up_plateform_id'],
                    'roleid'=>$logindata['up_roleid'],
                    'code'=>$logindata['u_code'],
                    'tel'=>$logindata['u_tel'],
                    'fcode'=>$logindata['up_fcode'],
                    'dttxId'=>$logindata['up_dttx_uid'],
                    'isActive'=>$logindata['up_isActive'],
                ];
                $usercommon->updateLastLoginInState($logindata['u_id']);
                Logs::writeMongodb(500001,'db_user_platform',$input['username'],'WAP用户登录成功',$logininfo,'Ym');
                session('user', $logininfo);
                if ($logindata['up_isActive']){
                    session('url_fcode', $logininfo['code']);
                    $url= Session::has('buy_goods_jump_url') ? Session::pull('buy_goods_jump_url') : (Session::has('pre_url')?Session::pull('pre_url'):url('ucenter/index'));
                }else{
                    $url=url('login/active');
                }
                $this->ajaxReturn(ajaxCallBack(200,$url));
            }
        }
    }

    /*
     * 退出登录
     * */
    public function logout(){
        if((session('?user'))){
            session('user', null);
            session(null);
        }
        $this->redirect('login/index');
    }



}