<?php
namespace app\payment\controller;
use app\common\controller\Platform;
use app\common\tools\Logs;
use app\payment\model\Account;
use app\payment\model\AccounTangbao;
use app\payment\model\AccountCashIn;

use app\payment\model\AccountCashTran;
use app\payment\model\AccountOrder;
use app\payment\model\PaytypeBusiness;
use app\payment\model\AccountPayIn;
use think\Db;
use think\Exception;
use think\Request;
use think\Session;

/**
 *
 * User: lirong
 * Date: 2017/7/1
 * Time: 11:03
 */
class Finance extends Platform {

    /**
     * 账户查询
     */
    public function accountenquiry(){

        $input['unick']=input('post.unick','','trim');
        $input['uname']=input('post.uname','','trim');
        $input['utel']=input('post.utel','','trim');
        $input['ustates']=input('post.states','','intval');
        $input['pageSize'] =input('post.pageSize','30','trim');
        $input['pageCurrent'] =input('post.pageCurrent','1','intval');
        $input['orderField'] =input('post.orderField','a_id','trim');
        $input['orderDirection'] =input('post.orderDirection','desc','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');
        $account =new Account();
        $data =$account->findAccountList($input);
        $this->assign('data',$data);
        $this->assign('input',$input);
        return $this->fetch();
    }


    /**
     * 账户冻结
     * @return mixed
     */
    public function changestop(){

        if (Request::instance()->isPost()){
            $id=input('post.id','','intval');
            $platformId=input('post.platformId','','intval');
            $messae =input('post.message','','trim');

            if (empty($messae) || strlen($messae)<=5){
                $this->ajaxReturn(ajaxCallBack(300,'冻结原因不能少于5个字符!'));
            }
            $userid =empty(Session::get('user.userId'))?0:Session::get('user.userId');

            Db::startTrans();
            try{
                $acc =Db::name('account')->where(['a_id'=>$id])->update(['a_states'=>0,'a_memo'=>$messae]);
                if (!$acc){
                    throw new Exception('账号冻结失败');
                }
                $data=[
                    'as_acid'=>$id,
                    'as_platform_id'=>$platformId,
                    'as_create_uid'=>$userid,
                    'as_message'=>$messae,
                    'as_type'=>0,
                    'as_create_time'=>mytime()
                ];
                $stop =Db::name('account_stoplog')->insert($data);
                if (!$stop){
                    throw new Exception('账号冻结日志创建失败!');
                }
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200,'冻结成功',true,'payment_Finance_accountenquiry'));
            }catch (\Exception $e){
                Db::rollback();
                Logs::writeMongodb(400050,'db_account',$id,'账号冻结失败',$e->getMessage());
                $this->ajaxReturn(ajaxCallBack(300,'冻结失败，请重试!'));
            }

        }else{
            $id =Request::instance()->param('id','0','intval');

            if (empty($id)){
                $this->ajaxReturn(ajaxCallBack(300,'参数错误，请重试！'));
            }

            $account =new Account();
            $res = $account->findDetailByaid($id);
            if (empty($res)){
                $this->ajaxReturn(ajaxCallBack(300,'您修改的数据不存在或已被删除，请刷新后重试!'));
            }

            if ($res['a_states']==0){
                $this->ajaxReturn(ajaxCallBack(300,'该账号已冻结，请勿重复操作!'));
            }

            $this->assign('data',$res);
            return $this->fetch();
        }

    }

    /*
     *分润冻结
     */
    public function orderstop(){

        if (Request::instance()->isPost()){

            $id =input('post.id','0');
            $messae =input('post.message','','trim');
            if (empty($id) || !is_numeric($id)){
                $this->ajaxReturn(ajaxCallBack(300,'参数错误'));
            }

            $before =Db::name('account_order')->where(['ao_id'=>$id])->lock(true)->find();
            if ($before['ao_state']==-1){
                $this->ajaxReturn(ajaxCallBack(300,'该订单已处理，请勿重复操作!'));
            }

            $res =Db::name('account_order')->where(['ao_id'=>$id])->update(['ao_state'=>-1,'ao_mem'=>$messae]);
            if ($res){
                $this->ajaxReturn(ajaxCallBack(200,'修改成功!',true,'payment_Finance_orderdetail'));
            }else{
                $this->ajaxReturn(ajaxCallBack(300,'修改失败，请确认订单是否已处理!'));
            }
            exit();
        }else{

            $id =Request::instance()->param('id','0');
            if (empty($id)){
                $this->ajaxReturn(ajaxCallBack(300,'参数错误，请重试！'));
            }

            $accountOrder =new AccountOrder();
            $res =$accountOrder->findDetailByAoid($id,'ao_id,ao_buy_uid,ao_state');

            if (empty($res)){
                $this->ajaxReturn(ajaxCallBack(300,'信息不存在或已被删除，请重试!'));
            }

            if ($res['ao_state']==-1 || $res['ao_state']==1){
                $this->ajaxReturn(ajaxCallBack(300,'订单已处理，请勿重复操作!'));
            }

            $this->assign('data',$res);
            return $this->fetch();
        }

    }


    /**
     * 账户解冻
     */
    public function changeopen(){
        $id =Request::instance()->param('id','0','intval');
        $platformId =Request::instance()->param('platformId','0','intval');
        if (empty($id)){
            $this->ajaxReturn(ajaxCallBack(200,'参数错误，请刷新后重试!'));
        }

        $res =Db::name('account')->where(['a_id'=>$id])->lock(true)->find();
        if ($res['a_states']==1){
            $this->ajaxReturn(ajaxCallBack(300,'账号已解冻，请勿重复操作!'));
        }
        Db::startTrans();
        try{
            $acc =Db::name('account')->where(['a_id'=>$id])->update(['a_states'=>1,'a_memo'=>'状态正常']);
            if (!$acc){
                throw new Exception('账号解冻失败!');
            }
            $stop =Db::name('account_stoplog')->insert([
                'as_acid'=>$id,
                'as_platform_id'=>$platformId,
                'as_create_uid'=>!empty(Session::get('user.userId'))?Session::get('user.userId'):0,
                'as_message'=>'账户解冻!',
                'as_type'=>1,
                'as_create_time'=>mytime()
            ]);
            if (!$stop){
                throw new Exception('解冻日志创建失败!');
            }
            Db::commit();
            $this->ajaxReturn(ajaxCallBack(200,'解冻成功!'));
        }catch (Exception $e){
            Db::rollback();
            Logs::writeMongodb(400050,'db_account',$id,'账户解冻失败',$e->getMessage());
            $this->ajaxReturn(ajaxCallBack(300,'解冻失败,请重试!'));
        }


    }

    /**
     * 账户明细
     */
    public function accountdetails(){

        $input['ipt_nick']=input('post.ipt_nick','','trim');
        $input['ipt_caid']=input('post.caid','','trim');
        $input['ipt_aid']=Request::instance()->param('aid','','trim');
        $input['ipt_order_id']=input('ipt_order_id','','trim');
        $input['beginDate']=input('post.beginDate','','trim');
        $input['endDate']=input('post.endDate','','trim');

        $input['ipt_type']=input('post.ipt_type','','intval');
        $input['pageSize'] =input('post.pageSize','30','trim');
        $input['pageCurrent'] =input('post.pageCurrent','1','intval');
        $input['orderField'] =input('post.orderField','ca_create_time','trim');
        $input['orderDirection'] =input('post.orderDirection','desc','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');
        $accountCashTran =new AccountCashTran();
        $res =$accountCashTran->findAccountCashTranList($input);
        $this->assign('input',$input);
        $this->assign('data',$res);
        return $this->fetch();
    }


    /**
     * 订单分润明细
     */
    public function orderdetail(){

        $input['ipt_nick']=input('post.ipt_nick','','trim');
        $input['ipt_order_id']=input('post.ipt_order_id','','trim');
        $input['ipt_aoid']=input('post.ipt_aoid','','trim');
        $input['ipt_state']=input('post.ipt_state','','trim');
        $input['ipt_moneyMin']=input('post.ipt_moneyMin','','trim');
        $input['ipt_moneyMax']=input('post.ipt_moneyMax','','trim');
        $input['beginDate']=input('post.beginDate','','trim');
        $input['endDate']=input('post.endDate','','trim');
        $input['pageSize'] =input('post.pageSize','30','trim');
        $input['pageCurrent'] =input('post.pageCurrent','1','intval');
        $input['orderField'] =input('post.orderField','ao_create_time','trim');
        $input['orderDirection'] =input('post.orderDirection','desc','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');


        $accountOrder =new AccountOrder();
        $data = $accountOrder->findAccountOrderList($input);
    //    print_r($data) ;
        $this->assign('input',$input);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 唐宝对账
     */
    public function tangbaoaccount(){


        $input['ipt_nick']=input('post.ipt_nick','','trim');
        $input['ipt_order_id']=input('post.ipt_order_id','','trim');
        $input['ipt_atid']=input('post.ipt_atid','','trim');

        $input['ipt_ischecked']=input('post.ipt_ischecked','0','trim');
        $input['ipt_moneyMin']=input('post.ipt_moneyMin','','trim');
        $input['ipt_moneyMax']=input('post.ipt_moneyMax','','trim');
        $input['pay_beginDate']=input('post.pay_beginDate','','trim');
        $input['pay_endDate']=input('post.pay_endDate','','trim');

        $input['reach_beginDate']=input('post.reach_beginDate','','trim');
        $input['reach_endDate']=input('post.reach_endDate','','trim');

        $input['pageSize'] =input('post.pageSize','30','trim');
        $input['pageCurrent'] =input('post.pageCurrent','1','intval');
        $input['orderField'] =input('post.orderField','at_create_time','trim');
        $input['orderDirection'] =input('post.orderDirection','desc','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');
        $accountTangbao =new AccounTangbao();
        $data = $accountTangbao->findAccountTangbao($input);

        $this->assign('input',$input);
        $this->assign('data',$data);
        return $this->fetch();

    }

    public function taobaoconfirm(){

        $id =input('id','0');
        if (empty($id) || !is_numeric($id)){
            $this->ajaxReturn(ajaxCallBack(300,'参数错误，请刷新后重试!'));
        }
        $userid =Session::get('user.userId');
        $nick =Session::get('user.username');
        $res =Db::name('account_tangbao')->where(['at_id'=>$id])->update(['at_platform_state'=>1,'at_is_checked'=>1,'at_oper_upid'=>$userid,'at_oper_nick'=>$nick,'at_finshtime'=>mytime()]);

        if ($res){
            $this->ajaxReturn(ajaxCallBack(200,'操作成功!'));
        }else{
            $this->ajaxReturn(ajaxCallBack(300,'操作失败,请重试!'));
        }

    }


    /**
     * 充值进账查询
     */
    public function accountcashin(){


        $input['ipt_nick']=input('post.ipt_nick','','trim');
        $input['ipt_caid']=input('post.ipt_caid','','trim');
        $input['ipt_state']=input('post.ipt_state','','intval');
        $input['ipt_payType']=input('post.ipt_payType','','intval');

        $input['ipt_moneyMin']=input('post.ipt_moneyMin','','trim');
        $input['ipt_moneyMax']=input('post.ipt_moneyMax','','trim');

        $input['apply_beginDate']=input('post.apply_beginDate','','trim');
        $input['apply_endDate']=input('post.apply_endDate','','trim');
        $input['reach_beginDate']=input('post.reach_beginDate','','trim');
        $input['reach_endDate']=input('post.reach_endDate','','trim');


        $input['pageSize'] =input('post.pageSize','30','trim');
        $input['pageCurrent'] =input('post.pageCurrent','1','intval');
        $input['orderField'] =input('post.orderField','ci_createTime','trim');
        $input['orderDirection'] =input('post.orderDirection','desc','trim');
        $input['platformId']=input('post.platformId',$this->platformId,'intval');


        $accountCashIn =new AccountCashIn();
        $res =$accountCashIn->findAccountCashInList($input);

        $paytypeBusiness =new PaytypeBusiness();
        $paydata = $paytypeBusiness->findAllPayTypeList("pb_paytype,pb_name");


        $this->assign('paydata',$paydata);
        $this->assign('input',$input);
        $this->assign('data',$res);
        return $this->fetch();

    }

    /**
     * 进账管理
     * */
    public function income(){
        $input['ipt_nick']       =input('post.ipt_nick', '', 'trim');
        $input['ipt_channel_id'] =input('post.ipt_channel_id', '', 'trim');
        $input['ipt_order_id'] =  input('post.ipt_order_id', '', 'trim');
        $input['ipt_state']      =input('post.ipt_state', '');
        $input['ipt_payType']    =input('post.ipt_payType', '', 'intval');

        $input['ipt_moneyMin']   =input('post.ipt_moneyMin', '', 'trim');
        $input['ipt_moneyMax']   =input('post.ipt_moneyMax', '', 'trim');

        $input['apply_beginDate']=input('post.apply_beginDate', '', 'trim');
        $input['apply_endDate']  =input('post.apply_endDate', '', 'trim');
        $input['reach_beginDate']=input('post.reach_beginDate', '', 'trim');
        $input['reach_endDate']  =input('post.reach_endDate', '', 'trim');


        $input['pageSize']       = input('post.pageSize', '30', 'trim');
        $input['pageCurrent']    = input('post.pageCurrent', '1', 'intval');
        $input['orderField']     = input('post.orderField', 'ap_create_time','trim');
        $input['orderDirection'] = input('post.orderDirection', 'desc', 'trim');
        $input['platformId']     = input('post.platformId',$this->platformId,'intval');

        $statlist =get_dict(4);
        unset($statlist[0]);

        $input['stateList']   =$statlist;
        $accountPayIn = new AccountPayIn();
        $result       = $accountPayIn->findAccountPayInList($input);

        $this->assign('input', $input);
        $this->assign('data',  $result);
        return $this->fetch();
    }

    /**
     * 用于首页数据展示
     * @return mixed
     */
    public function index(){}

    /**
     * 增加数据
     * @return mixed
     */
    public function create(){
    }

    /**
     *修改数据
     * @return mixed
     */
    public function edit(){}

    /**
     * 移除数据
     * @return mixed
     */
    public function remove(){}
}