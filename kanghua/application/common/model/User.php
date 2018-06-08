<?php
namespace app\common\model;

use app\common\tools\Logs;
use think\Exception;
use think\Model;
use think\Db;
use think\Request;

class User extends Model
{
    protected $table = 'user';

    public function getUserById($id, $field = '*'){

        if(!$id || !is_numeric($id)){
            return false;
        }

        return Db::name($this->table)->where(['u_id' => $id])->field($field)->find();
    }

    public function getUserOne($field = '*', $where, $join = array()){
        $obj = Db::name($this->table)->alias('u');
        if(!$where){
            return false;
        }

        if($join){
            $obj = $obj->join($join);
        }

        return $obj->where($where)->field($field)->find();
    }


    /**
     * 根据大唐UID同步更新个人资料
     * @param $info
     * @param $dttxId
     * @return bool|int|string
     */
    public function synchronizeDttxInfo($dttxdata,$logindata){
        if (!is_array($dttxdata) || empty($logindata)){
            return false;
        }
        $updatedata['u_dttx_uid']=$dttxdata['u_id'];
        $updatedata['u_type']=$dttxdata['u_type'];
        $updatedata['u_nick']=$dttxdata['u_nick'];
        $updatedata['u_logo']=$dttxdata['u_logo'];
        if ($dttxdata['u_type']==1){
            $updatedata['u_name']=$dttxdata['u_comLegalName'];
        }else{
            $updatedata['u_name']=$dttxdata['u_name'];
        }
        $updatedata['u_tel']=$dttxdata['u_tel'];
        $updatedata['u_state']=$dttxdata['u_state'];
        $updatedata['u_level']=$dttxdata['u_level'];
        $updatedata['u_fCode']=$dttxdata['u_fCode'];
        $updatedata['u_code']=$dttxdata['u_code'];
        $updatedata['u_auth']=$dttxdata['u_auth'];
        $updatedata['u_createTime']=$dttxdata['u_createTime'];
        ksort($dttxdata);
        $userinfoCrcCode =getSuperMD5(implode('--',$updatedata));
        $updatedata['u_crc']=$userinfoCrcCode;
        unset($updatedata['u_dttx_uid']);
        unset($updatedata['u_code']);
        if ($logindata['u_crc']!==$userinfoCrcCode){
            $res = Db::name($this->name)->where(['u_id'=>$logindata['u_id']])->update($updatedata);
            return $res;
        }
    }

    /**
     * 更新登录信息
     * @param $uid
     * @return int|string
     */
    public function updateLastLoginInState($uid){
        $logininfo=[
            'u_logCount'=>['exp','u_logCount+1'],
            'u_logTime'=>mytime(),
            'u_logIp'=>Request::instance()->ip()
        ];
        $res = Db::name($this->name)->where(['u_id'=>$uid])->update($logininfo);
        return $res;
    }

    /**
     * 添加初始化新大唐用户信息(getUserinfo.json)
     * @param $dttxdata
     * @param $platformId
     * @param int $fcode
     * @return bool|string
     */
    public function activeDttxUser($dttxdata,$platformId,$fcode=0){
        if (!is_array($dttxdata) || empty($platformId)){
            return ajaxCallBack(300,'参数错误!');
        }
        //查找user表是否存在信息
        $data = Db::name($this->name)->where(['u_dttx_uid'=>$dttxdata['u_id']])->find();
        //查找附属表是否存在信息
        $dataplatfrom = Db::name('user_platform')->where(['up_dttx_uid'=>$dttxdata['u_id'],'up_plateform_id'=>$platformId])->find();

        if (!empty($data) && !empty($dataplatfrom)){
            return ajaxCallBack(300,'该用户已存在!');
        }

        //封装接口获取信息
        $userinfo =[
            'u_dttx_uid'=>$dttxdata['u_id'],
            'u_type'=>$dttxdata['u_type'],
            'u_nick'=>$dttxdata['u_nick'],
            'u_logo'=>$dttxdata['u_logo'],
            'u_name'=>$dttxdata['u_name'],
            'u_tel'=>$dttxdata['u_tel'],
            'u_level'=>$dttxdata['u_level'],
            'u_state'=>$dttxdata['u_state'],
            'u_createTime'=>strtotime($dttxdata['u_createTime']),
            'u_code'=>$dttxdata['u_code'],
            'u_fCode'=>$dttxdata['u_fCode'],
            'u_auth'=>$dttxdata['u_auth']
        ];
        ksort($userinfo);
        $userinfoCrcCode =getSuperMD5(implode('--',$userinfo)); //生成crc校验码
        Db::startTrans();
        try{
                //存在公共信息，二次激活用户
                if (!empty($data) && empty($dataplatfrom)){
                        $userPlatform =[
                            'up_plateform_id'=>$platformId,
                            'up_fcode'=>$fcode,
                            'up_uid'=>$data['u_id'],
                            'up_unick'=>$dttxdata['u_nick'],
                            'up_create_time'=>time(),
                            'up_dttx_uid'=>$dttxdata['u_id']
                        ];
                    Db::name('user_platform')->insert($userPlatform);
                    $lastid =Db::name('user_platform')->getLastInsID();
                    if (!$lastid){
                        Logs::writeMongodb(500010,'db_user_platform',$dttxdata['u_nick'],'创建用户附表记录失败一',$userPlatform);
                        throw new Exception('创建用户附表记录失败');
                    }
                    $account =[
                        'a_uid'  =>$lastid,
                        'a_platform_id'=>$platformId,
                        'a_dttx_uid'=>$dttxdata['u_id'],
                        'a_nick'=>$dttxdata['u_nick'],
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
                    $string =implode('--',$account);
                    $account['a_crc']=getSupersha1($string);
                    Db::name('account')->insert($account);
                    $accountlastid =Db::name('account')->getLastInsID();
                    if (!$accountlastid){
                        Logs::writeMongodb(500010,'db_account',$dttxdata['u_nick'],'账户记录创建失败一',$account);
                        throw new Exception('账户记录创建失败!');
                    }
                }elseif (empty($data) && empty($dataplatfrom)) {
                    $userinfo['u_crc'] = $userinfoCrcCode;
                    $userinfo['u_activeTime'] = mytime();
                    Db::name('user')->insert($userinfo);
                    $userid = Db::name('user')->getLastInsID();
                    $userPlatform = [
                        'up_plateform_id' => $platformId,
                        'up_fcode' => $fcode,
                        'up_uid' => $userid,
                        'up_unick' => $dttxdata['u_nick'],
                        'up_create_time' => time(),
                        'up_dttx_uid' => $dttxdata['u_id']
                    ];
                    Db::name('user_platform')->insert($userPlatform);
                    $lastid = Db::name('user_platform')->getLastInsID();
                    if (!$lastid) {
                        Logs::writeMongodb(500010,'user_platform',$dttxdata['u_nick'],'创建用户附表记录失败二',$userPlatform);
                        throw new Exception('创建用户附表记录失败，请重试!');
                    }
                    $account = [
                        'a_uid' => $lastid,
                        'a_platform_id' => $platformId,
                        'a_dttx_uid' => $dttxdata['u_id'],
                        'a_nick' => $dttxdata['u_nick'],
                        'a_createTime' => time(),
                        'a_freeMoney' => 0,
                        'a_frozenMoney' => 0,
                        'a_score' => 0,
                        'a_totalMoney' => 0,
                        'a_agentMoney' => 0,
                        'a_vipMoney' => 0,
                        'a_tangBao' => 0,
                        'a_storeScore' => 0,
                        'a_scoreTotal' => 0,
                        'a_tangTotal' => 0
                    ];
                    ksort($account);
                    $string = implode('--', $account);
                    $account['a_crc'] = getSupersha1($string);
                    Db::name('account')->insert($account);
                    $accountlastid = Db::name('account')->getLastInsID();
                    if (!$accountlastid) {
                        Logs::writeMongodb(500010,'db_account',$dttxdata['u_nick'],'账户记录创建失败二',$account);
                        throw new Exception('账户记录创建失败，请重试!');
                    }
                }
            Db::commit();
            Logs::writeMongodb(500011,'',$dttxdata['u_nick'],'新账户记录创建成功',$account);
            return ajaxCallBack(200,'插入数据成功!');
        }catch (\Exception $e){
            Db::rollback();
            return ajaxCallBack(300,$e->getMessage());
        }
    }


    /**
     * 获取大唐用户信息初始化资料(login.json)
     * @param $dttxdata
     * @param $platformId
     * @param int $fcode
     * @return bool|string
     */
    public function createUserByDttxLoginUserInfo($dttxdata,$platformId,$fcode=0){
        if (!is_array($dttxdata) || empty($platformId)){
            return ajaxCallBack(300,'参数错误!');
        }
        //查找user表是否存在信息
        $data = Db::name($this->name)->where(['u_dttx_uid'=>$dttxdata['u_id']])->find();
        //查找附属表是否存在信息
        $dataplatfrom = Db::name('user_platform')->where(['up_dttx_uid'=>$dttxdata['u_id'],'up_plateform_id'=>$platformId])->find();

        if (!empty($data) && !empty($dataplatfrom)){
            return ajaxCallBack(300,'该用户已存在!');
        }

        //如果没有指定推荐人则归属项目管理人
        if (empty($fcode)){
            $platform = Db::name('platform')->where(['pl_id'=>$platformId])->find();
            if (!empty($platform)){
                $fcode= $platform['pl_dttx_code'];
            }
        }
        //封装接口获取信息
        $userinfo =[
            'u_dttx_uid'=>$dttxdata['u_id'],
            'u_type'=>$dttxdata['u_type'],
            'u_nick'=>$dttxdata['u_nick'],
            'u_logo'=>$dttxdata['u_logo'],
            'u_name'=>$dttxdata['u_name'],
            'u_tel'=>$dttxdata['u_tel'],
            'u_level'=>$dttxdata['u_level'],
            'u_state'=>$dttxdata['u_state'],
            'u_createTime'=>$dttxdata['u_createTime'],
            'u_code'=>$dttxdata['u_code'],
            'u_fCode'=>$dttxdata['u_fCode'],
            'u_auth'=>$dttxdata['u_auth']
        ];
        ksort($userinfo);
        $userinfoCrcCode =getSuperMD5(implode('--',$userinfo)); //生成crc校验码
        if (!empty($data) && empty($dataplatfrom)){
                $userPlatform =[
                    'up_plateform_id'=>$platformId,
                    'up_fcode'=>$fcode,
                    'up_uid'=>$data['u_id'],
                    'up_create_time'=>time(),
                    'up_dttx_uid'=>$dttxdata['u_id']
                ];
            Db::name('user_platform')->insert($userPlatform);
            $lastid =Db::name('user_platform')->getLastInsID();
            if (!$lastid){
                return ajaxCallBack(300,'附表插入数据失败!');
            }
            return ajaxCallBack(200,'插入附表数据成功!');
        }elseif (empty($data) && empty($dataplatfrom)){
            Db::startTrans();
            try{
                $userinfo['u_crc']=$userinfoCrcCode;
                $userinfo['u_activeTime']=mytime();
                Db::name('user')->insert($userinfo);
                $userid =Db::name('user')->getLastInsID();
                $userPlatform =[
                    'up_plateform_id'=>$platformId,
                    'up_fcode'=>$fcode,
                    'up_uid'=>$userid,
                    'up_create_time'=>time(),
                    'up_dttx_uid'=>$dttxdata['u_id']
                ];
                Db::name('user_platform')->insert($userPlatform);
                Db::commit();
                return ajaxCallBack('200','插入数据成功!');
            }catch (\Exception $e){
                Db::rollback();
                return ajaxCallBack(300,'插入数据失败!');
            }
        }
    }

    /*
     * 通过大唐账号或者手机号码 获取一条用户记录
     * */
    public function getUserByNickOrTel($dttxnick, $field = '*', $join = array(), $isHide = false){

        if(!$dttxnick){
            return false;
        }

        if(preg_match('/^1[34578]\d{9}$/', $dttxnick)){
            $where['u.u_tel']  = $dttxnick;
        }else{
            $where['u.u_nick'] = $dttxnick;
        }

        $user = Db::name('user u')->where($where)->field($field)->join($join)->find();

        if(!$user){
            return false;
        }

        if($isHide){
            $user['nick'] = self::hidNickName($user['nick']);
            $user['recommendNick'] = self::hidNickName($user['recommendNick']);
            $user['tel'] = hidtel($user['tel']);
            $user['recommendTel'] = hidtel($user['recommendTel']);
        }

        return $user;
    }

    //中文名字隐藏中间或最后文字
    public function hidNickName($name){
        $length = mb_strlen($name, 'utf-8');
        $chars  = array();
        for($i=0; $i < $length; $i ++){
            if(($length == 2 || $length == 3) && $i == 1){
                $chars[$i] = '*';
            }else if($length >= 4 && ($i == 1 || $i == 2)){
                $chars[$i] = '*';
            }else{
                $chars[$i] = mb_substr($name, $i, 1, 'utf-8');
            }
        }

        return implode('', $chars);
    }

    /**
     * 根据推广码获取用户信息
     * @param $code
     * @param string $field
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function getUserinfoByCode($code,$field="*"){

        if (empty($code)){
            return false;
        }

        $res =Db::name($this->name)->field($field)->where(['u_code'=>$code])->find();

        return $res;


    }

    public function getProfile($user){
        if(!$user || !is_array($user)){
            return false;
        }

        $field = 'u.u_id as id,u.u_name as username,u.u_nick as nickname,u.u_tel as tel,du.u_nick as recommendNick,ar.a_name as province,au.a_name as city,u.u_auth as auth';
        $where = array('up.up_id' => $user['userId'], 'up.up_plateform_id' => $user['platformId']);
        $join  = [
            ['db_user_platform up', 'up.up_uid =u.u_id', 'LEFT'],
            ['db_user du', 'du.u_code=up.up_fcode', 'LEFT'],
            ['db_area ar', 'up.up_provinceId=ar.a_id', 'LEFT'],
            ['db_area au', 'up.up_cityId=au.a_id', 'LEFT'],
        ];

        return self::getUserOne($field, $where, $join);
    }



}