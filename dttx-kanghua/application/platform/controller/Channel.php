<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/1 0001
 * Time: 14:00
 */
namespace app\platform\controller;
use app\common\controller\Platform;
use think\Request;
use think\db;

class Channel extends Platform {

    public function _initialize(){
        parent::_initialize();
        $this->model = model('Channel');
    }

    public function index()
    {
        $where = array('c_isDelete' => 0, 'c_projectId' => session('user.platformId'));
        $field = 'c_id as id,c_level as level,c_name as name,c_description as description,c_roleIds as roleIds';

        $list  = $this->model->getChannelList($field, $where);
        $this->assign('channelList', $list);
        return $this->fetch();
    }

    public function edit(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;
        $where = array('c_isDelete' => 0, 'c_projectId' => session('user.platformId'), 'c_id' => $id);

        if(Request::instance()->isPost()){
            $level = input('level', 0, 'intval');
            $name  = input('name', '', 'htmlspecialchars,strip_tags,trim');
            $desc  = input('description', '', 'htmlspecialchars,strip_tags,trim');
            $roles = input('roles', 0, 'intval');

            empty($level) && $this->ajaxReturn(ajaxRemoteMessage('error',"请输入渠道等级"));
            empty($name)  && $this->ajaxReturn(ajaxRemoteMessage('error',"请输入渠道称号"));
            empty($desc)  && $this->ajaxReturn(ajaxRemoteMessage('error',"请输入职能说明"));
            empty($roles) && $this->ajaxReturn(ajaxRemoteMessage('error',"请选择渠道后台角色"));

            $levelArray = array( 1 => array('province', '一级'), array('city', '二级'), array('region', '三级'), array('street', '四级'));
            $condition = ['c_levelcode' => $levelArray[$level][0], 'c_isDelete' => 0, 'c_id' => array('neq', $id)];
            $count = Db::table('db_channel')->where($condition)->count();
            if($count && in_array($level, array(1, 2))){//一级和二级不能重复添加
                $this->ajaxReturn(ajaxCallBack(300, '一级或二级已存在'));
            }

            $data = array(
                'c_level'       => $levelArray[$level][1],
                'c_name'        => $name,
                'c_levelcode'   => $levelArray[$level][0],
                'c_description' => $desc,
                'c_roleIds'     => $roles,
                'c_operateId'   => session('user.userId'),
                'c_updateTime'  => time(),
            );

            $res = Db::table('db_channel')->where($where)->update($data);
            $res === false && $this->ajaxReturn(ajaxRemoteMessage(300, '编辑渠道失败'));
            $this->ajaxReturn(ajaxCallBack(200, '编辑渠道成功', true, 'platform_Channel_index'));
        }else{
            $field = 'c_id as id,c_level as level,c_levelcode as code,c_name as name,c_description as description,c_roleIds as roleIds';

            $list  = $this->model->getChannelOne($field, $where);
            $roles = Db::table('db_user_role')->where(['ur_platform_id' => session('user.platformId'), 'ur_is_delete' => 0, 'ur_status' => 1])->whereOr('ur_platform_id',0)->field('ur_roleid as id,ur_rolename as name')->select();
            $level = array( 1 => '一级', '二级', '三级', '四级');

            $this->assign('roleList', $roles);
            $this->assign('level', $level);
            $this->assign('list', $list);
            return $this->fetch();
        }
    }

    public function view(){
        $id = input('id', 0, 'intval');
        !$id && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));

        $field = 'c_id as id,c_level as level,c_name as name,c_description as description,c_roleIds as roleIds';
        $where = array('c_isDelete' => 0, 'c_projectId' => session('user.platformId'), 'c_id' => $id);
        $list  = $this->model->getChannelOne($field, $where);
        $roles = Db::table('db_user_role')->where(['ur_platform_id' => session('user.platformId'), 'ur_is_delete' => 0, 'ur_status' => 1])->whereOr('ur_platform_id',0)->field('ur_roleid as id,ur_rolename as name')->select();

        $this->assign('roleList', $roles);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function create(){

        if(Request::instance()->isPost()){
            $level = input('level', 0, 'intval');
            $name  = input('name', '', 'htmlspecialchars,strip_tags,trim');
            $desc  = input('description', '', 'htmlspecialchars,strip_tags,trim');
            $roles = input('roles', 0, 'intval');

            empty($level) && $this->ajaxReturn(ajaxRemoteMessage('error',"请输入渠道等级"));
            empty($name)  && $this->ajaxReturn(ajaxRemoteMessage('error',"请输入渠道称号"));
            empty($desc)  && $this->ajaxReturn(ajaxRemoteMessage('error',"请输入职能说明"));
            empty($roles) && $this->ajaxReturn(ajaxRemoteMessage('error',"请选择渠道后台角色"));

            $levelArray = array( 1 => array('province', '一级'), array('city', '二级'), array('region', '三级'), array('street', '四级'));
            $count = Db::table('db_channel')->where(['c_levelcode' => $levelArray[$level][0], 'c_isDelete' => 0])->count();
            if($count && in_array($level, array(1, 2))){//一级和二级不能重复添加
                $this->ajaxReturn(ajaxCallBack(300, '一级或二级已存在'));
            }

            $data = array(
                'c_level'       => $levelArray[$level][1],
                'c_name'        => $name,
                'c_levelcode'   => $levelArray[$level][0],
                'c_description' => $desc,
                'c_roleIds'     => $roles,
                'c_createId'    => session('user.userId'),
                'c_operateId'   => session('user.userId'),
                'c_createTime'  => time(),
                'c_updateTime'  => time(),
                'c_projectId'   => session('user.platformId')
            );

            $res = Db::table('db_channel')->insert($data);
            !$res && $this->ajaxReturn(ajaxCallBack(300, '添加渠道失败'));
            $this->ajaxReturn(ajaxCallBack(200, '添加渠道成功', true, 'platform_Channel_index'));
        }else{

            $level = array( 1 => '一级', '二级', '三级', '四级');
            $roles = Db::table('db_user_role')->where(['ur_platform_id' => session('user.platformId'), 'ur_is_delete' => 0, 'ur_status' => 1])->whereOr('ur_platform_id',0)->field('ur_roleid as id,ur_rolename as name')->select();
            $this->assign('roleList', $roles);
            $this->assign('level', $level);
            return $this->fetch();
        }
    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }

    /*
     * 远程验证渠道等级是否存在
     * */
    public function checkchannellevel(){
        $input = input('');
        $level = isset($input['level']) ? htmlspecialchars(strip_tags(trim($input['level']))) : '';
        empty($level) && $this->ajaxReturn(array('state' => 'error', 'message' => '非法参数'));

        $where = array('c_level' => $level, 'c_createId' => session('user.userId'), 'c_projectId' => session('user.platformId'), 'c_isDelete' => 0);
        $count = $this->model->getChannelCount($where);
        $count && $this->ajaxReturn(ajaxRemoteMessage('error',"该等级已存在"));
        $this->ajaxReturn(ajaxRemoteMessage('ok', ''));
    }

    /*
     * 远程验证渠道称号是否存在
     * */
    public function checkchannelname(){
        $input = input('');
        $name  = isset($input['name']) ? htmlspecialchars(strip_tags(trim($input['name']))) : '';

        empty($name) && $this->ajaxReturn(array('state' => 'error', 'message' => '非法参数'));
        $where = array('c_level' => $name, 'c_createId' => session('user.userId'), 'c_projectId' => session('user.platformId'), 'c_isDelete' => 0);
        $count = $this->model->getChannelCount($where);
        $count && $this->ajaxReturn(ajaxRemoteMessage('error',"该称号已存在"));
        $this->ajaxReturn(ajaxRemoteMessage('ok', ''));
    }



}