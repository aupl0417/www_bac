<?php
namespace app\platform\controller;
use app\common\controller\Platform;
use think\Db;
use think\Request;
use think\Session;

/**
 *
 * User: lirong
 * Date: 2017/7/4
 * Time: 16:02
 */
class Userrole extends Platform{

    /**
     * 用于首页数据展示
     * @return mixed
     */
    public function index()
    {
        $input['pageSize']=input('post.pageSize','30','intval');
        $input['pageCurrent']=input('post.pageCurrent','1','intval');

        $input['orderField']=input('post.orderField','ur_roleid','trim');
        $input['orderDirection']=input('post.orderDirection','asc','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');
        $input['rolename'] =input('post.rolename',"",'trim');

        $userrole =new \app\platform\model\UserRole();
        $data = $userrole->findUserRoleList($input);
        $this->assign('input',$input);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 增加数据
     * @return mixed
     */
    public function create()
    {
        if (Request::instance()->isPost()){
            $input['ur_rolename'] =  input('post.rolename','','trim');
            $input['ur_description'] =  input('post.description','','trim');


            $result =$this->validate($input,'UserRoleValidate');
            if (true !==$result){
                return $this->ajaxReturn(ajaxCallBack(300,$result));
            }
            $input['ur_platform_id'] =  input('post.platformId','','intval');
            if (empty($input['ur_platform_id'])){
                $input['ur_platform_id'] =Session::get('user.platformId');
            }

            $res = Db::name('user_role')->insert($input);

            if ($res){
                return $this->ajaxReturn(ajaxCallBack(200,'添加角色成功!',true,'platform_Userrole_index'));
            }else{
                return $this->ajaxReturn(ajaxCallBack(300,'添加角色失败,请重试!'));
            }

        }else{
            return $this->fetch();
        }
    }

    /**
     *修改数据
     * @return mixed
     */
    public function edit()
    {
        if (Request::instance()->isPost()){
            $input['ur_roleid']= input('post.roleid','0','intval');
            $input['ur_rolename'] =  input('post.rolename','','trim');
            $input['ur_description'] =  input('post.description','','trim');

            $result =$this->validate($input,'UserRoleValidate');
            if (true !==$result){
                return $this->ajaxReturn(ajaxCallBack(300,$result));
            }
            $res = Db::name('user_role')->update($input);

            if ($res){
                return $this->ajaxReturn(ajaxCallBack(200,'修改角色成功!',true,'platform_Userrole_index'));
            }else{
                return $this->ajaxReturn(ajaxCallBack(300,'修改角色失败,请重试!'));
            }

        }else{
            $id =Request::instance()->param('id','0','intval');
            $userRole =new \app\platform\model\UserRole();
            $data =$userRole->findRoleByid($id);
            print_r($data);
            $this->assign('data',$data);
            return $this->fetch();
        }


    }

    /**
     * 移除数据
     * @return mixed
     */
    public function remove(){
        $id =Request::instance()->param('id','0','intval');
        if (empty($id)){
            $this->ajaxReturn(ajaxCallBack(300,'参数错误，请刷新后重试!'));
        }
        $userRole =new \app\platform\model\UserRole();
        $ur_platform_id =Session::get('user.platformId');
        $res =$userRole->removeRoleByid($id,$ur_platform_id);
        $this->ajaxReturn(ajaxCallBack($res['code'],$res['message']));

    }

    /**
     * 修改状态
     */
    public function change(){
        $id =Request::instance()->param('id','0','intval');
        $state =Request::instance()->param('state','0','intval');

        $userRole = new \app\platform\model\UserRole();
        $res =$userRole->changeStatus($id,$state);
        if ($res){
            $this->ajaxReturn(ajaxCallBack(200,'操作成功！'));
        }else{
            $this->ajaxReturn(ajaxCallBack(300,'操作失败，请重试！'));
        }

    }

    /**
     * 角色权限设置
     */
    public function rolesetting($roleid){

            $array_menu_res = db('admin_menu')->where(['outside'=>1])->order('listorder, id')->select();
            if($array_menu_res){
                foreach ($array_menu_res as $value) {
                    $array_menu[$value['id']] = $value;
                }
            }
            if(Request::instance()->isPost()){
                //删除旧权限
                db('user_role_priv')->where('urp_roleid', $roleid)->delete();
                $ids = input('post.ids');
                $data['urp_roleid'] = $roleid;
                if($ids){
                    $ids = explode(',', $ids);
                    foreach ($ids as $menu_id){
                        //取出id的设置
                        $detail = $array_menu[$menu_id];
                        $data['urp_menuid'] = $detail['id'];
                        $data['urp_module'] = strtolower($detail['module']);
                        $data['urp_controller'] = strtolower($detail['controller']);
                        $data['urp_action'] = strtolower($detail['action']);
                        db('user_role_priv')->insert($data);
                    }
                }
                //这里用html里写的js来post， 结果也是在js里写的。
                //	$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','message'=> 'haha', 'tabid'=>'System_adminRoleList'));
            }else{


                //$menus = list_to_tree($array_menu, 'id' ,'parentid', 'children');
                $map['urp_roleid'] = $roleid;
                foreach ($array_menu as $menu_id => $menu){
                    unset($menu['icon']);
                    $json_priv[$menu_id] = $menu;
                    //判断该权限是否拥有
                    $map['urp_menuid'] = $menu['id'];
                    $exist_priv = db('user_role_priv')->where($map)->find();
                    if($exist_priv){
                        $json_priv[$menu_id]['checked'] = true;
                    }
                }
                $menus = list_to_tree($json_priv, 'id' ,'parentid', 'children');
                $this->assign('json_priv', json_encode($menus));
                $this->assign('roleid', $roleid);

                return $this->fetch();
            }
        }

}