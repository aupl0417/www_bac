<?php
namespace app\common\controller;
use think\Config;
use think\Controller;
use think\Hook;
use think\Request;
use think\Session;

/**
 * 通用控制类
 * Created by lirong
 * Date: 2017/6/24
 * Time: 18:01
 */
class Common extends Controller{

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->assign('version',"?v=".Config::get('update.version'));
    }


    protected function ajaxReturn($data,$type='',$json_option=0) {
        if(empty($type)) $type  =   'JSON';
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data,$json_option));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET[config('VAR_JSONP_HANDLER')]) ? $_GET[config('VAR_JSONP_HANDLER')] : config('DEFAULT_JSONP_HANDLER');
                exit($handler.'('.json_encode($data,$json_option).');');
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
            default     :
                // 用于扩展其他返回格式数据
                Hook::listen('ajax_return',$data);
        }
    }


    /**
     * 判断后台用户是否已经登陆
     */
    public function checkAdmin() {
        $request = Request::instance();
        $controller_name = $request->controller();

        //登录界面不判断, Publics控制，不判断
        if($controller_name =='Publics' || $controller_name =='Cron') {
            return true;
        } else {
            $userid = cookie('admin_userid');
            //没有相关session则跳转到登录页
            if(!session('admin_userid') || !session('admin_roleid') || $userid != session('admin_userid')){
                $this->redirect('Publics/login');
            }
        }
    }

    protected function platformlist(){
        $platform =new \app\admin\model\Platform();
        $res = $platform->findAllPlatform();
        return $res;
    }


}