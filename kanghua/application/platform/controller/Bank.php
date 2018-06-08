<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/17 0017
 * Time: 10:10
 */
namespace app\platform\controller;

use app\common\controller\Platform;
use think\Request;
use think\Db;

class Bank extends Platform{

    public function index(){
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 30, 'intval');

        $input['orderField'] =input('post.orderField','bank_enabled','trim');
        $input['orderDirection'] =input('post.orderDirection','desc','trim');

        $id       = input('post.id', '', 'intval');
        $bankname = input('post.bankname', '', 'htmlspecialchars,strip_tags,trim');
        $state    = input('post.state', '', 'htmlspecialchars,strip_tags,trim');
        $where    = array('bank_platform_id' => session('user.platformId'));

        !empty($bankname) && $where['bank_name']      = array('LIKE', '%' . $bankname .'%');
        ($state != 'all') && $where['bank_enabled']  = $state;
        !empty($id)   && $where['bank_id']  = $id;

        $page['totalCount'] = Db::name('bank')->where($where)->count();
        $limit = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];
        $list = Db::name('bank')->where($where)->limit($limit)->order($input['orderField'],$input['orderDirection'])->select();

        $this->assign('id',       $id ?: '');
        $this->assign('bankname', $bankname);
        $this->assign('state',    $state);
        $this->assign('page',     $page);
        $this->assign('bankList',$list);
        return $this->fetch();
    }

    public function create(){

        if(Request::instance()->isPost()){
            $state = input('post.state', 0, 'intval');
            $type  = input('post.type', 0, 'intval');
            $name  = input('post.name', '', 'htmlspecialchars,strip_tags,trim');

            !$name && $this->ajaxReturn(ajaxCallBack(300, '请输入银行名称'));

            $platformId = session('user.platformId');
            if(Db::name('bank')->where(['bank_name' => $name, 'bank_platform_id' => $platformId])->count()){
                $this->ajaxReturn(ajaxCallBack(300, '银行名称在该平台已存在'));
            }

            $data = array(
                'bank_name' => $name,
                'bank_type' => $type,
                'bank_platform_id' => $platformId,
                'bank_enabled'     => $state
            );

            $res = Db::name('bank')->insert($data);
            !$res && $this->ajaxReturn(ajaxCallBack(300, '添加银行失败'));
            $this->ajaxReturn(ajaxCallBack(200, '添加银行成功', true, 'platform_Bank_index'));
        }else{
            return $this->fetch();
        }

    }

    public function edit(){
        $id = input('id', 0, 'intval');
        !$id && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));

        if(Request::instance()->isPost()){
            $state = input('post.state', 0, 'intval');
            $type  = input('post.type', 0, 'intval');
            $name  = input('post.name', '', 'htmlspecialchars,strip_tags,trim');

            !$name && $this->ajaxReturn(ajaxCallBack(300, '请输入银行名称'));

            $platformId = session('user.platformId');
            if(Db::name('bank')->where(['bank_name' => $name, 'bank_platform_id' => $platformId, 'bank_id' => array('neq', $id)])->count()){
                $this->ajaxReturn(ajaxCallBack(300, '银行名称在该平台已存在'));
            }

            $data = array(
                'bank_name' => $name,
                'bank_type' => $type,
                'bank_enabled'     => $state
            );

            $res = Db::name('bank')->where(['bank_id' => $id])->update($data);
            !$res && $this->ajaxReturn(ajaxCallBack(300, '编辑银行失败'));
            $this->ajaxReturn(ajaxCallBack(200, '编辑银行成功', true, 'platform_Bank_index'));
        }else{
            $field = 'bank_id,bank_name,bank_type,bank_enabled';
            $where = ['bank_id' => $id, 'bank_platform_id' => session('user.platformId')];
            $bank  = Db::name('bank')->where($where)->field($field)->find();
            $this->assign('id', $id);
            $this->assign('bank', $bank);
            return $this->fetch();
        }


    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }

}