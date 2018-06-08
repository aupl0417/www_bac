<?php 
/**
 * 用户权限
 * 传入一个数组 $action = array(
                'permission'    //需要验证的行为
                'allow'         //所有人都可以进
        );
        现在2个功能基本一样
 * @author Lain
 *
 */
namespace app\admin\behavior;
use think\Request;

class Authenticate{
    protected $options = array();
    
    public function run(&$params) {
        //检查会话是否超时
        //session(null);
        if(!session('userid') || !session('roleid') || cookie('userid') != session('userid')){
            $this->ajaxReturn(array('statusCode'=>301,'message'=>'会话超时！'));
        }
        $request = Request::instance();
        $module = $request->module();
        $controller = $request->controller();
        $action = $request->action();
        $allow = isset($params['allow']) ? $params['allow'] : array();
        $permission = isset($params['permission']) ? $params['permission'] : array();
        
        if (session('?admin')) {
            return true;
        }
        
        if (in_array($action, $permission)) {
            return true;
        } elseif (session('?admin_identify')) {
            if (in_array($action, $allow)) {
                return true;
            } else {
                $map['m'] = strtolower($module);
                $map['c'] = strtolower($controller);
                $map['a'] = strtolower($action);
                $map['roleid'] = session('roleid');
                
                $priv = db('admin_role_priv')->where($map)->find();
                
                if (is_array($priv) && !empty($priv)) {
                    return true;
                } else {
                    $this->ajaxReturn(array('statusCode'=>300,'message'=>'您没有此权限！'));
                }
            }
        } else {
            //session过期
            $this->ajaxReturn(array('statusCode'=>301,'message'=>'会话超时！'));
        }
        return true;
    }
    
    public function ajaxReturn($data){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }
}