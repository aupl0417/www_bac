<?php
/**
 * 此文件由表单生成器创建
 * day:{day}
 */
namespace app\work\controller;
use app\work\controller\Common;
class Password extends Common
{
    public function index(){

        return view();
    }

    public function changePassword(){
        $msg = $this->validate($this->post,'Admin.password');
        if(true !== $msg) return ['code' => 0,'msg' => $msg];

        $data['openid']         = session('admin.openid');
        $data['old_password']   = md5($this->post['old_password']);
        $data['password']       = md5($this->post['password']);
        $res = api('Password/changePassword',$data);
        return $res;
    }

}
