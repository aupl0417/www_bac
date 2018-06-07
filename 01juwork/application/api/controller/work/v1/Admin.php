<?php
namespace app\api\controller\work\v1;
use app\api\controller\work\v1\Init;
class Admin extends Init
{
    /**
     * 雇员登录
     * 2017-06-01
     */
    public function login($check=1){
        if($check == 1) {
            $res = $this->check('username,password');
            if($res['code'] != 1) return $this->ret($res);
        }

        $where = [
            'username'  => strtolower($this->post['username']),
            'password'  => $this->post['password'],
        ];
        $rs = db('admin')->where($where)->field('atime,etime,appid',true)->find();
        if($rs){
            //取权限
            $power = db('admin_group')->where(['id' => $rs['group_id'],'status' => 1])->field('menu_id,action')->find();
            if(!$power) return $this->ret(['code' => 0,'msg' => '该用户无权限！']);

            $power['action'] = json_decode(strtolower(html_entity_decode($power['action'])),true);
            $rs['power'] = $power;

            if($rs['status'] != 1) return $this->ret(['code' => 0,'msg' => '账号已被暂停使用！']);
            $sql = 'update '.config('database.prefix').'admin set loginum=loginum+1,lastlogintime=now(),lastloginip="'.$this->request->ip().'" where id='.$rs['id'];
            db()->execute($sql);
            //session('admin',$rs);

            //获取权限
            //toDo

            return $this->ret(['code' => 1,'data' => $rs,'msg' => '登录成功！']);
        }

        return $this->ret(['code' => 0,'msg' => '账号或密码错误！']);
    }
}
