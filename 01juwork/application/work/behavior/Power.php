<?php
/**
 * 权限检测
 */
namespace app\work\behavior;
use app\work\controller\Common;
use think\Request;
class Power extends Common
{
    //定义此预加载，即可直接跳过Init中的_initialize()
    public function _initialize()
    {

    }

    public function run(&$params='')
    {
        $not_controller = ['Thumb','Login','Index'];   //跳过权限验证的控制器
        if(!in_array($this->request->controller(),$not_controller)) $this->power($params);
    }

    public function power($params){
        $controller = strtolower($this->request->controller());
        $action     = strtolower($this->request->action());
        //file_put_contents('power.txt',$this->request->controller().'-'.$this->request->action().PHP_EOL,FILE_APPEND);
        $rs = db('controller')->cache(true,60)->where(['status' => 1,'controller' => $controller])->field('action')->find();
        if(!$rs) $this->noPower();
        if($rs['action']){
            $n = 0;
            $action_list = json_decode(strtolower(html_entity_decode($rs['action'])),true);
            //dump($action);
            if(isset($action_list[$action]) && $action_list[$action]){
                //dump($action_list[$action]);

                foreach($action_list[$action] as $val){
                    //dump($this->request->controller().':'.$val);
                    //dump(session('admin.power')['action']);
                    if(in_array($controller.':'.$val,session('admin.power')['action'])) $n++;
                }
                //dump($n);

            }
            //dump($n);exit();
            if($n == 0) $this->noPower();
        }else{
            $this->noPower();
        }
    }

    private function noPower(){
        if($this->request->isAjax()){
            //exit();
            $this->redirect('/Index/noPowerAjax');
        }else $this->redirect('/Index/noPower');
    }


}
