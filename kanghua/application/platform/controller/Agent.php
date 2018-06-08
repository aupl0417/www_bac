<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/1 0001
 * Time: 16:20
 */
namespace app\platform\controller;

use app\common\controller\Platform;
use app\common\tools\Logs;
use app\platform\model\Area;
use think\Exception;
use think\Request;
use think\db;
use think\Session;

class Agent extends Platform{

    public function _initialize(){
        parent::_initialize();
        $this->agentModel = model('Agent');
    }

    public function index(){
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 30, 'intval');

        $id        = input('post.id', '', 'intval');
        $name      = input('post.name', '', 'htmlspecialchars,strip_tags,trim');
        $provinceCode  = input('post.provinceCode', '', 'intval');
        $cityCode      = input('post.cityCode', '', 'intval');
        $level         = input('post.level', '', 'intval');
        $where         = array('a_isDelete' => 0, 'a_projectId' => session('user.platformId'), 'a_state' => array('neq', 'blocked'));

        !empty($name) && $where['a_dttxNick']    = array('LIKE', '%' . $name .'%');
        !empty($id)   && $where['a_id']          = array('=', $id);
        !empty($provinceCode)  && $where['a_provinceId'] = array('=', $provinceCode);
        !empty($cityCode)      && $where['a_cityId']     = array('=', $cityCode);
        !empty($level)         && $where['a_level']      = array('=', $level);

        $page['totalCount']  = $this->agentModel->getAgentCount($where);
        $limit = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];
        $join  = [
            ['db_user_platform up', 'up.up_id=a.a_userId', 'LEFT'],
            ['db_user u','up.up_uid=u.u_id', 'LEFT'],
            ['db_channel c','a.a_level=c.c_id', 'LEFT']
        ];
        $field = 'a_id as id,a_dttxNick as nickName,a_level as level,a_provinceId,a_cityId,a_createTime as createTime,a_state as state,u_name as trueName,c_name as levelName';
        $agent = $this->agentModel->getAgentAll($field, $where, $join, 'a_id desc', $limit);
        if($agent){
            $areaModel = model('common/Area');
            foreach ($agent as &$val){
                $province    = $areaModel->getAreaById('a_name', $val['a_provinceId']);
                $city        = $areaModel->getAreaById('a_name', $val['a_cityId']);
                $val['area'] = ($province ? $province['a_name'] : '')  . ($city ? '-' . $city['a_name'] : '');
            }
        }

        $areaModel = new Area();
        $area =$areaModel->findListByParentId(0);

        $levelList = Db::table('db_channel')->where(array('c_projectId' => session('user.platformId')))->field('c_id as id,c_name as name')->select();

        $this->assign('area',      $area);
        $this->assign('levelList', $levelList);
        $this->assign('agentList', $agent);
        $this->assign('page',      $page);
        $this->assign('id',        $id ?: '');
        $this->assign('name',      $name);
        $this->assign('level',     $level);
        $this->assign('provinceId',$provinceCode);
        return $this->fetch();
    }

    public function edit()
    {
        // TODO: Implement edit() method.
    }

    public function create(){

        if (Request::instance()->isPost()){

            $provinceId = input('post.provinceId', 0, 'intval');
            $cityId     = input('post.cityId', 0, 'intval');

            $nickname   = input('post.dttxnick', '', 'htmlspecialchars,strip_tags,trim');

            !$provinceId && $this->ajaxReturn(ajaxCallBack(300, '请选择代理商区域'));
            if (empty($nickname)){
                return $this->ajaxReturn(ajaxCallBack(300,'请输入代理商会员名'));
            }
            $platformId =Session::get('user.platformId');
            $res = Db::name('agent')->where(['a_dttxNick'=>$nickname,'a_projectId'=>$platformId])->find();
            if (!empty($res)){
                if ($res['a_state']=='freeze'){
                    return $this->ajaxReturn(ajaxCallBack(300,'添加的代理商会员名已存在，账号已冻结!',true));
                }
                return $this->ajaxReturn(ajaxCallBack(300,'您添加的代理商会员名已存在!',true));
            }

            $hasagent =Db::name('agent')->where(['a_provinceId'=>$provinceId,'a_cityId'=>$cityId,'a_projectId'=>$platformId])->count();
            if ($hasagent>0){
                return $this->ajaxReturn(ajaxCallBack(300,'该区域已添加过代理!'));
            }

            $dttxinfo =getDttxUserInfo($nickname,false);
            if (empty($dttxinfo)){
                return $this->ajaxReturn(ajaxCallBack(300,'获取大唐账号信息超时，请重试!'));
            }

            if ($dttxinfo['status']!=1001){
                return $this->ajaxReturn(ajaxCallBack(300,$dttxinfo['data'],true));
            }

            $dttxinfo =$dttxinfo['data'];
            $dttxUid =$dttxinfo['userID'];
            $User =new \app\admin\model\User();
            $users = $User->existUserByUid($dttxUid);
            Db::startTrans();
            try{
                if (false===$users){
                    $userdata =[
                        'u_dttx_uid'=>$dttxinfo['userID'],
                        'u_type'=>$dttxinfo['type'],
                        'u_nick'=>$dttxinfo['userNick'],
                        'u_logo'=>$dttxinfo['userLogo'],
                        'u_name'=>$dttxinfo['realName'],
                        'u_tel' =>$dttxinfo['tel'],
                        'u_level'=>$dttxinfo['level'],
                        'u_state'=>$dttxinfo['state'],
                        'u_createTime'=>strtotime($dttxinfo['createTime']),
                        'u_code'=>$dttxinfo['code'],
                        'u_fCode'=>$dttxinfo['fCode'],
                        'u_auth'=>$dttxinfo['auth'],
                    ];
                    ksort($userdata);
                    $userinforCrcCode =getSuperMD5(implode('--',$userdata));
                    $userdata['u_crc']=$userinforCrcCode;
                    $userdata['u_activeTime']=mytime();
                    Db::name('user')->insert($userdata);
                    $userid =Db::name('user')->getLastInsID();
                    if (!$userid){
                        throw new Exception('创建用户信息记录失败!');
                    }
                }else{
                    $userid =$users['u_id'];
                }

                $levelField = (!empty($provinceId) && empty($cityId)) ? 'province' : 'city';
                $levels     = Db::name('channel')->where(['c_levelcode' => $levelField])->field('c_id')->find();
                $level      = !empty($levels) ? $levels['c_id'] : 0;

                $userPlatformInfo =Db::name('user_platform')->where(['up_dttx_uid'=>$dttxUid,'up_plateform_id'=>$platformId])->find();
                if (empty($userPlatformInfo)){
                    $userPlatform_data =[
                        'up_plateform_id'  =>$platformId,
                        'up_fcode'  =>0,
                        'up_uid'    =>$userid,
                        'up_unick'  =>$dttxinfo['userNick'],
                        'up_user_agent_level'=>$level,
                        'up_roleid'=>2,
                        'up_dttx_uid'=>$dttxUid,
                        'up_create_time'=>time()
                    ];
                    Db::name('user_platform')->insert($userPlatform_data);
                    $userplatformId =Db::name('user_platform')->getLastInsID();
                    if (!$userplatformId){
                        throw new Exception('创建用户信息记录失败，请重试!');
                    }
                    $account =[
                        'a_uid'  =>$userplatformId,
                        'a_platform_id'=>$platformId,
                        'a_dttx_uid'=>$dttxinfo['userID'],
                        'a_nick'=>$dttxinfo['userNick'],
                        'a_createTime'=>time(),
                        'a_freeMoney'=>0,
                        'a_frozenMoney'=>0,
                        'a_score'=>0,
                        'a_totalMoney'=>0,
                        'a_agentMoney'=>0,
                        'a_vipMoney'=>0,
                        'a_tangBao'=>0,
                        'a_storeScore'=>0,
                        'a_scoreTotal'=>0,
                        'a_tangTotal'=>0
                    ];
                    ksort($account);
                    $account['a_crc']=getSupersha1(implode('--',$account));
                    Db::name('account')->insert($account);
                    $accountlastid =Db::name('account')->getLastInsID();
                    if (!$accountlastid){
                        throw new Exception('账户记录创建失败，请重试!');
                    }

                }else{
                    $userplatformId =$userPlatformInfo['up_id'];
                    $roleResult = Db::name('user_platform')->where(['up_id' => $userplatformId, 'up_isDelete' => 0, 'up_states' => 1])->update(['up_user_agent_level' => $level, 'up_roleid' => 2]);
                    if (!$roleResult){
                        throw new Exception('更新用户代理商权限失败!');
                    }
                }

                $agent_data =[
                    'a_userId' =>$userplatformId,
                    'a_provinceId' =>$provinceId,
                    'a_cityId'  =>$cityId,
                    'a_level' =>$level,
                    'a_dttxNick'=>$dttxinfo['userNick'],
                    'a_ratio'=>empty($cityId)?3:15,  //分润比例
                    'a_createId' =>Session::get('user.userId'),
                    'a_createTime' =>time(),
                    'a_operateId'=>Session::get('user.userId'),
                    'a_state'=>'normal',
                    'a_reason'=>'项目方添加',
                    'a_projectId'=>$platformId
                ];
                $agentResult = Db::name('agent')->insertGetId($agent_data);
                if (!$agentResult){
                    throw new Exception('创建代理商记录失败!');
                }
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200,'添加代理成功!',true,'platform_Agent_index'));
            }catch (\Exception $e){
                Logs::writeMongodb(300002,'db_agent',$nickname,'添加代理失败',$agent_data);
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300,'添加代理失败，请重试!'));
            }

        }else{

            $areaModel = new Area();
            $area =$areaModel->findListByParentId(0);
            $this->assign('area',$area);
            return $this->fetch();

        }


    }

    /*
     * 添加代理
     * */
    public function create_bak(){
        if(Request::instance()->isPost()){
            $provinceId = input('post.provinceId', 0, 'intval');
            $cityId     = input('post.cityId', 0, 'intval');
            $level      = input('post.level', 0, 'intval');
            $nickname   = input('post.nickname', '', 'htmlspecialchars,strip_tags,trim');
            $userId     = input('post.userId', 0, 'intval');

            !$provinceId && $this->ajaxReturn(ajaxCallBack(300, '请选择代理商区域'));
            !$level      && $this->ajaxReturn(ajaxCallBack(300, '代理商等级不能为空'));
            (!$userId || !$nickname) && $this->ajaxReturn(ajaxCallBack(300, '代理商会员名不能为空'));

            $where = ['up_id' => $userId, 'up_isDelete' => 0, 'up_states' => 1];
            $userPlatform = Db::name('user_platform')->where($where)->field('up_id as id,up_plateform_id as platformId')->find();
            if(!$userPlatform){
                $this->ajaxReturn(ajaxCallBack(300, '暂无平台用户信息'));
            }

            $agentModel = Db::name('agent');
            $agent = $agentModel->where(['a_dttxNick' => $nickname, 'a_isDelete' => 0])->field('a_state as state')->find();
            if($agent){
                if($agent['state'] == 'normal'){

                    $this->ajaxReturn(ajaxCallBack(300, '该用户已是经销商，不能重复添加'));
                }

                if($agent['state'] == 'freeze'){
                    $this->ajaxReturn(ajaxCallBack(300, '该用户的经销商资格已被冻结'));
                }
            }

            if($provinceId && !$cityId){
                if($agentModel->where(['a_provinceId' => $provinceId, 'a_state' => array('neq', 'blocked'), 'a_isDelete' => 0])->count()){
                    $this->ajaxReturn(ajaxCallBack(300, '省代理已存在'));
                }
                $condition = 'province';
            }else if($provinceId && $cityId){
                if($agentModel->where(['a_cityId' => $cityId, 'a_state' => array('neq', 'blocked'), 'a_isDelete' => 0])->count()){
                    $this->ajaxReturn(ajaxCallBack(300,'市代理已存在'));
                }
                $condition = 'city';
            }

            Db::startTrans();
            try{
                $data = array(
                    'a_userId' => $userId,
                    'a_provinceId' => $provinceId,
                    'a_cityId' => $cityId,
                    'a_level'  => $level,
                    'a_dttxNick' => $nickname,
                    'a_createId' => session('user.userId'),
                    'a_createTime' => time(),
                    'a_operateId'  => session('user.userId'),
                    'a_updateTime' => time(),
                    'a_projectId'  => $userPlatform['platformId']
                );

                $res = $agentModel->insert($data);
                if(!$res){
                    throw new Exception('添加代理商失败');
                }

                $role   = Db::name('channel')->where(['c_levelCode' => $condition, 'c_isDelete' => 0])->field('c_roleIds as roleIds')->find();
                $roleId = $role ? $role['roleIds'] : 0;
                $res    = Db::name('user_platform')->where(['up_id' => $userId, 'up_isDelete' => 0, 'up_states' => 1])->update(['up_user_agent_level' => $level, 'up_roleid' => $roleId]);
                if(!$res){
                    throw new Exception('更新用户平台关联表代理商等级失败');
                }

                // 提交事务
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200, '添加代理商成功', true, 'platform_Agent_index'));
            }catch (Exception $e){
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300, '添加代理商失败'));
            }
        }else{
            $areaModel = new Area();
            $area =$areaModel->findListByParentId(0);
            $this->assign('area',$area);
            return $this->fetch();
        }
    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }

    public function getchannel(){
        $level = input('get.level', '', 'htmlspecialchars,strip_tags,trim');

        empty($level) && $this->ajaxReturn(array('state' => 'error', 'message' => '非法参数'));

        $where = array('c_levelCode' => $level);
        $model = Db::table('db_channel')->where($where)->field('c_name,c_id')->find();

        !$model && $this->ajaxReturn(array('state' => 'error', 'message' => '暂无该级别'));

        $this->ajaxReturn(array('state' => 'ok', 'message' => $model));
    }

    public function checkUser(){
        $nickname = input('get.nickname', '', 'htmlspecialchars,strip_tags,trim');

        empty($nickname) && $this->ajaxReturn(ajaxCallBack(300, '请输入代理商会员名'));

        $where = array('u_nick' => $nickname);
        $user  = Db::name('user_platform up')->join('db_user u','up.up_uid=u.u_id','left')->where($where)->field('up_id as id,u_name as name,u_tel as tel')->find();
        !$user && $this->ajaxReturn(ajaxCallBack(300, '该会员不存在'));

        $res = Db::name('agent')->where(array('a_userId' => $user['id'], 'a_state' => 'normal'))->find();
        $res && $this->ajaxReturn(ajaxCallBack(301, ['id' => $user['id'], 'msg' => '该会员已是代理身份']));
        $user['tel'] = $user['tel'] ? (function_exists('hidtel') ? hidtel($user['tel']) : $user['tel']) : '';
        $this->ajaxReturn(ajaxCallBack(200,  $user));
    }

    public function changeState(){
        $id = Request::instance()->param('id', 0, 'intval');
        empty($id) && $this->ajaxReturn(array('state' => 'error', 'message' => '非法参数'));
        $state  = Request::instance()->param('state', '', 'htmlspecialchars,strip_tags,trim');

        if(Request::instance()->isPost()){
            $reason = Request::instance()->param('reason', '', 'htmlspecialchars,strip_tags,trim');
            !$state && $this->ajaxReturn(array('state' => 'error', 'message' => '请选择变更状态'));
            !$reason && $this->ajaxReturn(array('state' => 'error', 'message' => '请填写变更理由'));

            $data = array(
                'a_state' => $state,
                'a_reason' => $reason
            );

            $res = Db::table('db_agent')->where(array('a_id' => $id))->update($data);
            $res === false && $this->ajaxReturn(ajaxCallBack(300, '变更状态失败'));
            Logs::writeMongodb(300004,'db_agent',$id,'代理冻结',$data);
            $this->ajaxReturn(ajaxCallBack(200, '变更状态成功', true, 'platform_Agent_index'));
        }else {
            $this->assign('state', $state);
            $this->assign('id', $id);
            return $this->fetch();
        }
    }

    public function view(){
        $id = Request::instance()->param('id', 0, 'intval');
        empty($id) && $this->ajaxReturn(array('state' => 'error', 'message' => '非法参数'));

        $where     = array('a_isDelete' => 0, 'a_projectId' => session('user.platformId'), 'a_id' => $id);
        $join  = [
            ['db_user b','a.a_userId=b.u_id', 'LEFT'],
            ['db_channel c','a.a_level=c.c_id', 'LEFT']
        ];
        $field = 'a_id as id,a_dttxNick as nickName,a_level as level,a_provinceId as provinceId,a_cityId as cityId,a_createTime as createTime,a_state as state,u_name as trueName,c_name as levelName,a_reason as reason';
        $agent = $this->agentModel->getAllAgentOne($field, $where, $join);
        if($agent){
            $areaModel = model('common/Area');
            $agent['province']   = $areaModel->getAreaById('a_name', $agent['provinceId'])['a_name'];
            $agent['city']       = $areaModel->getAreaById('a_name', $agent['cityId'])['a_name'];
        }
        $this->assign('agent', $agent);
        return $this->fetch();
    }

}