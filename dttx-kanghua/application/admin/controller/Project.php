<?php

namespace app\admin\controller;

use app\admin\model\Account;
use app\admin\model\Platform;
use app\admin\model\User;
use app\common\controller\Admin;
use app\common\tools\Logs;
use app\platform\model\Area;
use think\Config;
use think\Db;
use think\Exception;
use think\Request;

class Project extends Admin {

    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $project =new Platform();
        $input['pageSize']=input('post.pageSize','30','intval');
        $input['pageCurrent']=input('post.pageCurrent',1,'intval');
        $input['orderField']=input('post.orderField','pl_create_time','trim');
        $input['orderDirection'] =input('post.orderDirection','desc','trim');
        $input['name']=input('post.name','','trim');
        $input['companyname']=input('post.companyname','','trim');
        $input['order']=$input['orderField'].' '.$input['orderDirection'];

        $data =$project->findPlatFormList($input);

        $this->assign('data',$data);
        $this->assign('input',$input);

        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        if (Request::instance()->isPost()){


            $data['name'] =input('post.name','','trim');
            $data['companyname']=input('post.companyname','','trim');
            $data['contact'] =input('post.contact','','trim');
            $data['dttxnick'] = input('post.dttxnick','','trim');
            $data['status'] =input('post.status','','trim');
            $data['content'] =input('post.content','','trim');
            $data['servicenick']= input('post.servicenick','','trim');
            $data['serviceratio']= input('post.serviceratio','','trim');

            $data['image'] =input('post.image','','trim');
            $data['description'] =input('post.description','','trim');

            $data['pl_provinceCode'] =input('post.pl_provinceCode',0,'intval');
            $data['pl_cityCode']=input('post.pl_cityCode',0,'intval');
            $data['pl_regionCode']=input('post.pl_regionCode',0,'intval');

            //验证
            $result = $this->validate($data,'Project');
            if(true !== $result){
                // 验证失败 输出错误信息
               $this->ajaxReturn(ajaxCallBack(300,$result));
            }
            $dttxdata=getDttxUserInfo($data['dttxnick'],false);
            $service_user=getDttxUserInfo($data['servicenick'],false);
            $dttxuser=getDttxUserInfo('大唐天下',false);
            if (empty($dttxdata['data'])){
                $this->ajaxReturn(ajaxCallBack(301,'链接超时，请重新提交!'));
            }
            if (empty($service_user)){
                $this->ajaxReturn(ajaxCallBack(301,'链接超时，请重新提交!'));
            }
            if (empty($dttxuser)){
                $this->ajaxReturn(ajaxCallBack(301,'链接超时，请重新提交!'));
            }
            if ($dttxdata['status']!='1001'){
                $this->ajaxReturn(ajaxCallBack(300,'当前大唐账号不存在，请确认!'));
            }
            if ($service_user['status']!='1001'){
                $this->ajaxReturn(ajaxCallBack(300,'技术服务费大唐账号不存在，请确认!'));
            }
            if ($dttxuser['status']!='1001'){
                $this->ajaxReturn(ajaxCallBack(300,'分润计算账号不存在，请确认!'));
            }
            $remoteUser =[];
            array_push($remoteUser,$dttxdata['data']);
            array_push($remoteUser,$service_user['data']);
            array_push($remoteUser,$dttxuser['data']);
            $time =time();
            Db::startTrans();
            try{
                $platform_data =[
                    'pl_name'=>$data['name'],
                    'pl_company_name'=>$data['companyname'],
                    'pl_contact'=>$data['contact'],
                    'pl_dttx_nick'=>$data['dttxnick'],
                    'pl_dttx_uid'=>$dttxdata['data']['userID'],
                    'pl_dttx_code'=>$dttxdata['data']['code'],
                    'pl_tech_uid'=>$service_user['data']['userID'],
                    'pl_tech_nick'=>$service_user['data']['userNick'],
                    'pl_tech_ratio'=>$data['serviceratio'],
                    'pl_service_uid'=>$dttxuser['data']['userID'],
                    'pl_service_nick'=>$dttxuser['data']['userNick'],
                    'pl_service_ratio'=>8,
                    'pl_provinceCode'=>$data['pl_provinceCode'],
                    'pl_cityCode'=>$data['pl_cityCode'],
                    'pl_regionCode'=> $data['pl_regionCode'],
                    'pl_states'=>$data['status'],
                    'pl_content'=>$data['content'],
                    'pl_image'=>$data['image'],
                    'pl_description'=>$data['description'],
                    'pl_create_time'=>$time
                ];
                Db::name('platform')->insert($platform_data);
                $platformId =Db::name('platform')->getLastInsID();
                if (!$platformId){
                    Logs::writeMongodb(500020,'db_platform',$data['name'],'项目记录创建失败',$platform_data);
                    throw new Exception('项目记录创建失败!');
                }else{
                    Logs::writeMongodb(500021,'db_platform',$data['name'],'项目记录创建成功',$platform_data);
                }

                //循环插入
                foreach ($remoteUser as $item){
                    $user =new User();
                    $users=$user->existUserByUid($item['userID']);
                    if (false===$users){
                        $userdata =[
                            'u_dttx_uid'=>$item['userID'],
                            'u_type'=>$item['type'],
                            'u_nick'=>$item['userNick'],
                            'u_logo'=>$item['userLogo'],
                            'u_name'=>$item['realName'],
                            'u_tel' =>$item['tel'],
                            'u_level'=>$item['level'],
                            'u_state'=>$item['state'],
                            'u_createTime'=>strtotime($item['createTime']),
                            'u_code'=>$item['code'],
                            'u_fCode'=>$item['fCode'],
                            'u_auth'=>$item['auth'],
                        ];
                        ksort($userdata);
                        $userinforCrcCode =getSuperMD5(implode('--',$userdata));
                        $userdata['u_crc']=$userinforCrcCode;
                        $userdata['u_activeTime']=mytime();
                        Db::name('user')->insert($userdata);
                        $userid =Db::name('user')->getLastInsID();
                        if (!$userid){
                            Logs::writeMongodb(500020,'db_platform',$data['name'],'创建用户信息记录失败',$userdata);
                            throw new Exception('创建用户信息记录失败!');
                        }else{
                            Logs::writeMongodb(500021,'db_platform',$data['name'],'创建用户信息记录成功',$userdata);
                        }
                    }else{
                        $userid =$users['u_id'];
                    }
                    $platformdata=[
                        'up_plateform_id'=>$platformId,
                        'up_dttx_uid'=>$item['userID'],
                        'up_fcode'=>Config::get('default_dttx_fcode'),
                        'up_roleid'=>$data['dttxnick']==$item['userNick']?1:0,
                        'up_isActive'=>1,
                        'up_uid'=>$userid,
                        'up_unick'=>$item['userNick'],
                        'up_create_time'=>$time
                    ];
                    Db::name('user_platform')->insert($platformdata);
                    $uplastid =Db::name('user_platform')->getLastInsID();
                    if (!$uplastid){
                        Logs::writeMongodb(500020,'db_platform',$data['name'],'创建用户信息记录附表失败',$platformdata);
                        throw new Exception('创建用户信息记录附表失败，请重试！');
                    }else{
                        Logs::writeMongodb(500021,'db_platform',$data['name'],'创建用户信息记录附表成功',$platformdata);
                    }

                    if ($data['dttxnick'] == $item['userNick']){
                        $shopkeeper =[
                            's_name'=> $data['name'],
                            's_userId'=>$uplastid,
                            's_userTrueName'=>$item['realName'],
                            's_userDttxNick'=>$data['dttxnick'],
                            's_provinceCode'=>$data['pl_provinceCode'],
                            's_cityCode'=>$data['pl_cityCode'],
                            's_regionCode'=>$data['pl_regionCode'],
                            's_content'=>$data['companyname'],
                            's_state'=>'pass',
                            's_createTime'=>time(),
                            's_projectId'=>$platformId,
                        ];

                        Db::name('shopkeeper')->insert($shopkeeper);
                        $lastshopid =Db::name('shopkeeper')->getLastInsID();
                        if (!$lastshopid){
                            Logs::writeMongodb(500020,'db_platform',$data['name'],'项目管理员同步经销商失败',$shopkeeper);
                            throw new Exception('项目管理员同步经销商失败!');
                        }else{
                            Logs::writeMongodb(500021,'db_platform',$data['name'],'项目管理员同步经销商成功',$shopkeeper);
                        }

                        $baseData =[
                            'ba_code'=>'parent',
                            'ba_name'=>'总仓库',
                            'ba_description'=>'总仓库',
                            'ba_pid'=>0,
                            'ba_channel'=>'总仓库',
                            'ba_createId'=>$uplastid,
                            'ba_createTime'=>time(),
                            'ba_operateId'=>$uplastid,
                            'ba_updateTime'=>time(),
                            'ba_projectId'=>$platformId
                        ];
                        Db::name('base')->insert($baseData);
                        $lastBaseId =Db::name('shopkeeper')->getLastInsID();
                        if (!$lastBaseId){
                            Logs::writeMongodb(500020,'db_platform',$data['name'],'总仓库初始化失败',$baseData);
                            throw new Exception('总仓库初始化失败!');
                        }else{
                            Logs::writeMongodb(500021,'db_platform',$data['name'],'总仓库初始化成功',$baseData);
                        }
                    }

                    $account =[
                        'a_uid'  =>$uplastid,
                        'a_platform_id'=>$platformId,
                        'a_dttx_uid'=>$item['userID'],
                        'a_nick'=>$item['userNick'],
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
                        Logs::writeMongodb(500020,'db_platform',$data['name'],'账户记录创建失败',$account);
                        throw new Exception('账户记录创建失败，请重试!');
                    }else{
                        Logs::writeMongodb(500021,'db_platform',$data['name'],'账户记录创建成功',$account);
                    }
                }
                Db::commit();
                Logs::writeMongodb(500021,'db_platform',$data['name'],'创建项目成功');
                $this->ajaxReturn(ajaxCallBack(200,'创建项目成功!',true,'admin_Project_index'));
            }catch (\Exception $e){
            //    print_r($e->getMessage());
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300,$e->getMessage()));
            }

        }else{
            $areaModel = new Area();
            $area =$areaModel->findListByParentId(0);
            $this->assign('area',$area);
            $this->assign('action','add');
            return $this->fetch();
        }

    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit()
    {
        if (Request::instance()->isPost()){
            $id =input('post.plid','','intval');
            $data['name'] =input('post.name','','trim');
            $data['companyname']=input('post.companyname','','trim');
            $data['contact'] =input('post.contact','','trim');
            $data['dttxnick'] = input('post.dttxnick','','trim');
            $data['status'] =input('post.status','','trim');
            $data['content'] =input('post.content','','trim');
            $data['servicenick']= input('post.servicenick','','trim');
            $data['serviceratio']= input('post.serviceratio','','trim');

            $data['image'] =input('post.image','','trim');
            $data['description'] =input('post.description','','trim');

//
//            $data['pl_provinceCode'] =input('post.pl_provinceCode',0,'intval');
//            $data['pl_cityCode']=input('post.pl_cityCode',0,'intval');
//            $data['pl_regionCode']=input('post.pl_regionCode',0,'intval');

            //验证
            $result = $this->validate($data,'Project');
            if(true !== $result){
                // 验证失败 输出错误信息
                $this->ajaxReturn(ajaxCallBack(300,$result));
            }

            $platformdata = [
                'pl_name'=>$data['name'],
                'pl_company_name'=>$data['companyname'],
                'pl_contact'=>$data['contact'],
                'pl_states'=>$data['status'],
                'pl_content'=>$data['content'],
                'pl_image'=>$data['image'],
                'pl_description'=>$data['description'],
            ];

//            if (!empty($data['pl_provinceCode'])){
//                $platformdata['pl_provinceCode'] =$data['pl_provinceCode'];
//            }
//
//            if (!empty($data['pl_cityCode'])){
//                $platformdata['pl_cityCode'] =$data['pl_cityCode'];
//            }
//
//            if (!empty($data['pl_regionCode'])){
//                $platformdata['pl_regionCode'] =$data['pl_regionCode'];
//            }
            $res = Db::name('platform')->where('pl_id',$id)->update($platformdata);
            if ($res){
                $this->ajaxReturn(ajaxCallBack('200','修改成功!',true,'admin_Project_index'));
            }else{
                $this->ajaxReturn(ajaxCallBack('300','修改失败，请重试!'));
            }


        }

        $id = Request::instance()->param('id','0','intval');
        if (empty($id)){
            $this->ajaxReturn(ajaxCallBack(300,'参数错误，请重试!'));
        }
        $platefrom =new Platform();
        $data =$platefrom->findDetailByid($id);
        if (empty($data)){
            $this->ajaxReturn(ajaxCallBack('300','该信息不存在或已被删除，请刷新页面后重试！'));
        }

        $areaModel = new Area();
        $area =$areaModel->findListByParentId(0);
        $this->assign('area',$area);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function remove()
    {
        $ids =input('ids','');
        if (empty($ids)){
            $this->ajaxReturn(ajaxCallBack(300,'请选择需要操作条目!'));
        }
        $platform =new Platform();
        $platform->remove($ids);

    }

    public function change(){

        $status =Request::instance()->param('status');
        $ids =Request::instance()->param('ids');
        if (empty($ids)){
            $this->ajaxReturn(ajaxCallBack(300,'请选择需要操作条目!'));
        }
        $platform =new Platform();
        $res =$platform->changeStatus($ids,$status);

        if ($res){
            $this->ajaxReturn(ajaxCallBack(200,'批量操作成功！'));
        }else{
            $this->ajaxReturn(ajaxCallBack(300,'批量操作失败！'));
        }

    }

    public function reviewlist(){
        $input['pageSize']    = input('post.pageSize','30','intval');
        $input['pageCurrent'] = input('post.pageCurrent',1,'intval');
        $input['orderField']  = input('post.orderField','in_createTime','htmlspecialchars,strip_tags,trim');
        $input['orderDirection'] =input('post.orderDirection','desc','htmlspecialchars,strip_tags,trim');
        $input['productname']    =input('post.productname','','htmlspecialchars,strip_tags,trim');
        $input['companyname']    =input('post.companyname','','htmlspecialchars,strip_tags,trim');
        $input['dttxnick']       =input('post.dttxnick','','htmlspecialchars,strip_tags,trim');
        $input['mobile']         =input('post.mobile','','htmlspecialchars,strip_tags,trim');
        $input['order']          = $input['orderField'].' '.$input['orderDirection'];

        $where = ['in_isDelete' => 0];
        $input['productname'] && $where['in_product_name'] = array('LIKE', '%' . $input['productname'] . '%');
        $input['companyname'] && $where['in_company_name'] = array('LIKE', '%' . $input['companyname'] . '%');
        $input['mobile']      && $where['in_mobile']       = array('EQ', $input['mobile']);
        $input['dttxnick']    && $where['in_dttx_nick']    = array('EQ', $input['dttxnick']);
        $limit = ($input['pageCurrent'] - 1) * $input['pageSize'] . ',' . $input['pageSize'];
        $order = $input['order'] ?: 'in_createTime asc';

        $data['list']  = Db::name('investment')->where($where)->limit($limit)->order($order)->select();
        $data['count'] = Db::name('investment')->where($where)->count();

        $this->assign('data', $data);
        $this->assign('input',$input);

        return $this->fetch();
    }

    public function review(){
        $id = input('id', 0, 'intval');
        !$id && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));
        if(Request::instance()->isPost()){
            $data = array(
                'in_status' => input('post.status', 0, 'intval'),
                'in_reason' => input('post.reason', '', 'htmlspecialchars,strip_tags,trim'),
                'in_updateTime' => time(),
            );
            !$data['in_status'] && $this->ajaxReturn(ajaxCallBack(300, '请选择审核状态'));
            ($data['in_status'] == -1 && empty($data['in_reason'])) && $this->ajaxReturn(ajaxCallBack(300, '请填写审核拒绝原因'));

            $res = Db::name('investment')->where(['in_id' => $id, 'in_isDelete' => 0])->update($data);
            $res === false && $this->ajaxReturn(ajaxCallBack(300, '审核失败'));
            $this->ajaxReturn(ajaxCallBack(200, '审核成功', true, 'admin_Project_reviewlist'));
        }else{
            $data = Db::name('investment')->where(['in_id' => $id, 'in_isDelete' => 0, 'in_status' => 0])->find();
            !$data && $this->ajaxReturn(ajaxCallBack(300, '暂无数据', true, 'admin_Project_reviewlist'));
            $this->assign('data', $data);
            return $this->fetch();
        }

    }


}
