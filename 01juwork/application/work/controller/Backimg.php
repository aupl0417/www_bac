<?php
/**
 * 此文件由表单生成器创建
 * day:{day}
 */
namespace app\work\controller;
use app\work\controller\Commonmodules;
class Backimg extends Commonmodules
{
    public function _initialize()
    {
        parent::_initialize();
        $this->formtpl_id   = 328;  //表单模板ID
        $this->module_name  = '背景图管理';   //模块名称
        $this->initForm();

    }

    public function index(){
        $res = $this->_index();
        $this->assign('res',$res);

        $html = html_table($res['data']['list'],$this->formtpl['list_fields']);
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
}
