<?php
namespace app\common\controller;
use think\Config;
use think\Hook;
use think\Request;
use think\Session;

/**
 * 平台管理员继承父类
 * User: lirong
 * Date: 2017/6/25
 * Time: 13:02
 */
abstract class Platform extends Common{

    protected $platformId=0; //默认平台id


    public function _initialize()
    {
        parent::_initialize();

            if ((!Session::has('user') || Session::get('user.roleid')==0) && !Session::has('admin_userid')){
                if (Request::instance()->isAjax()){
                    return $this->ajaxReturn(ajaxCallBack(300,'登录超时或您没有权限访问该页面!'));
                }else{
                    return $this->error("登录超时或您没有权限访问该页面!",url('platform/login/index'));
                }
            }

            $controller =Request::instance()->controller();
            //后台用户检查
            if (Session::has('admin_userid')){
                $action = array(
                    'permission'=>array('profile', 'changepassword', 'ajax_checkusername'),
                    //'allow'=>array('index')
                );
                $novalidate=['publics','admincp','common'];
                if (!in_array(strtolower($controller),$novalidate)){
                    Hook::exec('app\\common\\behavior\\CheckAdminAuth', 'run', $action);
                }
            }else{
                //平台用户检测
                $novalidate_platform_controller =['index'];
                $action =['permission'=>array('userprofile','ajax_area')];
                if (!in_array(strtolower($controller),$novalidate_platform_controller)){
                    Hook::exec('app\\common\\behavior\\CheckPlatformAuth','run',$action);
                }
            }

            if(Session::has("admin_userid")){
                $this->assign('admin_state',true);
                $platformdata =$this->platformlist();
                $this->assign('platformdata',$platformdata);
            }else{
                $this->assign('admin_state',false);
                $this->platformid =Session::get('user.platformId');
            }

    }

    /**
     * 用于首页数据展示
     * @return mixed
     */
    public abstract function index();

    /**
     * 增加数据
     * @return mixed
     */
    public abstract function create();

    /**
     *修改数据
     * @return mixed
     */
    public abstract function edit();

    /**
     * 移除数据
     * @return mixed
     */
    public abstract function remove();

    



}