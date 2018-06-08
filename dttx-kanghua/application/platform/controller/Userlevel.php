<?php
namespace app\platform\controller;
use app\common\controller\Platform;
use think\Db;
use think\Request;


/**
 * 会员管理
 * User: lirong
 * Date: 2017/6/28
 * Time: 13:48
 */
class Userlevel extends Platform{

    public function index(){

        $input['pageSize']=input('post.pageSize','30','intval');
        $input['pageCurrent']=input('post.pageCurrent',1,'intval');
        $input['orderField']=input('post.orderField','ul_id','trim');
        $input['orderDirection'] =input('post.orderDirection','asc','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');
        $input['name']=input('post.name','','trim');

        $userLevel =new \app\platform\model\UserLevel();
        $data =$userLevel->findUserLevelList($input);
        $this->assign('data',$data);
        $this->assign('input',$input);
        return $this->fetch();
    }


    public function create()
    {
        if (Request::instance()->isPost()){
            $input['ul_user_no']=input('post.userNo','','trim');
            $input['ul_name']=input('post.name','','trim');
            $input['ul_ratio']=input('post.ratio','','trim');
            $input['ul_upgrade_require']=input('post.upgrade','','trim');
            $input['ul_level_mark']=input('post.mark','','trim');
            $input['ul_status']=input('post.status','','trim');
            $input['ul_money'] = input('post.price','','floatval');

            $result = $this->validate($input,'UserLevelValidate');
            if(true !== $result){
                // 验证失败 输出错误信息
                $this->ajaxReturn(ajaxCallBack(300,$result));
            }
            $input['ul_platform_id'] =session('user.platformId');

            $res =Db::name('user_level')->insert($input);

            if ($res){
                $this->ajaxReturn(ajaxCallBack(200,'添加会员等级成功!',true, 'platform_Userlevel_index'));
            }else{
                $this->ajaxReturn(ajaxCallBack(300,'添加会员等级失败，请重试!'));
            }

        }else{

            return $this->fetch();

        }

    }

    public function edit()
    {
        $userlevel =new \app\platform\model\UserLevel();
        if(Request::instance()->isPost()){

            $input['ul_user_no']=input('post.userNo','','trim');
            $input['ul_name']=input('post.name','','trim');
            $input['ul_ratio']=input('post.ratio','','trim');
            $input['ul_upgrade_require']=input('post.upgrade','','trim');
            $input['ul_level_mark']=input('post.mark','','trim');
            $input['ul_status']=input('post.status','','trim');
            $input['ul_money']=input('post.price','','floatval');
            $input['ul_id'] =input('post.ul_id','0','trim');
            $result = $this->validate($input,'UserLevelValidate');
            if(true !== $result){
                // 验证失败 输出错误信息
                $this->ajaxReturn(ajaxCallBack(300,$result));
            }

            $res =Db::name('user_level')->update($input);

            if ($res === false){
                $this->ajaxReturn(ajaxCallBack(300,'修改会员等级失败，请重试!'));
            }
            $this->ajaxReturn(ajaxCallBack(200,'修改会员等级成功!',true,'platform_Userlevel_index'));

        }else{
            $id= Request::instance()->param('id','0','intval');
            if (empty($id)){
                $this->ajaxReturn(ajaxCallBack(300,'参数错误，请刷新页面后重试!'));
            }
            $data =$userlevel->findDetaliByid($id);
            if (empty($data)){
                $this->ajaxReturn(ajaxCallBack(300,'该记录不存在或已被删除，请刷新后重试!'));
            }
            $this->assign('data',$data);
            return $this->fetch();
        }

    }

    public function remove(){
        $id= Request::instance()->param('id','0','intval');
        if (empty($id)){
            $this->ajaxReturn(ajaxCallBack(300,'参数错误，请刷新页面后重试!'));
        }

        $userlevel =new \app\platform\model\UserLevel();

        $res =$userlevel->removeByid($id);
        if ($res){
            $this->ajaxReturn(ajaxCallBack(200,'删除会员等级成功!'));
        }else{
            $this->ajaxReturn(ajaxCallBack(300,'删除会员等级失败，请重试!'));
        }
    }

    public function read(){
        $id= Request::instance()->param('id','0','intval');
        if (empty($id)){
            $this->ajaxReturn(ajaxCallBack(300,'参数错误，请刷新页面后重试!'));
        }
        $userlevel =new \app\platform\model\UserLevel();
        $data =$userlevel->findDetaliByid($id);
        if (empty($data)){
            $this->ajaxReturn(ajaxCallBack(300,'该记录不存在或已被删除，请刷新后重试!'));
        }
        $this->assign('data',$data);
        return $this->fetch();

    }
}

