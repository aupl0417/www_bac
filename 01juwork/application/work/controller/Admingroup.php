<?php
/**
 * 此文件由表单生成器创建
 * day:{day}
 */
namespace app\work\controller;
use app\work\controller\Commonmodules;
class Admingroup extends Commonmodules
{
    public function _initialize()
    {
        parent::_initialize();
        $this->formtpl_id   = 318;  //表单模板ID
        $this->module_name  = '角色管理';   //模块名称
        $this->initForm();

    }

    public function index(){
        $res = $this->_category();
        $this->assign('res',$res);
        $html = html_table($res['data'],$this->formtpl['list_fields']);
        //dump($res);
        $this->assign('html_table',$html['html']);
        $this->_searchFields(); //搜索表单

        return view();
    }

    /**
     * 批量删除
     */
    public function deleteCategorySelect(){
        $res = $this->_deleteCategorySelect();
        return $res;
    }

    /**
     * 转移目录
     */
    public function changeCategory(){
        $res = $this->_changeCategory();
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
     * 排序
     */
    public function setSort(){
        $res = $this->_setSort();
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
     * 菜单列表
     */
    public function menu(){
        $list = get_category([
            'table'     => 'menu',
            'field'     => 'id,upid,name',
            'order'     => 'sort asc,id asc',
            'where'     => ['status' => 1],
        ]);

        //dump($list);
        //$this->assign('menu',$list['data']);
        $rs = db('admin_group')->where(['id' => $this->param['id']])->field('menu_id')->find();
        $tree = tree($list,$rs['menu_id']);
        $this->assign('tree',$tree);

        return view();
    }

    /**
     * 保存菜单ID
     */
    public function setMenu(){
        $res = db('admin_group')->where(['id' => $this->post['group_id']])->update(['menu_id' => $this->post['ids']]);
        if(false !== $res) return ['code' => 1,'msg' => '操作成功！'];
        return ['code' => 0,'msg' => '操作失败！'];
    }

    /**
     * 模块权限
     */
    public function controllerList(){
        $action = [];
        $rs = db('admin_group')->where(['id' => $this->param['id']])->field('action')->find();
        if($rs['action']) $action = json_decode(html_entity_decode($rs['action']),true);
        $this->assign('action',$action);

        $list = db('controller')->where(['status' => 1])->field('controller,controller_name')->select();
        $this->assign('list',$list);

        return view();
    }

    public function setController(){
        $res = db('admin_group')->where(['id' => $this->post['id']])->update(['action' => json_encode($this->post['action'])]);
        if(false !== $res) return ['code' => 1,'msg' => '操作成功！'];
        return ['code' => 0,'msg' => '操作失败！'];
    }
}
