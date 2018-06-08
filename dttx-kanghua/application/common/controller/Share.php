<?php
namespace app\common\controller;
use think\Request;
use think\Session;

/**
 *
 * Author: lirong
 * Date: 2017/7/25
 * Time: 16:57
 */
class Share extends Common{

    public function _initialize()
    {
        parent::_initialize();

        $request = Request::instance();
        $action = $request->action();
        $url_code = Request::instance()->param('code', '0', 'intval');
//        if (Session::has('user')){
//              $url_code =Session::get('user.code');
//        }

        if (empty($url_code)) {
            if (Session::has('url_fcode')) {
                $code = Session::get('url_fcode');
            }else{
                $code = Session::get('user.code');
            }
            if (!empty($code)) {
                if ($action == 'index') {
                    $this->redirect('store/index', ['code' => $code]);
                } else {
                    $this->redirect(trim($_SERVER['REQUEST_URI'],'.html').'/code/'.$code);
                }
            }
        }else{
            if (Session::has('user') && Session::get('isActive')==1){
                $code =Session::get('user.code');
                Session::set('url_fcode',$code);
            }
        }

    }
}