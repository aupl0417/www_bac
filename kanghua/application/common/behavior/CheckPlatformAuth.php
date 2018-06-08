<?php
namespace app\common\behavior;
use think\Request;
/**
 * 权限验证行为
 * User: lirong
 * Date: 2017/6/25
 * Time: 20:17
 */
class CheckPlatformAuth{

    public function run(&$params){

        //检查会话是否超时
        //session(null);
        if(!session('user') || !session('user.roleid')){
            $this->ajaxReturn(array('statusCode'=>301,'message'=>'会话超时！'));
        }
        $request = Request::instance();
        $module = $request->module();
        $controller = $request->controller();
        $action = $request->action();
        $allow = isset($params['allow']) ? $params['allow'] : array();
        $permission = isset($params['permission']) ? $params['permission'] : array();

        if (session('user.roleid')==1) {
            return true;
        }

        if (in_array($action, $permission)) {
            return true;
        } elseif (session('?user.roleid')) {
            if (in_array($action, $allow)) {
                return true;
            } else {
                $map['urp_module'] = strtolower($module);
                $map['urp_controller'] = strtolower($controller);
                $map['urp_action'] = strtolower($action);
                $map['urp_roleid'] = session('user.roleid');

                $priv = db('user_role_priv')->where($map)->find();

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