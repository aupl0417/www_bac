<?php

namespace app\payment\controller;

use app\common\controller\Platform;
use app\common\tools\Logs;
use app\payment\model\Account;
use app\payment\model\AccountOrder;
use app\payment\model\AccountOrderTran;
use think\Db;
use think\Exception;
use think\Log;
use think\Request;
use think\Session;

/**
 * 支付订单金额分润计算
 * User: lirong
 * Date: 2017/7/13
 * Time: 10:46
 */
class Order extends Platform
{

    /**
     * 分润明细表
     * @return mixed
     */
    public function index(){
        $input['ipt_nick']=input('post.ipt_nick','','trim');
        $input['ipt_order_id']=input('post.ipt_order_id','','trim');
        $input['ipt_aoid']=input('post.ipt_aoid','','trim');
        $input['ipt_state']=input('post.ipt_state','0','trim');
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
     * 分润驳回
     * @return mixed
     */
    public function orderstop(){

        if (Request::instance()->isPost()){

            $id =input('post.id','0');
            $messae =input('post.message','','trim');
            $orderid =input('post.orderid','0');
            if (empty($id) || !is_numeric($id)){
                $this->ajaxReturn(ajaxCallBack(300,'参数错误'));
            }
            Db::startTrans();
            try{
                $order =Db::name('account_order')->where(['ao_id'=>$id])->update(['ao_state'=>-1,'ao_mem'=>$messae]);
                if (!$order){
                    throw new Exception('分润订单表状态更新失败，请重试!');
                }

                $tasklist =Db::name('order_tasklist')->where(['ot_id'=>$orderid])->update(['ot_state'=>2,'ot_update_time'=>mytime()]);
                if (!$tasklist){
                    throw new Exception('队列状态更新失败，请重试!');
                }
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200,'驳回成功!',true,'payment_Order_index'));
            }catch (\Exception $e){
                $this->ajaxReturn(ajaxCallBack(300,'驳回失败，'.$e->getMessage()));
            }
        }else {

            $id = Request::instance()->param('id', '0');
            if (empty($id)) {
                $this->ajaxReturn(ajaxCallBack(300, '参数错误，请重试！'));
            }

            $accountOrder = new AccountOrder();
            $res = $accountOrder->findDetailByAoid($id, 'ao_id,ao_buy_uid,ao_state,ao_order_id');

            if (empty($res)) {
                $this->ajaxReturn(ajaxCallBack(300, '信息不存在或已被删除，请重试!'));
            }

            if ($res['ao_state'] == -1 || $res['ao_state'] == 1) {
                $this->ajaxReturn(ajaxCallBack(300, '订单已处理，请勿重复操作!'));
            }

            $this->assign('data', $res);
            return $this->fetch();
        }

    }

    /**
     * 分润计算明细
     */
    public function calcdetail(){

        $id =Request::instance()->param('id','0');
        if (empty($id) || !is_numeric($id)){
            return $this->ajaxReturn(ajaxCallBack(300,'参数错误,请刷新页面后重试'));
        }

        $accountOrderTran =new AccountOrderTran();
        $res = $accountOrderTran->findTranListByOrderId($id);

        if (false===$res){
            $this->ajaxReturn(ajaxCallBack(300,'为找到该订单分润记录!'));
        }

        $this->assign('data',$res);
        return $this->fetch();
    }

    //手动分润计算
    public function calcOrder()
    {

        $orderid = Request::instance()->param('id', '0');

        if (empty($orderid) && is_numeric($orderid)) {
            $this->ajaxReturn(ajaxCallBack(300, '订单ID不能为空!'));
        }
        //订单数据
        $orders = Db::name('orders')->field('os_id,os_order_price,os_actual_payprice,os_status')->where(['os_id' => $orderid])->find();
        if ($orders['os_status'] == 3) {
            //入账资金数据
            $account_order_payin = Db::name('account_pay_in')->where(['ap_shop_order_id' => $orderid])->find();
            if (empty($account_order_payin)) {
                $this->ajaxReturn(ajaxCallBack(300, '没有该订单支付记录,请确认该订单是否已支付!'));
            }
            //分润订单数据
            $account_order_data = Db::name('account_order')->where(['ao_order_id' => $orderid])->lock(true)->find();
            if (empty($account_order_data)) {
                $this->ajaxReturn(ajaxCallBack(300, '分润订单记录不存在,请确认!'));
            }

            if ($account_order_data['ao_state']==1){
                $this->ajaxReturn(ajaxCallBack(300, '该订单已处理,请勿重复操作!'));
            }


            if (($orders['os_actual_payprice'] != $account_order_data['ao_money']) || ($account_order_payin['ap_pay_money'] != $account_order_data['ao_money'])) {
                $this->ajaxReturn(ajaxCallBack(300, '订单金额异常,请确认订单金额与入账金额是否一致!'));
            }
            $operatorid =Session::get('user.userId');
            $account = new Account();

            Db::startTrans();
            try {
                //一级收益
                if (!empty($account_order_data['ao_onelevel_uid'])) {
                    $one_account = $account->findAccountByUid($account_order_data['ao_onelevel_uid']);
                    if ($one_account['code'] == 200) {
                        $onedata = [
                            'ad_id' => getTimeMarkID(),
                            'ad_platfrom_id' => $account_order_data['ao_platform_id'],
                            'ad_order_id' => $orderid,
                            'ad_acid' => $one_account['data']['a_id'],
                            'ad_upid' => $account_order_data['ao_onelevel_uid'],
                            'ad_nick' => $account_order_data['ao_onelevel_nick'],
                            'ad_type' => 2,
                            'ad_order_money' => $account_order_data['ao_money'],
                            'ad_money' => $account_order_data['ao_onelevel_money'],
                            'ad_ratio' => $account_order_data['ao_one_ratio'],
                            'ad_remark' => '推广分润收益',
                            'ad_create_time' => time()
                        ];
                        $one_tran = Db::name('account_order_tran')->insert($onedata);
                        if (!$one_tran) {
                            Logs::writeMongodb(800010,'db_account_order_tran',$orderid,'一级订单分润表创建失败',$onedata,'Ym');
                             throw new Exception('一级订单分润表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_order_tran',$orderid,'一级订单分润表创建成功',$onedata,'Ym');
                        }
                        $one_tran =[
                            'ca_id' => getTimeMarkID(),
                            'ca_uid' => $account_order_data['ao_onelevel_uid'],
                            'ca_unick' => $account_order_data['ao_onelevel_nick'],
                            'ca_platform_id' => $account_order_data['ao_platform_id'],
                            'ca_aid' => $one_account['data']['a_id'],
                            'ca_money' => $account_order_data['ao_onelevel_money'],
                            'ca_order_id' => $orderid,
                            'ca_balance' => $one_account['data']['a_freeMoney'] + $account_order_data['ao_onelevel_money'],
                            'ca_balance_type' => 2,
                            'ca_memo' => '推广分润收益',
                            'ca_type' => 1,
                            'ca_create_time' => mytime()
                        ];
                        $one_case = Db::name('account_cash_tran')->insert($one_tran);
                        if (!$one_case) {
                            Logs::writeMongodb(800010,'db_account_cash_tran',$orderid,'一级资金异动表创建失败',$one_tran,'Ym');
                            throw new Exception('一级资金异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_cash_tran',$orderid,'一级资金异动表创建成功',$one_tran,'Ym');
                        }
                        $oneresult = $account->changeAccountMoney($one_account['data']['a_id'], $account_order_data['ao_onelevel_money'], 'vip', 'add');
                        if ($oneresult['statusCode'] == 300) {
                            Logs::writeMongodb(800010,'db_account',$orderid,'会员' . $account_order_data['ao_onelevel_uid'] . '账号:' . $one_account['data']['a_id'] . 'message:' . $oneresult['message'],$oneresult,'Ym');
                            throw new Exception($one_account['data']['a_id'].$oneresult['message']);
                            //补日志记录
                        }else{
                            Logs::writeMongodb(800011,'db_account',$orderid,'会员' . $account_order_data['ao_onelevel_uid'] . '账号:' . $one_account['data']['a_id'] . 'message:' . $oneresult['message'],$oneresult,'Ym');
                        }

                    } else {
                        //补日志记录
                        Logs::writeMongodb(800010,'db_account',$orderid,'账号查询:' . $account_order_data['ao_onelevel_uid'] . $one_account['data'],$one_account,'Ym');
                        throw new Exception($account_order_data['ao_onelevel_nick'].$one_account['data']);
                    }
                }
                // 二级会员收益
                if (!empty($account_order_data['ao_twolevel_uid'])) {
                    $two_account = $account->findAccountByUid($account_order_data['ao_twolevel_uid']);
                    if ($two_account['code'] == 200) {

                        $twodata = [
                            'ad_id' => getTimeMarkID(),
                            'ad_platfrom_id' => $account_order_data['ao_platform_id'],
                            'ad_order_id' => $orderid,
                            'ad_acid' => $two_account['data']['a_id'],
                            'ad_upid' => $account_order_data['ao_twolevel_uid'],
                            'ad_nick' => $account_order_data['ao_twolevel_nick'],
                            'ad_type' => 2,
                            'ad_order_money' => $account_order_data['ao_money'],
                            'ad_money' => $account_order_data['ao_twolevel_money'],
                            'ad_ratio' => $account_order_data['ao_two_ratio'],
                            'ad_remark' => '推广分润收益',
                            'ad_create_time' => time()
                        ];
                        $two_res =Db::name('account_order_tran')->insert($twodata);
                        if (!$two_res) {
                            Logs::writeMongodb(800010,'db_account_order_tran',$orderid,'二级订单异动表创建失败',$twodata,'Ym');
                            throw new Exception('二级订单异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_order_tran',$orderid,'二级订单异动表创建成功',$twodata,'Ym');
                        }
                        $two_tran =[
                            'ca_id' => getTimeMarkID(),
                            'ca_uid' => $account_order_data['ao_twolevel_uid'],
                            'ca_unick' => $account_order_data['ao_twolevel_nick'],
                            'ca_platform_id' => $account_order_data['ao_platform_id'],
                            'ca_aid' => $two_account['data']['a_id'],
                            'ca_money' => $account_order_data['ao_twolevel_money'],
                            'ca_order_id' => $orderid,
                            'ca_balance' => $two_account['data']['a_freeMoney'] + $account_order_data['ao_twolevel_money'],
                            'ca_balance_type' => 2,
                            'ca_memo' => '推广分润收益',
                            'ca_type' => 1,
                            'ca_create_time' => mytime()
                        ];
                        $two_case = Db::name('account_cash_tran')->insert($two_tran);
                        if (!$two_case) {
                            Logs::writeMongodb(800010,'db_account_cash_tran',$orderid,'二级资金异动表创建失败',$two_tran,'Ym');
                            throw new Exception('二级资金异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_cash_tran',$orderid,'二级资金异动表创建成功',$two_tran,'Ym');
                        }
                        $tworesult = $account->changeAccountMoney($two_account['data']['a_id'], $account_order_data['ao_twolevel_money'], 'vip', 'add');
                        if ($tworesult['statusCode'] == 300) {
                            //补日志记录
                            Logs::writeMongodb(800010,'db_account',$orderid,'会员' . $account_order_data['ao_twolevel_uid'] . '账号:' . $two_account['data']['a_id'] . 'message:' . $tworesult['message'],$tworesult,'Ym');
                            throw new Exception($account_order_data['ao_twolevel_nick']."账户".$tworesult['message']);

                        }else{
                            Logs::writeMongodb(800011,'db_account',$orderid,'会员' . $account_order_data['ao_twolevel_uid'] . '账号:' . $two_account['data']['a_id'] . 'message:' . $tworesult['message'],$tworesult,'Ym');
                        }
                    } else {
                        //补日志记录
                        Logs::writeMongodb(800010,'db_account',$orderid,'账号查询:' . $account_order_data['ao_twolevel_uid'] . $two_account['data'],$two_account,'Ym');
                        throw new Exception($account_order_data['ao_twolevel_nick'].$two_account['data']);
                    }
                }
                //省代收益计算
                if (!empty($account_order_data['ao_province_uid'])) {
                    $province_data = $account->findAccountByUid($account_order_data['ao_province_uid']);
                    if ($province_data['code'] == 200) {
                        $provdata = [
                            'ad_id' => getTimeMarkID(),
                            'ad_platfrom_id' => $account_order_data['ao_platform_id'],
                            'ad_order_id' => $orderid,
                            'ad_acid' => $province_data['data']['a_id'],
                            'ad_upid' => $account_order_data['ao_province_uid'],
                            'ad_nick' => $account_order_data['ao_province_nick'],
                            'ad_type' => 1,
                            'ad_order_money' => $account_order_data['ao_money'],
                            'ad_money' => $account_order_data['ao_province_money'],
                            'ad_ratio' => $account_order_data['ao_province_ratio'],
                            'ad_remark' => '省代分润收益',
                            'ad_create_time' => time()
                        ];
                        $lastprovinceorder_tran = Db::name('account_order_tran')->insert($provdata);
                        if (!$lastprovinceorder_tran) {
                            Logs::writeMongodb(800010,'db_account_order_tran',$orderid,'省代订单分润异动表创建失败',$provdata,'Ym');
                            throw new Exception('省代订单分润异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_order_tran',$orderid,'省代订单分润异动表创建成功',$provdata,'Ym');
                        }
                        $province_tran =[
                            'ca_id' => getTimeMarkID(),
                            'ca_uid' => $account_order_data['ao_province_uid'],
                            'ca_unick' => $account_order_data['ao_province_nick'],
                            'ca_platform_id' => $account_order_data['ao_platform_id'],
                            'ca_aid' => $province_data['data']['a_id'],
                            'ca_money' => $account_order_data['ao_province_money'],
                            'ca_order_id' => $orderid,
                            'ca_balance' => $province_data['data']['a_freeMoney'] + $account_order_data['ao_province_money'],
                            'ca_balance_type' => 1,
                            'ca_memo' => '省代分润收益',
                            'ca_type' => 1,
                            'ca_create_time' => mytime()
                        ];
                        $province_case =Db::name('account_cash_tran')->insert($province_tran);

                        if (!$province_case) {
                            Logs::writeMongodb(800010,'db_account_cash_tran',$orderid,'省代资金异动表创建失败',$province_tran,'Ym');
                            throw new Exception('省代资金异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_cash_tran',$orderid,'省代资金异动表创建成功',$province_tran,'Ym');
                        }

                        $province_result = $account->changeAccountMoney($province_data['data']['a_id'], $account_order_data['ao_province_money'], 'agent', 'add');
                        if ($province_result['statusCode'] == 300) {
                            //补日志记录
                            //    echo $province_result['message'];
                            Logs::writeMongodb(800010,'db_account',$orderid,'会员' . $account_order_data['ao_province_uid'] . '账号:' . $province_data['data']['a_id'] . 'message:' . $province_result['message'],$province_result,'Ym');
                            throw new Exception($account_order_data['ao_twolevel_nick']."账户".$province_result['message']);

                        }else{
                            Logs::writeMongodb(800011,'db_account',$orderid,'会员' . $account_order_data['ao_province_uid'] . '账号:' . $province_data['data']['a_id'] . 'message:' . $province_result['message'],$province_result,'Ym');
                        }
                    } else {
                        //补日志记录
                        Logs::writeMongodb(800010,'db_account',$orderid,'账号查询:' . $account_order_data['ao_province_uid'] . $province_data['data'],$province_data,'Ym');
                        throw new Exception($account_order_data['ao_province_nick'].$province_data['data']);
                    }
                }
                //市代收益计算
                if (!empty($account_order_data['ao_city_uid'])) {
                    $city_data = $account->findAccountByUid($account_order_data['ao_city_uid']);
                    if ($city_data['code'] == 200) {
                        $citydata = [
                            'ad_id' => getTimeMarkID(),
                            'ad_platfrom_id' => $account_order_data['ao_platform_id'],
                            'ad_order_id' => $orderid,
                            'ad_acid' => $city_data['data']['a_id'],
                            'ad_upid' => $account_order_data['ao_city_uid'],
                            'ad_nick' => $account_order_data['ao_city_nick'],
                            'ad_type' => 1,
                            'ad_order_money' => $account_order_data['ao_money'],
                            'ad_money' => $account_order_data['ao_city_money'],
                            'ad_ratio' => $account_order_data['ao_city_ratio'],
                            'ad_remark' => '市代分润收益',
                            'ad_create_time' => time()
                        ];
                        $lastcity_data = Db::name('account_order_tran')->insert($citydata);
                        if (!$lastcity_data) {
                            Logs::writeMongodb(800010,'db_account_order_tran',$orderid,'市代订单分润异动表创建失败',$citydata,'Ym');
                            throw new Exception('市代订单分润异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_order_tran',$orderid,'市代订单分润异动表创建成功',$citydata,'Ym');
                        }
                        $city_tran=[
                            'ca_id' => getTimeMarkID(),
                            'ca_uid' => $account_order_data['ao_city_uid'],
                            'ca_unick' => $account_order_data['ao_city_nick'],
                            'ca_platform_id' => $account_order_data['ao_platform_id'],
                            'ca_aid' => $city_data['data']['a_id'],
                            'ca_money' => $account_order_data['ao_city_money'],
                            'ca_order_id' => $orderid,
                            'ca_balance' => $city_data['data']['a_freeMoney'] + $account_order_data['ao_city_money'],
                            'ca_balance_type' => 1,
                            'ca_memo' => '市代分润收益',
                            'ca_type' => 1,
                            'ca_create_time' => mytime()
                        ];
                        $lastcity_order_data = Db::name('account_cash_tran')->insert($city_tran);
                        if (!$lastcity_order_data) {
                            Logs::writeMongodb(800010,'db_account_cash_tran',$orderid,'市代资金订单分润异动表创建失败'.$orderid,$city_tran,'Ym');
                            throw new Exception('市代资金订单分润异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_cash_tran',$orderid,'市代资金订单分润异动表创建成功'.$orderid,$city_tran,'Ym');
                        }

                        $cityresult = $account->changeAccountMoney($city_data['data']['a_id'], $account_order_data['ao_city_money'], 'agent', 'add');
                        if ($cityresult['statusCode'] == 300) {
                            //补日志记录
                            Logs::writeMongodb(800010,'db_account',$orderid,'会员' . $account_order_data['ao_city_uid'] . '账号:' . $city_data['data']['a_id'] . 'message:' . $cityresult['message'],$cityresult,'Ym');
                            throw new Exception($account_order_data['ao_city_nick']."账户".$cityresult['message']);

                        }else{
                            Logs::writeMongodb(800011,'db_account',$orderid,'会员' . $account_order_data['ao_city_uid'] . '账号:' . $city_data['data']['a_id'] . 'message:' . $cityresult['message'],$cityresult,'Ym');
                        }
                    } else {
                        //补日志记录
                        Logs::writeMongodb(800010,'db_account',$orderid,'账号查询:' . $account_order_data['ao_city_uid'] . $city_data['data'],$city_data,'Ym');
                        throw new Exception($account_order_data['ao_city_nick'].$city_data['data']);
                    }
                }

                //大唐服务费收益
                if (!empty($account_order_data['ao_datang_uid'])) {
                    $datang_data = $account->findAccountByUid($account_order_data['ao_datang_uid']);
                    if ($datang_data['code'] == 200) {
                        $datangdata = [
                            'ad_id' => getTimeMarkID(),
                            'ad_platfrom_id' => $account_order_data['ao_platform_id'],
                            'ad_order_id' => $orderid,
                            'ad_acid' => $datang_data['data']['a_id'],
                            'ad_upid' => $account_order_data['ao_datang_uid'],
                            'ad_nick' => $account_order_data['ao_datang_nick'],
                            'ad_type' => 6,
                            'ad_order_money' => $account_order_data['ao_money'],
                            'ad_money' => $account_order_data['ao_datang_money'],
                            'ad_ratio' => $account_order_data['ao_datang_ratio'],
                            'ad_remark' => '大唐服务费分润收益',
                            'ad_create_time' => time()
                        ];
                        $lastdatang_data = Db::name('account_order_tran')->insert($datangdata);
                        if (!$lastdatang_data) {
                            Logs::writeMongodb(800010,'db_account_order_tran',$orderid,'大唐服务费订单分润异动表创建失败',$datangdata,'Ym');
                            throw new Exception('大唐服务费订单分润异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_order_tran',$orderid,'大唐服务费订单分润异动表创建成功',$datangdata,'Ym');
                        }
                        $datang_tran =[
                            'ca_id' => getTimeMarkID(),
                            'ca_uid' => $account_order_data['ao_datang_uid'],
                            'ca_unick' => $account_order_data['ao_datang_nick'],
                            'ca_platform_id' => $account_order_data['ao_platform_id'],
                            'ca_aid' => $datang_data['data']['a_id'],
                            'ca_money' => $account_order_data['ao_datang_money'],
                            'ca_order_id' => $orderid,
                            'ca_balance' => $datang_data['data']['a_freeMoney'] + $account_order_data['ao_datang_money'],
                            'ca_balance_type' => 6,
                            'ca_memo' => '大唐服务费分润收益',
                            'ca_type' => 1,
                            'ca_create_time' => mytime()
                        ];
                        $last_datang_data = Db::name('account_cash_tran')->insert($datang_tran);
                        if (!$last_datang_data) {
                            Logs::writeMongodb(800010,'db_account_cash_tran',$orderid,'大唐服务费订单分润异动表创建失败',$datang_tran,'Ym');
                            throw new Exception('大唐服务费资金订单分润异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_cash_tran',$orderid,'大唐服务费订单分润异动表创建成功',$datang_tran,'Ym');
                        }

                        $datangresult = $account->changeAccountMoney($datang_data['data']['a_id'], $account_order_data['ao_datang_money'], '', 'add');
                        if ($datangresult['statusCode'] == 300) {
                            //补日志记录
                            Logs::writeMongodb(800010,'db_account',$orderid,'会员' . $account_order_data['ao_datang_uid'] . '账号:' . $datang_data['data']['a_id'] . 'message:' . $datangresult['message'],$datangresult,'Ym');
                            throw new Exception($account_order_data['ao_datang_nick']."账户".$datangresult['message']);
                        }else{
                            Logs::writeMongodb(800011,'db_account',$orderid,'会员' . $account_order_data['ao_datang_uid'] . '账号:' . $datang_data['data']['a_id'] . 'message:' . $datangresult['message'],$datangresult,'Ym');
                        }
                    } else {
                        //补日志记录
                        Logs::writeMongodb(800010,'db_account',$orderid,'账号查询:' . $account_order_data['ao_datang_uid'] . $datang_data['data'],$datang_data,'Ym');
                        throw new Exception($account_order_data['ao_datang_nick'].$datang_data['data']);
                    }
                }

                //技术服务费

                if (!empty($account_order_data['ao_tech_uid'])) {
                    $tech_data = $account->findAccountByUid($account_order_data['ao_tech_uid']);
                    if ($tech_data['code'] == 200) {
                        $teachdata = [
                            'ad_id' => getTimeMarkID(),
                            'ad_platfrom_id' => $account_order_data['ao_platform_id'],
                            'ad_order_id' => $orderid,
                            'ad_acid' => $tech_data['data']['a_id'],
                            'ad_upid' => $account_order_data['ao_tech_uid'],
                            'ad_nick' => $account_order_data['ao_tech_nick'],
                            'ad_type' => 5,
                            'ad_order_money' => $account_order_data['ao_money'],
                            'ad_money' => $account_order_data['ao_tech_money'],
                            'ad_ratio' => $account_order_data['ao_tech_ratio'],
                            'ad_remark' => '技术服务费分润收益',
                            'ad_create_time' => time()
                        ];
                        $last_tech_data = Db::name('account_order_tran')->insert($teachdata);
                        if (!$last_tech_data) {
                            Logs::writeMongodb(800010,'db_account_order_tran',$orderid,'技术服务费订单分润异动表创建失败',$teachdata,'Ym');
                            throw new Exception('技术服务费订单分润异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_order_tran',$orderid,'技术服务费订单分润异动表创建成功',$teachdata,'Ym');
                        }
                        $tech_tran =[
                            'ca_id' => getTimeMarkID(),
                            'ca_uid' => $account_order_data['ao_tech_uid'],
                            'ca_unick' => $account_order_data['ao_tech_nick'],
                            'ca_platform_id' => $account_order_data['ao_platform_id'],
                            'ca_aid' => $tech_data['data']['a_id'],
                            'ca_money' => $account_order_data['ao_tech_money'],
                            'ca_order_id' => $orderid,
                            'ca_balance' => $tech_data['data']['a_freeMoney'] + $account_order_data['ao_tech_money'],
                            'ca_balance_type' => 5,
                            'ca_memo' => '技术服务费分润收益',
                            'ca_type' => 1,
                            'ca_create_time' => mytime()
                        ];
                        $last_tech_tran_data = Db::name('account_cash_tran')->insert($tech_tran);
                        if (!$last_tech_tran_data) {
                            Logs::writeMongodb(800010,'db_account_cash_tran',$orderid,'技术服务费订单分润异动表创建失败',$tech_tran,'Ym');
                            throw new Exception('技术服务费资金订单分润异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_cash_tran',$orderid,'技术服务费订单分润异动表创建成功',$tech_tran,'Ym');
                        }

                        $techresult = $account->changeAccountMoney($tech_data['data']['a_id'], $account_order_data['ao_tech_money'], '', 'add');
                        if ($techresult['statusCode'] == 300) {
                            //补日志记录
                            Logs::writeMongodb(800010,'db_account',$orderid,'会员' . $account_order_data['ao_tech_uid'] . '账号:' . $tech_data['data']['a_id'] . 'message:' . $techresult['message'],$techresult,'Ym');
                            throw new Exception($account_order_data['ao_tech_nick']."账户".$techresult['message']);
                        }else{
                            Logs::writeMongodb(800011,'db_account',$orderid,'会员' . $account_order_data['ao_tech_uid'] . '账号:' . $tech_data['data']['a_id'] . 'message:' . $techresult['message'],$techresult,'Ym');
                        }
                    } else {
                        //补日志记录
                        Logs::writeMongodb(800010,'db_account',$orderid,'账号查询:' . $account_order_data['ao_tech_uid'] . $tech_data['data'],$tech_data,'Ym');
                        throw new Exception($account_order_data['ao_tech_nick'].$tech_data['data']);
                    }
                }

                //项目方
                if (!empty($account_order_data['ao_platform_uid'])) {
                    $platfrom_data = $account->findAccountByUid($account_order_data['ao_platform_uid']);
                    if ($platfrom_data['code'] == 200) {
                        $platformdata = [
                            'ad_id' => getTimeMarkID(),
                            'ad_platfrom_id' => $account_order_data['ao_platform_id'],
                            'ad_order_id' => $orderid,
                            'ad_acid' => $platfrom_data['data']['a_id'],
                            'ad_upid' => $account_order_data['ao_platform_uid'],
                            'ad_nick' => $account_order_data['ao_platfrom_nick'],
                            'ad_type' => 7,
                            'ad_order_money' => $account_order_data['ao_money'],
                            'ad_money' => $account_order_data['ao_platform_money'],
                            'ad_ratio' => 0,
                            'ad_remark' => '项目方分润收益',
                            'ad_create_time' => time()
                        ];
                        $last_platfrom_data = Db::name('account_order_tran')->insert($platformdata);
                        if (!$last_platfrom_data) {
                            Logs::writeMongodb(800010,'db_account_order_tran',$orderid,'项目方订单分润异动表创建失败',$platformdata,'Ym');
                            throw new Exception('项目方订单分润异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_order_tran',$orderid,'项目方订单分润异动表创建成功',$platformdata,'Ym');
                        }
                        $platfrom_tran =[
                            'ca_id' => getTimeMarkID(),
                            'ca_uid' => $account_order_data['ao_platform_uid'],
                            'ca_unick' => $account_order_data['ao_platfrom_nick'],
                            'ca_platform_id' => $account_order_data['ao_platform_id'],
                            'ca_aid' => $platfrom_data['data']['a_id'],
                            'ca_money' => $account_order_data['ao_platform_money'],
                            'ca_order_id' => $orderid,
                            'ca_balance' => $platfrom_data['data']['a_freeMoney'] + $account_order_data['ao_platform_money'],
                            'ca_balance_type' => 7,
                            'ca_memo' => '项目方分润收益',
                            'ca_type' => 1,
                            'ca_create_time' => mytime()
                        ];
                        $last_platform_tran_data = Db::name('account_cash_tran')->insert($platfrom_tran);
                        if (!$last_platform_tran_data) {
                            Logs::writeMongodb(800010,'db_account_cash_tran',$orderid,'项目方资金订单分润异动表创建失败',$platfrom_tran,'Ym');
                            throw new Exception('项目方资金订单分润异动表创建失败!');
                        }else{
                            Logs::writeMongodb(800011,'db_account_cash_tran',$orderid,'项目方资金订单分润异动表创建成功',$platfrom_tran,'Ym');
                        }

                        $platfromresult = $account->changeAccountMoney($platfrom_data['data']['a_id'], $account_order_data['ao_platform_money'], '', 'add');
                        if ($platfromresult['statusCode'] == 300) {
                            //补日志记录
                            Logs::writeMongodb(800010,'db_account',$orderid,'会员' . $account_order_data['ao_platform_uid'] . '账号:' . $platfrom_data['data']['a_id'] . 'message:' . $platfromresult['message'],$platfromresult,'Ym');
                            throw new Exception($account_order_data['ao_platfrom_nick']."账户".$platfromresult['message']);
                        }else{
                            Logs::writeMongodb(800011,'db_account',$orderid,'会员' . $account_order_data['ao_platform_uid'] . '账号:' . $platfrom_data['data']['a_id'] . 'message:' . $platfromresult['message'],$platfromresult,'Ym');
                        }
                    } else {
                        //补日志记录
                        Logs::writeMongodb(800010,'db_account',$orderid,'账号查询:' . $account_order_data['ao_platform_uid'] . $platfrom_data['data'],$platfrom_data,'Ym');
                        throw new Exception($account_order_data['ao_platfrom_nick'].$platfrom_data['data']);
                    }
                }

                $order_state =Db::name('account_order')->where(['ao_id' => $account_order_data['ao_id']])->update(['ao_state' => 1,'ao_oper_id'=>$operatorid,'ao_sucess_time'=>time()]);
                if (!$order_state) {
                    Logs::writeMongodb(800010,'db_account_order',$orderid,'订单分润表更新失败AO_ID'.$account_order_data['ao_id'],['ao_state' => 1,'ao_oper_id'=>$operatorid,'ao_sucess_time'=>time(),'ao_id' => $account_order_data['ao_id']],'Ym');
                    throw new Exception('订单分润表更新失败!');
                }else{
                    Logs::writeMongodb(800011,'db_account_order',$orderid,'订单分润表更新成功'.$account_order_data['ao_id'],['ao_state' => 1,'ao_oper_id'=>$operatorid,'ao_sucess_time'=>time(),'ao_id' => $account_order_data['ao_id']],'Ym');
                }

                $res = Db::name('order_tasklist')->where(['ot_id'=>$orderid])->find();
                if (!empty($res) && $res['ot_state']==0){
                    Db::name('order_tasklist')->where(['ot_id'=>$orderid])->update(['ot_state'=>1,'ot_update_time'=>mytime(),'ot_opt_type'=>1]);
                }
                Db::commit();
                Logs::writeMongodb(800011,'db_account_order',$orderid,"收益计算完成订单",$account_order_data,'Ym');

                $this->ajaxReturn(ajaxCallBack(200, '收益计算完成!'));
            } catch (\Exception $e) {
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300, '收益计算失败,请重试!'));
            }

        } else {
            Logs::writeMongodb(800012,'db_account_order',$orderid,"订单状态异常是否交易完成",'','Ym');
            $this->ajaxReturn(ajaxCallBack(300, '订单状态异常,请确认是否交易完成'));
        }

    }


    public function create()
    {
    }

    public function edit()
    {
    }

    public function remove()
    {
    }
}