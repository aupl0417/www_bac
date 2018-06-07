<?php
namespace app\wx\controller;

use think\Controller;

class Index extends Home
{

    public function index1(){
        $redirect_url = 'http://tk.mweisky.cn/01ju/public/index.php/wx/Index/test';
        $url = $this->oauth2($redirect_url);
        header("Location:" . $url);
//        $action = action('Home/oauth2', ['redirect_url' => 'http://tk.mweisky.cn/01ju/public/index.php/wx/Index/test']);
    }

    public function test(){
        $code      = input('code', '', 'htmlspecialchars');
        $tokenInfo = action('Home/getAccessTokenByCode', ['code' => $code]);
        $userInfo  = $this->getUser($tokenInfo['access_token'], $tokenInfo['openid']);
        dump($userInfo);
    }

}
