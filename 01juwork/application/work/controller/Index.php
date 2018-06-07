<?php
namespace app\work\controller;
use app\work\controller\Common;
class Index extends Common
{
    public function index()
    {
        //config('api_debug',true);
        $res = api('Menu/menu',['openid' => session('admin.openid')]);
        $this->assign('menu',$res['data']);

        return view();
    }

    public function main(){
        //dump(session('admin'));
        //config('api_debug',true);
        //$res = curl_post('http://api.01ju.com/front.Channel/channel',['category_id' => 100330606]);
        //$res = curl_get('http://api.01ju.com/front.Example/example/category_id/100330606/callback/test');
		//$res = curl_post('http://api.01ju.com/front.Example/example',['category_id' => 100330606]);
		//$res = curl_post('http://api.01ju.com/front.Bottommenu/get_menu',['category_id' => 2]);
		//$res = curl_post('http://api.01ju.com/front.Details/get_details',['category_id' => 973]);
        //dump($res);
        return view();
    }

    public function noPower(){
        return view();
    }

    public function noPowerAjax(){
        return ['code' => 0,'msg' => '没有权限！'];
    }
}
