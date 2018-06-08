<?php
namespace app\common\controller;

use think\Cache;
use think\Request;
use think\Session;

/**
 *  WAP版继承父类
 * User: lirong
 * Date: 2017/6/25
 * Time: 13:05
 */
abstract class Wap extends Common {

    protected $plafromdata =null;

    public function _initialize(){
        parent::_initialize();
        //免验证模块
        $params =[
            'allow_url'=>[
                'login/index','login/checklogin','goods/detail','login/logout','online/orderpayment', 'order/docreate'
            ]
        ];
        $request = Request::instance();
        $controller = $request->controller();
        $action = $request->action();
        $curl_url=$controller.'/'.$action;
        if (!in_array(strtolower($curl_url),$params['allow_url'])){
            if (!Session::has('user')){
                if (Request::instance()->isAjax()){
                    $this->assign(ajaxCallBack(505,'请登录后操作！'));
                }else{
                    $pre_url =$_SERVER['REQUEST_URI'];
                    Session::set('pre_url',$pre_url);
                    $this->redirect('login/index');
                }
            }else{
                if (strtolower($curl_url)!=='login/active'){
                    if (Session::get('user.isActive')==0){
                        $this->redirect('login/active');
                    }
                }
                $platfromId = Session::get('user.platformId');
                $cacheId ='platform_'.$platfromId;
                if (!($this->plafromdata = Cache::get($cacheId))){
                    $platfrom = new \app\admin\model\Platform();
                    $this->plafromdata = $platfrom->findDetailByid($platfromId);
                    Cache::set($cacheId,$this->plafromdata, 10);
                }
                $this->assign('platformdata', $this->plafromdata);
            }
        }
    }

}