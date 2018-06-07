<?php
namespace app\work\controller;
use app\work\controller\Init;

class Login extends Init
{
    public function index()
    {
        return view();
    }

    public function checkLogin(){
        $msg = $this->validate($this->post,'Login');
        if(true !== $msg) return ['code' => 0,'msg' => $msg];

        if(captcha_check($this->post['vcode']) !== true) return ['code' => 10,'msg' => '验证码错误！'];
        $this->post['password'] = md5($this->post['password']);

        //config('api_debug',true);
        $res = api('Admin/login',$this->post);
        if($res['code'] == 1){
            session('admin',$res['data']);
        }
        return $res;
    }

    public function logout(){
        session('admin',null);
        return redirect('/login');
    }

    /**
     * 接口参数
     * @return array
     */
    private function api_cfg()
    {
        $data = [
            'appid'         => 1,
            'access_key'    => '1f982aa4178c278c95529e28b0f1b20f',
            'secret_key'    => 'cfdb08305ddf31113eed7d6bd7c6ce94',
            'sign_code'     => 'a35bbf3cf3cb98f91bc748f4660127ee',
        ];
        return $data;
    }

    /**
     * 验证码
     */
    public function vcode(){
        return captcha_src();
    }
}
