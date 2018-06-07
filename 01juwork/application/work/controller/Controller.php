<?php
/**
 * 此文件由表单生成器创建
 * day:{day}
 */
namespace app\work\controller;
use app\work\controller\Commonmodules;
class Controller extends Commonmodules
{
    public function _initialize()
    {
        parent::_initialize();
        $this->formtpl_id   = 314;  //表单模板ID
        $this->module_name  = '控制器管理';   //模块名称
        $this->initForm();

    }

    public function index(){
        $res = $this->_index();
        $this->assign('res',$res);

        $btns   = '<a href="/Controller/edit/id/[id]" class="btn blue btn-outline btn-block md5">修改</a><div class="btn red btn-outline btn-block" onclick="actionList([id])">权限</div>';   //操作按钮
        $html = html_table($res['data']['list'],$this->formtpl['list_fields'],$btns,1);
        $this->assign('html_table',$html['html']);

        $this->_searchFields(); //搜索表单

        return view();
    }

    /**
     * 批量删除
     */
    public function deleteSelect(){
        $res = $this->_deleteSelect();
        return $res;
    }

    /**
     * 批量设置状态
     */
    public function setStatus(){
        $res = $this->_setStatus();
        return $res;
    }

    /**
     * 修改
     */
    public function edit(){
        $res = $this->_edit();
        return view();
    }

    /**
     * 保存修改
     */
    public function edit_save(){
        $res = $this->_edit_save();
        return $res;
    }

    /**
     * 新增
     */
    public function add(){
        $res = $this->_add();
        return view();
    }
    /**
     * 保存新增
     */
    public function add_save(){
        $res = $this->_add_save();
        return $res;
    }

    /**
     * 方法列表
     */
    public function actionList(){
        $rs = db('controller')->where(['id' => $this->param['id']])->find();

        $action_arr = [];
        if($rs['action']) $action_arr = json_decode(html_entity_decode($rs['action']),true);


        $file   = APP_PATH . 'work/controller/'.$rs['controller'].'.php';
        $action = [];
        if(file_exists($file)){
            $body = file_get_contents($file);
            preg_match_all("/public function ([\s\S]*?)\(/ies",$body,$out);
            if(isset($out[1]) && $out[1]) {
                foreach($out[1] as $val){
                    if($val) $action[$val] = isset($action_arr[$val]) ? $action_arr[$val] : [];
                }
            }
        }


        //dump($action);
        $this->assign('action',$action);
        return view();
    }

    /**
     * 保存方法权限
     */
    public function setAction(){
        //dump($this->post);
        $res = db('controller')->where(['id' => $this->post['id']])->update(['action' => json_encode($this->post)]);
        if(false !== $res){
            return ['code' => 1,'msg' => '操作成功！'];
        }

        return ['code' => 0,'msg' => '操作失败！'];
    }
}
