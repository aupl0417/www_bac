<?php
namespace app\common\behavior;
use think\Db;
use think\Request;
/**
 * 权限验证行为
 * User: lirong
 * Date: 2017/6/25
 * Time: 20:17
 */
class CheckAdminAuth{

    public function run(&$params){

        //检查会话是否超时
        //session(null);
        if(!session('admin_userid') || !session('admin_roleid') || cookie('admin_userid') != session('admin_userid')){
            $this->ajaxReturn(array('statusCode'=>301,'message'=>'会话超时！'));
        }
        $request = Request::instance();
        $module = $request->module();
        $controller = $request->controller();
        $action = $request->action();
        $allow = isset($params['allow']) ? $params['allow'] : array();
        $permission = isset($params['permission']) ? $params['permission'] : array();

        if (session('?admin_identify')) {
            return true;
        }

        if (in_array($action, $permission)) {
            return true;
        } elseif (session('?admin_roleid')) {
            if (in_array($action, $allow)) {
                return true;
            } else {
                $map['module'] = strtolower($module);
                $map['controller'] = strtolower($controller);
                $map['action'] = strtolower($action);
                $map['roleid'] = session('admin_roleid');

//                $priv = db('admin_role_priv')->where($map)->find();
                $priv =Db::name('admin_role_priv')->where($map)->find();

                if (is_array($priv) && !empty($priv)) {
                    return true;
                } else {
                    return $this->ajaxReturn(array('statusCode'=>300,'message'=>'您没有此权限！'));
                }
            }
        } else {
            //session过期
            return $this->ajaxReturn(array('statusCode'=>301,'message'=>'会话超时！'));
        }
        return true;
    }

    public function ajaxReturn($data){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }


}