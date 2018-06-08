<?php
namespace app\payment\model;
use app\common\tools\Logs;
use think\Db;
use think\Exception;
use think\Model;
use think\Session;

class Account extends Model
{
    protected $name ='account';
    private $debug =false;

    public function findAccountList($input){

        $admin_identify =Session::has('admin_identify');
        $map=[];
        $map['a_isTest']=0;

        if (Session::has('admin_userid')){
            $platformId = isset($input['platformId'])?$input['platformId']:"0";
        }else{
            $platformId =Session::get('user.platformId');
        }

        $map['a_platform_id']=$platformId;
        if (!empty($input['unick'])){
            $map['a_nick'] =$input['unick'];
        }
        if (!empty($input['uname'])){
            $map['u_name']=$input['uname'];
        }

        if (!empty($input['utel'])){
            $map['u_tel']=$input['utel'];
        }

        if ($input['ustates']!==''){
            $map['a_state']=$input['ustates'];
        }

        $pagesize =empty($input['pageSize'])?"30":intval($input['pageSize']);
        $page =empty($input['pageCurrent'])?1:intval($input['pageCurrent']);
        $order =$input['orderField'] .' '.$input['orderDirection'];

        $data['list'] = Db::name($this->name)->alias('a')->field('u.u_nick,u.u_tel,u.u_name,a.*')->join('db_user u','a.a_dttx_uid=u.u_dttx_uid','left')->where($map)->page($page,$pagesize)->order($order)->fetchSql($this->debug)->select();

        $data['count']=Db::name($this->name)->alias('a')->join('db_user u','a.a_dttx_uid=u.u_dttx_uid','left')->where($map)->fetchSql($this->debug)->count();

        return $data;
    }

    /**
     * 根据账户和平台id查询账户详情
     * @param $acid
     */
    public function findDetailByaid($acid){
        if (empty($acid)){
            return false;
        }

        $res = Db::name($this->name)->where(['a_id'=>$acid])->find();
        return $res;
    }

    /**
     * 查找个人账户信息
     * @param $uid
     */
    public function findAccountByUid($uid){

        if (empty($uid)){
            return false;
        }
        $res =Db::name($this->name)->where(['a_uid'=>$uid])->find();
        if (empty($res)){
            return ['code'=>300,'data'=>'未找到相关账户信息!'];
        }
        if (!self::getVerifyAndOutCrcCode($res,false) || $res['a_states']==0){
            return ['code'=>300,'data'=>'账户异常，已锁定'];
        }

        return ['code'=>200,'data'=>$res];

    }


    /**
     * 查找个人账户信息
     * @param $uid
     */
    public function findAccountVerifyByAid($aid){

        if (empty($aid)){
            return false;
        }
        $res =Db::name($this->name)->where(['a_id'=>$aid])->find();
        if (empty($res)){
            return ['code'=>300,'data'=>'未找到相关账户信息!'];
        }
        if (!self::getVerifyAndOutCrcCode($res,false)){
            return ['code'=>300,'data'=>'账户异常，已锁定'];
        }

        return ['code'=>200,'data'=>$res];

    }

    /**
     * 计算及验证校验码
     * @param $data
     * @param bool $verify
     * @return bool|string
     */
    public static function getVerifyAndOutCrcCode($data,$getVerifyCode=true){

        $account =[
            'a_uid'  =>$data['a_uid'],
            'a_platform_id'=>$data['a_platform_id'],
            'a_dttx_uid'=>$data['a_dttx_uid'],
            'a_nick'=>$data['a_nick'],
            'a_createTime'=>$data['a_createTime'],
            'a_freeMoney'=>$data['a_freeMoney']+0,
            'a_frozenMoney'=>$data['a_frozenMoney']+0,
            'a_totalMoney'=>$data['a_totalMoney']+0,
            'a_vipMoney'=>$data['a_vipMoney']+0,
            'a_agentMoney'=>$data['a_agentMoney']+0,
            'a_score'=>$data['a_score']+0,
            'a_tangBao'=>$data['a_tangBao']+0,
            'a_storeScore'=>$data['a_storeScore']+0,
            'a_scoreTotal'=>$data['a_scoreTotal']+0,
            'a_tangTotal'=>$data['a_tangTotal']+0,
        ];
        ksort($account);
        $string =implode('--',$account);
        $crccode=getSupersha1($string);
        if (!$getVerifyCode){
            if ($data['a_crc']==$crccode){
                return true;
            }else{
                if ($data['a_states']==1){
                    Db::startTrans();
                    try{
                        $res1 = Db::name('account')->where(['a_id'=>$data['a_id']])->update(['a_states'=>0]);
                        if (!$res1){
                            throw new Exception('账号冻结失败!');
                        }
                        $stoplog=[
                            'as_acid'=>$data['a_id'],
                            'as_platform_id'=>$data['a_platform_id'],
                            'as_create_uid'=>0,
                            'as_message'=>'账户异常，系统自锁请联系客服人员解锁',
                            'as_type'=>1,
                            'as_create_time'=>mytime()
                        ];
                        Logs::writeMongodb(400800,'db_account',$data['a_id'],'CRC账号异常日志',$stoplog);
                        $log =Db::name('account_stoplog')->insert($stoplog);
                        if (!$log){
                            throw new Exception('冻结日志写入失败!');
                        }
                        Db::commit();
                    }catch (\Exception $e){
                        Db::rollback();
                    }
                }
                return false;
            }
        }else{
            return $crccode;
        }
    }

    /**
     * 增加删除账号余额
     * @param $accountId
     * @param $moneyNum
     * @param string $type  vip,agent
     * @$inc String add增加 | subtract 其他减少
     * @return \think\response\Json
     */
    public function changeAccountMoney($accountId,$moneyNum,$type='vip',$inc='add'){
        if (empty($accountId)||empty($moneyNum)){
            return ajaxCallBack(300,'参数错误!');
        }
        $acccount =$this->findAccountVerifyByAid($accountId);
        if ($acccount['code']==300){
            return ajaxCallBack(300,$acccount['data']);
        }
        $data=$acccount['data'];
    //    dump($data);
        if($inc=='add'){
            $data['a_freeMoney']=$data['a_freeMoney']+$moneyNum;
            $data['a_totalMoney']=$data['a_totalMoney']+$moneyNum;
            if ($type=='vip'){
                $data['a_vipMoney']=$data['a_vipMoney']+$moneyNum;
            }elseif ($type=='agent'){
                $data['a_agentMoney']=$data['a_agentMoney']+$moneyNum;
            }
        }else if ($inc=='subtract'){
            if ($data['a_states']==1){
                return ajaxCallBack(300,'账号冻结，禁止转出业务!');
            }
            $data['a_freeMoney']=$data['a_freeMoney']-$moneyNum;
        }

        $data['a_crc']=self::getVerifyAndOutCrcCode($data);
        unset($data['a_id']);
        $res = Db::name($this->name)->where(['a_id'=>$accountId])->update($data);

        if ($res){
            Logs::writeMongodb(400041,'db_account',$accountId,'账户余额增加成功',$data,'Ym');
            return ajaxCallBack(200,'操作成功!');
        }else{
            Logs::writeMongodb(400040,'db_account',$accountId,'账户余额增加失败',$data,'Ym');
            return ajaxCallBack(300,'操作失败!');
        }

    }




}