<?php
/**
 * 配置参数字段
 * day:2017-06-17
 */
namespace app\work\controller;
use app\work\controller\Common;
class Configfield extends Common
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index(){
        $group = get_category([
            'table'     => 'config_category',
            'where'     => ['status' => 1],
            'order'     => 'sort asc,id asc',
        ]);

        $this->assign('group',$group['data']);
        return view();
    }

    /**
     * 分组字段
     */
    public function fields(){
        $list = db('config_fields')->where(['group_id' => $this->param['group_id']])->field('etime,appid',true)->order('sort asc,id asc')->select();
        $this->assign('list',$list);
        return view();
    }

    /**
     * 新增字段
     */
    public function add(){
        return view();
    }

    /**
     * 保存新增的字段
     */
    public function add_save(){
        $res = $this->validate($this->post,'ConfigFields');
        if(true !== $res){
            return ['code' => 0,'msg' => $res];
        }

        if(db('config_fields')->where(['name' => $this->post['name'],'group_id' => $this->post['group_id']])->find()){
            return ['code' => 0,'msg' => '已存在同名参数！'];
        }

        if(model('ConfigFields')->allowField(true)->save($this->post)){
            return ['code' => 1,'msg' => '操作成功！'];
        }
        return ['code' => 0,'msg' => '操作失败！'];
    }

    /**
     * 修改字段
     */
    public function edit(){
        $rs = db('config_fields')->where(['id' => $this->param['id']])->find();
        $this->assign('rs',$rs);
        return view();
    }

    /**
     * 保存修改的字段
     * @return array
     */
    public function edit_save(){
        $res = $this->validate($this->post,'ConfigFields');
        if(true !== $res){
            return ['code' => 0,'msg' => $res];
        }

        if(db('config_fields')->where(['name' => $this->post['name'],'group_id' => $this->post['group_id'],'id' => ['neq',$this->post['id']]])->find()){
            return ['code' => 0,'msg' => '已存在同名参数！'];
        }

        if(model('ConfigFields')->allowField(true)->save($this->post,['id' => $this->post['id']])){
            return ['code' => 1,'msg' => '操作成功！'];
        }
        return ['code' => 0,'msg' => '操作失败！'];
    }

    /**
     * 删除字段
     * @return array
     */
    public function deleteField(){
        if(db('config_fields')->where(['id' => $this->post['id'],'is_lock' => 0])->delete()){
            return ['code' => 1,'msg' => '操作成功！'];
        }
        return ['code' => 0,'msg' => '操作失败！'];
    }


    /**
     * 字段排序
     */
    public function fieldSort(){
        foreach($this->post['id'] as $key => $val){
            db('config_fields')->where(['id' => $val])->update(['sort' => ($key+1)]);
        }
        return ['code' => 1,'msg' => '操作成功！'];
    }
}
