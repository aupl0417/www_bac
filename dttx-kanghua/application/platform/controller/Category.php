<?php
namespace app\platform\controller;

use app\common\controller\Platform;
use think\Request;
use think\Db;

class Category extends Platform
{
    public function _initialize(){
        parent::_initialize();
        $this->goodsModel = model('Goods');
    }

    public function index(){
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 30, 'intval');

        $id   = input('post.id', '', 'intval');
        $name = input('post.name', '', 'htmlspecialchars,strip_tags,trim');

        $cateModel = model('Category');
        $where     = array('ca.ca_isDelete' => 0, 'ca.ca_projectId' =>  session('user.projectId'));
        !empty($name) && $where['ca.ca_name'] = array('LIKE', '%' . $name . '%');
        !empty($id)   && $where['ca.ca_id']   = $id;

        $page['totalCount']  = $cateModel->getCateCount($where);
        $limit     = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];
        $field     = 'ca.ca_id as id,ca.ca_name as name,ca.ca_createTime as createTime,c.ca_name as parentName';
        $cateList  = $cateModel->getCateList($field, $where, true, 'ca.ca_sort asc', $limit);

        $this->assign('id', $id);
        $this->assign('name', $name);
        $this->assign('cateList', $cateList);
        $this->assign('page', $page);
        return $this->fetch();
    }

    /*
     * 添加分类
     * */
    public function create(){
        if(Request::instance()->isPost()){
            $name = input('post.name', '', 'htmlspecialchars,strip_tags');
            $pid  = input('post.parentId', 0, 'intval');
            $tab  = input('post.tab', '', 'htmlspecialchars,strip_tags');

            !$name && $this->ajaxReturn(ajaxCallBack(300, '分类名不能为空'));

            $params = array(
                'ca_name' => $name,
                'ca_pid'  => $pid,
                'ca_projectId' => session('user.projectId'),
                'ca_createId'  => session('user.userId'),
                'ca_createTime'=> time()
            );

            $res  = Db::name('category')->insert($params);
            !$res && $this->ajaxReturn(ajaxCallBack(300, '添加分类失败'));
            $tabId = $tab == 'goods' ? 'platform_Goods_create' : 'platform_Category_index';
            $this->ajaxReturn(ajaxCallBack(200, '添加分类成功', true, $tabId));
        }else{
            $where    = array('ca_isDelete' => 0, 'ca_projectId' =>  session('user.projectId'));
            $field    = 'ca_id as id,ca_name as name';
            $cateList = model('Category')->getCateList($field, $where);

            $this->assign('cateList', $cateList);
            $this->assign('tab', input('tab', '', 'htmlspecialchars,strip_tags'));
            return $this->fetch();
        }
    }

    public function edit(){
        $id = input('id', 0, 'intval');
        !$id && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        if(Request::instance()->isPost()){
            $name = input('post.name', '', 'htmlspecialchars,strip_tags');
            $pid  = input('post.parentId', 0, 'intval');
            $params = array(
                'ca_name' => $name,
                'ca_pid'  => $pid,
                'ca_operateId' => session('user.userId'),
                'ca_updateTime'=> time()
            );

            $res  = Db::name('category')->where(['ca_id' => $id])->update($params);
            !$res && $this->ajaxReturn(ajaxCallBack(300, '编辑分类失败'));
            $this->ajaxReturn(ajaxCallBack(200, '编辑分类成功', true, 'platform_Category_index'));
        }else{
            $where    = array('ca_isDelete' => 0, 'ca_projectId' =>  session('user.projectId'));
            $field    = 'ca_id as id,ca_name as name,ca_pid as pid';
            $cate     = model('Category')->getCategoryById($id, $field);

            $cateList = model('Category')->getCateList($field, $where);

            $this->assign('cateList', $cateList);
            $this->assign('cate', $cate);
            return $this->fetch();
        }
    }

    public function remove(){
        $id = input('id', 0, 'intval');
        !$id && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));

        $data = ['ca_isDelete' => 1, 'ca_updateTime' => time(), 'ca_operateId' => session('user.userId')];
        $res = Db::name('category')->where(['ca_id' => $id])->update($data);
        $res === false && $this->ajaxReturn(ajaxCallBack(300, '删除分类失败'));
        $this->ajaxReturn(ajaxCallBack(200, '删除分类成功', false, 'platform_Category_index'));
    }
}
