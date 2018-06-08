<?php
namespace app\platform\controller;
use app\common\controller\Platform;
use think\Session;


class Index extends Platform
{

    /**
     * 用于首页数据展示
     * @return mixed
     */
    public function index()
    {
        //获取菜单
        $map['display'] = 1;
        $map['outside']=1;

        //检查权限,如果是超级管理员 ，则显示所有菜单
        $roleid = session('user.roleid');
        if($roleid != 1){
            //取出权限中的menu_id
            $priv_list = db('user_role_priv')->where('urp_roleid='.$roleid)->field('urp_menuid as menuid')->select();
            $menu_ids = '';
            if($priv_list){
                foreach ($priv_list as $v){
                    $menu_ids .= $menu_ids ? ','.$v['menuid'] : $v['menuid'];
                }
                $map['id'] = array('in', $menu_ids);
            }else{
                $map['id']=array('in', $menu_ids);
            }
        }
        $result = db('admin_menu')->where($map)->order('listorder,id')->field('id,listorder,name,icon,module,controller,action,parentid')->select();
        //这里可以做缓存
        $menu=list_to_tree($result,'id','parentid','_child');

        $this->assign('menu', $menu);
        //$this->display();
        return $this->fetch();
    }

    public function welcome(){
        $roleId         = session('user.roleid');
        $userId         = session('user.userId');
        $platformId     = session('user.platformId');
        $orderCondition = array('os_status' => 1, 'os_platform_id' => $platformId);
        $agentCondition = array('a_state' => array('neq', 'blocked'), 'a_isDelete' => 0, 'a_projectId' => $platformId);
        $shopkeeperCondition = array('s_state' => 'pass', 's_isDelete' => 0, 's_projectId' => $platformId);
        $shopItemCondition = array('si_isSale' => 1, 'si_isDelete' => 0, 'si_projectId' => $platformId);
        $ordersAllCondition= array('os_platform_id' => $platformId);
//        $userId = 5;
//        $roleId = 7;
        if($roleId != 1){
            $orderCondition['os_seller_id']     = $userId;
            $agentCondition['a_createId']       = $userId;
            $shopkeeperCondition['s_createId']  = $userId;
            $shopItemCondition['si_createId']   = $userId;
            $ordersAllCondition['os_seller_id'] = $userId;
        }

        $data['orderCount']      = db('orders')->where($orderCondition)->count();
        $data['agentCount']      = db('agent')->where($agentCondition)->count();
        $data['shopkeeperCount'] = db('shopkeeper')->where($shopkeeperCondition)->count();
        $data['shopItemsCount']  = db('shop_items')->where($shopItemCondition)->count();
        $data['ordersAllCount']  = db('orders')->where($ordersAllCondition)->count();
        $this->assign('roleId',$roleId);
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 增加数据
     * @return mixed
     */
    public function create(){

    }

    public function qcode(){
        $platformId =Session::get('user.platformId');
        \QRcode::png(url('wap/store/index',['id'=>$platformId],'html',true),false,'L','6');
    }

    /**
     *修改数据
     * @return mixed
     */
    public function edit()
    {
    }

    /**
     * 移除数据
     * @return mixed
     */
    public function remove()
    {
    }
}
