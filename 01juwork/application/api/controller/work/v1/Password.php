<?php
namespace app\api\controller\work\v1;
use app\api\controller\work\v1\Init;
class Password extends Init
{
    /**
     * 修改密码
     * 2017-06-16
     */
    public function changePassword($check=1){
        if($check == 1) {
            $res = $this->check('openid,old_password,password');
            if($res['code'] != 1) return $this->ret($res);
        }

        $admin = db('admin')->where(['openid' => $this->post['openid']])->field('id,status,password')->find();
        if($admin){
            if($admin['status'] != 1) return $this->ret(['code' => 0,'msg' => '雇员账号存在异常！']);
            if($admin['password'] !== $this->post['old_password']) return $this->ret(['code' => 0,'msg' => '旧密码错误！']);

            if(db('admin')->where(['openid' => $this->post['openid']])->update(['password' => $this->post['password']])) return $this->ret(['code' => 1,'msg' => '修改成功！']);
            return $this->ret(['code' => 0,'msg' => '修改失败！']);
        }
        return $this->ret(['code' => 0,'msg' => '雇员不存！']);
    }
}
