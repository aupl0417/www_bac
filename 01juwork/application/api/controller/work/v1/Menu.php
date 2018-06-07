<?php
namespace app\api\controller\work\v1;
use app\api\controller\work\v1\Init;
class Menu extends Init
{
    /**
     * 菜单列表
     * 2017-06-05
     */
    public function menu($check=1){
        if($check == 1) {
            $res = $this->check('openid',false);
            if($res['code'] != 1) return $this->ret($res);
        }

        $group = db('admin_group')->where(['id' => $this->admin['group_id']])->field('menu_id')->find();
        //dump($group);
        //获取三级菜单
        $menu = db('menu')->where(['status' => 1,'upid' => 0,'id' => ['in' , $group['menu_id']]])->field('atime,etime,ip',true)->order('sort asc,id asc')->select();
        //dump($menu);
        foreach($menu as &$val){
            $val['dlist']   = db('menu')->where(['status' => 1,'upid' => $val['id'],'id' => ['in' , $group['menu_id']]])->field('atime,etime,ip',true)->order('sort asc,id asc')->select();
            foreach($val['dlist'] as &$v){
                $v['dlist']   = db('menu')->where(['status' => 1,'upid' => $v['id'],'id' => ['in' , $group['menu_id']]])->field('atime,etime,ip',true)->order('sort asc,id asc')->select();
            }
        }
        return $this->ret(['code' => 1,'data' => $menu]);
    }
}
