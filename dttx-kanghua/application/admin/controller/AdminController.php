<?php

namespace app\admin\controller;

use think\Controller;
use think\Hook;
use think\Request;


class AdminController extends Controller
{
    public function _initialize(){

        //移植TP3.2的定义
        define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
        define('IS_PUT',        REQUEST_METHOD =='PUT' ? true : false);
        define('IS_DELETE',     REQUEST_METHOD =='DELETE' ? true : false);
        self::checkAdmin();
        // self::checkLang();
        $action = array(
        'permission'=>array('profile', 'changePassword', 'ajax_checkUsername'),
            //'allow'=>array('index')
        );

        $request =Request::instance();
        $controller =$request->controller();
        $novalidate=config('novaildate_controller');
        if (!in_array(strtolower($controller),$novalidate)){
            Hook::exec('app\\admin\\behavior\\Authenticate', 'run', $action);
        }
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
     * 判断用户是否已经登陆
     */
    public function checkAdmin() {
        $request = Request::instance();
        $controller_name = $request->controller();

        //登录界面不判断, Publics控制，不判断
        if($controller_name =='Publics' || $controller_name =='Cron') {
            return true;
        } else {
            $userid = cookie('userid');
            //没有相关session则跳转到登录页
            if(!session('userid') || !session('roleid') || $userid != session('userid')){
                $this->redirect('Publics/login');
            }
        }
    }
    
    /**
     * 语言设置
     */
    public function checkLang(){
        //设置默认语言
        if(!cookie('think_language')){
            //cookie('think_language', 'en-us');
            cookie('think_language', 'zh-cn');
        }
        B('Behavior\\CheckLang');
    }
}
