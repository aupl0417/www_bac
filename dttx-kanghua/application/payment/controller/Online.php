<?php

namespace app\payment\controller;

use app\common\controller\Common;
use app\common\tools\Logs;
use app\payment\dttxpay\DtpayNotify;
use app\payment\dttxpay\DtpaySubmit;
use app\payment\model\Account;
use app\payment\model\UserPlatform;
use think\Db;
use think\Exception;
use think\Log;
use think\Request;
use think\Config;

/**
 *
 * User: lirong
 * Date: 2017/7/11
 * Time: 14:39
 */
class Online extends Common
{

    //异步通知
    public function notifyurl()
    {

        if (Request::instance()->isPost()) {
           $post = $_POST;
            if (!empty($post)){
                Logs::write(100001,$post,$post['shopOrderID'],'','Ym');
            }
            $dtpayNotify = new DtpayNotify();
            $resulte = $dtpayNotify->verifySign($post);
            if ($resulte === true) {
                $shopOrderID = $post['shopOrderID'];
                $orderId = $post['orderID'];
                $payTime = strtotime($post['payTime']);
                $orderAmount = $post['orderAmount'] / 100;
                $acccount_id = getTimeMarkID();
                $paytype =$post['payChannel']=='Tangbao'?2:1;
                if (!empty($shopOrderID) && !empty($orderId)) {
                    $res = Db::name('orders')->where(['os_id' => $shopOrderID])->find();
                    if (empty($res)) {
                        return "订单为空!";
                    }

                    $payindata = Db::name('account_pay_in')->where(['ap_shop_order_id' => $orderId])->find();
                    $acorderdata = Db::name('account_order')->where(['ao_order_id' => $orderId])->find();

                    if (!empty($payindata) || !empty($acorderdata)) {
                        Logs::writeMongodb(200030,'',$shopOrderID,'订单ID:' . $shopOrderID . "已入库，无需重复计算",'','Ym');
                        return "已入库，无需重复计算";
                    }

                    if ($res['os_status'] == 0) {
                        $pay_userid = $res['os_buyer_id'];

                        $userPlatfrom = new UserPlatform();
                        $levldata = $userPlatfrom->findTowLevelByUpid($pay_userid);
                        $pay_in=[
                            'ap_id' => $acccount_id,
                            'ap_pay_order_id' => $post['orderID'],
                            'ap_pay_id' => $pay_userid,
                            'ap_pay_unick' => $res['os_buyer_nick'],
                            'ap_channel_id' => $post['channelID'],
                            'ap_shop_order_id' => $post['shopOrderID'],
                            'ap_platform_id' => $res['os_platform_id'],
                            'ap_pay_time' => $post['payTime'],
                            'ap_reciever_id' => $post['recieverID'],
                            'ap_payment_id' => $post['payerID'],
                            'ap_order_amount' => $orderAmount,
                            'ap_agent_amount' => $post['agentAmount'] / 100,
                            'ap_total_money' => $orderAmount,
                            'ap_pay_money' => $orderAmount,
                            'ap_only_pay' => $paytype,
                            'ap_pay_channel' => $post['payChannel'],
                            'ap_bus_id' => $post['busID'],
                            'ap_state' => $post['state']==3?1:0,
                            'ap_create_time' => time()
                        ];
                        $result2 = Db::name('account_pay_in')->insert($pay_in);
                        if (!$result2) {
                               Logs::writeMongodb(200030,'db_account_pay_in',$shopOrderID,"入账数据状态更新失败",$pay_in,'Ym');
                               return "fail";
                        }else{
                            Logs::writeMongodb(200031,'db_account_pay_in',$shopOrderID,'订单入账记录创建成功',$pay_in,'Ym');
                        }

                        if ($post['payChannel'] == 'Tangbao') {
                            $tangbao_pay_in=[
                                'at_id' => $acccount_id,
                                'at_order_id' => $shopOrderID,
                                'at_buy_uid' => $pay_userid,
                                'at_buy_nick' => $res['os_buyer_nick'],
                                'at_platform_id' => $res['os_platform_id'],
                                'at_money' => $orderAmount,
                                'at_paytangbao' => $orderAmount * 100,
                                'at_order_state' => $post['state'],
                                'at_paytime' => $post['payTime'],
                                'at_create_time' => time()
                            ];
                           $tangbaorestult = Db::name('account_tangbao')->insert($tangbao_pay_in);
                           if (!$tangbaorestult){
                               Logs::writeMongodb(200030,'db_account_tangbao',$shopOrderID,'订单唐宝入账失败!',$tangbao_pay_in,'Ym');
                           }else{
                               Logs::writeMongodb(200031,'db_account_tangbao',$shopOrderID,'订单号唐宝入账成功!',$tangbao_pay_in,'Ym');
                           }
                        }
                        Db::startTrans();
                        try {
                            if (!empty($levldata['buyer']['nick'])) {
                                $balance_money = $orderAmount;
                                $account_order_data['ao_id'] = $acccount_id;
                                $account_order_data['ao_platform_id'] = $levldata['buyer']['platformid'];
                                $account_order_data['ao_buy_uid'] = $levldata['buyer']['uid'];
                                $account_order_data['ao_buy_nick'] = $levldata['buyer']['nick'];
                                $account_order_data['ao_order_id'] = $shopOrderID;
                                $account_order_data['ao_money'] = $orderAmount;
                                $account_order_data['ao_score'] = $post['orderAmount'];

                                if ($levldata['member']['levelone']['nick'] != '') {
                                    $account_order_data['ao_onelevel_uid'] = $levldata['member']['levelone']['uid'];
                                    $account_order_data['ao_onelevel_nick'] = $levldata['member']['levelone']['nick'];
                                    $account_order_data['ao_one_userlevel_id'] = $levldata['member']['levelone']['levelid'];
                                    $account_order_data['ao_one_ratio'] = $levldata['member']['levelone']['ratio'];
                                    $account_order_data['ao_onelevel_money'] = ($orderAmount * $levldata['member']['levelone']['ratio']) / 100;
                                    $balance_money -= ($orderAmount * $levldata['member']['levelone']['ratio']) / 100;
                                } else {
                                    $account_order_data['ao_onelevel_uid'] = '';
                                    $account_order_data['ao_onelevel_nick'] = '';
                                    $account_order_data['ao_one_userlevel_id'] = '';
                                    $account_order_data['ao_one_ratio'] = 0;
                                    $account_order_data['ao_onelevel_money'] = 0;
                                }

                                if ($levldata['member']['leveltwo']['nick'] != '') {
                                    $account_order_data['ao_twolevel_uid'] = $levldata['member']['leveltwo']['uid'];
                                    $account_order_data['ao_twolevel_nick'] = $levldata['member']['leveltwo']['nick'];
                                    $account_order_data['ao_two_userlevel_id'] = $levldata['member']['leveltwo']['levelid'];
                                    $account_order_data['ao_two_ratio'] = $levldata['member']['leveltwo']['ratio'];
                                    $account_order_data['ao_twolevel_money'] = ($orderAmount * $levldata['member']['leveltwo']['ratio']) / 100;
                                    $balance_money -= ($orderAmount * $levldata['member']['leveltwo']['ratio']) / 100;
                                } else {
                                    $account_order_data['ao_twolevel_uid'] = '';
                                    $account_order_data['ao_twolevel_nick'] = '';
                                    $account_order_data['ao_two_userlevel_id'] = '';
                                    $account_order_data['ao_two_ratio'] = 0;
                                    $account_order_data['ao_twolevel_money'] = 0;
                                }

                                if ($levldata['agent']['city']['nick'] != '') {
                                    $account_order_data['ao_city_uid'] = $levldata['agent']['city']['uid'];
                                    $account_order_data['ao_city_nick'] = $levldata['agent']['city']['nick'];
                                    $account_order_data['ao_city_money'] = ($levldata['agent']['city']['ratio'] * $orderAmount) / 100;
                                    $account_order_data['ao_city_ratio'] = $levldata['agent']['city']['ratio'];
                                    $balance_money -= ($levldata['agent']['city']['ratio'] * $orderAmount) / 100;
                                } else {
                                    $account_order_data['ao_city_uid'] = '';
                                    $account_order_data['ao_city_nick'] = '';
                                    $account_order_data['ao_city_money'] = 0;
                                    $account_order_data['ao_city_ratio'] = 0;
                                }

                                if ($levldata['agent']['province']['nick'] != '') {
                                    $account_order_data['ao_province_uid'] = $levldata['agent']['province']['uid'];
                                    $account_order_data['ao_province_nick'] = $levldata['agent']['province']['nick'];
                                    $account_order_data['ao_province_money'] = ($levldata['agent']['province']['ratio'] * $orderAmount) / 100;
                                    $account_order_data['ao_province_ratio'] = $levldata['agent']['province']['ratio'];
                                    $balance_money -= ($levldata['agent']['province']['ratio'] * $orderAmount) / 100;
                                } else {
                                    $account_order_data['ao_province_uid'] = '';
                                    $account_order_data['ao_province_nick'] = '';
                                    $account_order_data['ao_province_money'] = '';
                                    $account_order_data['ao_province_ratio'] = 0;
                                }

                                if ($levldata['org']['dttx']['uid'] !=''){
                                    $account_order_data['ao_datang_uid']=$levldata['org']['dttx']['uid'];
                                    $account_order_data['ao_datang_nick']=$levldata['org']['dttx']['nick'];
                                    $account_order_data['ao_datang_ratio'] = 8;
                                    $account_order_data['ao_datang_money'] = $orderAmount*8/100;
                                    $balance_money -= $orderAmount*8/100;
                                }else{
                                    $account_order_data['ao_datang_uid']='';
                                    $account_order_data['ao_datang_nick']='';
                                    $account_order_data['ao_datang_ratio'] = 0;
                                    $account_order_data['ao_datang_money'] = 0;
                                }

                                if ($levldata['org']['lyj']['uid']!=''){
                                    $account_order_data['ao_tech_uid']=$levldata['org']['lyj']['uid'];
                                    $account_order_data['ao_tech_nick']=$levldata['org']['lyj']['nick'];
                                    $account_order_data['ao_tech_ratio'] = 4;
                                    $account_order_data['ao_tech_money'] = $orderAmount*4/100;
                                    $balance_money -= $orderAmount*4/100;
                                }else{
                                    $account_order_data['ao_tech_uid']='';
                                    $account_order_data['ao_tech_nick']='';
                                    $account_order_data['ao_tech_ratio'] = 0;
                                    $account_order_data['ao_tech_money'] = 0;
                                }

                                if ($levldata['org']['platfrom']['uid']!=''){
                                    $account_order_data['ao_platform_uid']=$levldata['org']['platfrom']['uid'];
                                    $account_order_data['ao_platfrom_nick']=$levldata['org']['platfrom']['nick'];
                                    $account_order_data['ao_platform_money'] = $balance_money;
                                }

                                $account_order_data['ao_state'] = 0;
                                $account_order_data['ao_balance_money'] = $balance_money;
                                $account_order_data['ao_create_time'] = $payTime;
                                $result3 = Db::name('account_order')->insert($account_order_data);
                                if (!$result3) {
                                    Logs::writeMongodb(200030,'db_account_order',$shopOrderID,"分润订单创建失败",$account_order_data,'Ym');
                                    throw new Exception('分润订单计算失败!');
                                }else{
                                    Logs::writeMongodb(200031,'db_account_order',$shopOrderID,'订单分润订单创建成功',$account_order_data,'Ym');
                                }
                            }
                            $result = Db::name('orders')->where(['os_id' => $shopOrderID])->update(['os_status' => 1, 'os_pay_order_id' => $orderId, 'os_pay_time' => $payTime,'os_pay_type'=>$paytype]);
                            if (!$result) {
                                Logs::writeMongodb(200030,'db_orders',$shopOrderID,'订单状态更新失败',$account_order_data,'Ym');
                                throw new Exception("订单状态更新失败,订单号:$shopOrderID");
                            }else{
                                Logs::writeMongodb(200031,'db_orders',$shopOrderID,'订单状态更新成功',$account_order_data,'Ym');
                            }
                            Db::commit();
                            Logs::writeMongodb(200031,'db_orders',$shopOrderID,'异步订单处理成功',$account_order_data,'Ym');
                            echo  "success";
                        } catch (\Exception $e) {
                            echo "fail";
                            Db::rollback();
                        }
                    }
                }

            } else {
                echo "fail";
                Logs::writeMongodb(200030,'notifyurl',$post['shopOrderID'],"订单数据验证失败:",$post,'Ym');
            }

        }
    }

    // 会员升级异步通知
    public function vipNotifyurl()
    {
        if (Request::instance()->isPost()) {
           $post = $_POST;

            $dtpayNotify = new DtpayNotify();
            $resulte = $dtpayNotify->verifySign($post);
            if ($resulte === true) {
                $shopOrderID = $post['shopOrderID'];
                Logs::writeMongodb(100002,'vipNotifyurl',$shopOrderID,'会员升级异步通知信息',$post,'Ym');
                $orderId = $post['orderID'];
                $payTime = strtotime($post['payTime']);
                $orderAmount = $post['orderAmount'] / 100;
                $acccount_id = getTimeMarkID();
                $paytype =$post['payChannel']=='Tangbao'?2:1;
                if (!empty($shopOrderID) && !empty($orderId)) {
                    $res = Db::name('orders')->where(['os_id' => $shopOrderID])->find();
                    if (empty($res)) {
                        Logs::writeMongodb(200040,'',$shopOrderID,'订单为空','','Ym');
                        return "fail！";
                    }
                    //所选id
                    $ugoods_level = Db::name('orders_goods')->field('og_goods_id')->where('og_order_id',$shopOrderID)->find();
                    $ugoods_ulevelId =!empty($ugoods_level) ? $ugoods_level['og_goods_id']:0;
                    Logs::writeMongodb(200040,'db_orders_goods',$shopOrderID,'会员升级等级数据',$ugoods_level,'Ym');
                    $payindata = Db::name('account_pay_in')->where(['ap_shop_order_id' => $orderId])->find();
                    $acorderdata = Db::name('account_order')->where(['ao_order_id' => $orderId])->find();

                    if (!empty($payindata) || !empty($acorderdata)) {
                    //    Log::write('订单ID:' . $orderId . "已入库，无需重复计算");
                        Logs::writeMongodb(200040,'',$shopOrderID,'订单ID:已入库，无需重复计算','','Ym');
                        return "fail";
                    }

                if ($res['os_status'] == 0) {
                    $pay_userid = $res['os_buyer_id'];

                    $userPlatfrom = new UserPlatform();
                    $levldata = $userPlatfrom->findTowLevelByUpid($pay_userid);
                    $pay_in =[
                        'ap_id' => $acccount_id,
                        'ap_pay_order_id' => $post['orderID'],
                        'ap_pay_id' => $pay_userid,
                        'ap_pay_unick' => $res['os_buyer_nick'],
                        'ap_channel_id' => $post['channelID'],
                        'ap_shop_order_id' => $post['shopOrderID'],
                        'ap_platform_id' => $res['os_platform_id'],
                        'ap_pay_time' => $post['payTime'],
                        'ap_reciever_id' => $post['recieverID'],
                        'ap_payment_id' => $post['payerID'],
                        'ap_order_amount' => $orderAmount,
                        'ap_agent_amount' => $post['agentAmount'] / 100,
                        'ap_total_money' => $orderAmount,
                        'ap_pay_money' => $orderAmount,
                        'ap_only_pay' => $paytype,
                        'ap_pay_channel' => $post['payChannel'],
                        'ap_bus_id' => $post['busID'],
                        'ap_state' => $post['state']==3?1:0,
                        'ap_create_time' => time()
                    ];
                    $result2 = Db::name('account_pay_in')->insert($pay_in);
                    if (!$result2) {
                        Logs::writeMongodb(200040,'account_pay_in',$shopOrderID,"订单入账数据记录创建失败",$pay_in,'Ym');
                    }else{
                        Logs::writeMongodb(200041,'account_pay_in',$shopOrderID,"订单号入账数据记录创建成功",$pay_in,'Ym');
                    }

                    if ($post['payChannel']=='Tangbao') {
                        $tangbao_pay_in=[
                            'at_id' => $acccount_id,
                            'at_order_id' => $shopOrderID,
                            'at_buy_uid' => $pay_userid,
                            'at_buy_nick' => $res['os_buyer_nick'],
                            'at_platform_id' => $res['os_platform_id'],
                            'at_money' => $orderAmount,
                            'at_paytangbao' => $orderAmount * 100,
                            'at_order_state' => $post['state'],
                            'at_paytime' => $post['payTime'],
                            'at_create_time' => time()
                        ];
                        $tangbaorestult = Db::name('account_tangbao')->insert($tangbao_pay_in);
                        if (!$tangbaorestult){
                            Logs::writeMongodb(200040,'db_account_tangbao',$shopOrderID,'订单号唐宝入账失败!',$tangbao_pay_in,'Ym');
                        }else{
                            Logs::writeMongodb(200041,'db_account_tangbao',$shopOrderID,'订单号唐宝入账成功!',$tangbao_pay_in,'Ym');
                        }
                    }
                    Db::startTrans();
                    try {
                        if (!empty($levldata['buyer']['nick'])) {
                            $balance_money = $orderAmount;
                            $account_order_data['ao_id'] = $acccount_id;
                            $account_order_data['ao_platform_id'] = $levldata['buyer']['platformid'];
                            $account_order_data['ao_buy_uid'] = $levldata['buyer']['uid'];
                            $account_order_data['ao_buy_nick'] = $levldata['buyer']['nick'];
                            $account_order_data['ao_order_id'] = $shopOrderID;
                            $account_order_data['ao_money'] = $orderAmount;
                            $account_order_data['ao_score'] = $post['orderAmount'];

                            if ($levldata['member']['levelone']['nick'] != '') {
                                $account_order_data['ao_onelevel_uid'] = $levldata['member']['levelone']['uid'];
                                $account_order_data['ao_onelevel_nick'] = $levldata['member']['levelone']['nick'];
                                $account_order_data['ao_one_userlevel_id'] = $levldata['member']['levelone']['levelid'];
                                $account_order_data['ao_one_ratio'] = $levldata['member']['levelone']['ratio'];
                                $account_order_data['ao_onelevel_money'] = ($orderAmount * $levldata['member']['levelone']['ratio']) / 100;
                                $balance_money -= ($orderAmount * $levldata['member']['levelone']['ratio']) / 100;
                            } else {
                                $account_order_data['ao_onelevel_uid'] = '';
                                $account_order_data['ao_onelevel_nick'] = '';
                                $account_order_data['ao_one_userlevel_id'] = '';
                                $account_order_data['ao_one_ratio'] = 0;
                                $account_order_data['ao_onelevel_money'] = 0;
                            }

                            if ($levldata['member']['leveltwo']['nick'] != '') {
                                $account_order_data['ao_twolevel_uid'] = $levldata['member']['leveltwo']['uid'];
                                $account_order_data['ao_twolevel_nick'] = $levldata['member']['leveltwo']['nick'];
                                $account_order_data['ao_two_userlevel_id'] = $levldata['member']['leveltwo']['levelid'];
                                $account_order_data['ao_two_ratio'] = $levldata['member']['leveltwo']['ratio'];
                                $account_order_data['ao_twolevel_money'] = ($orderAmount * $levldata['member']['leveltwo']['ratio']) / 100;
                                $balance_money -= ($orderAmount * $levldata['member']['leveltwo']['ratio']) / 100;
                            } else {
                                $account_order_data['ao_twolevel_uid'] = '';
                                $account_order_data['ao_twolevel_nick'] = '';
                                $account_order_data['ao_two_userlevel_id'] = '';
                                $account_order_data['ao_two_ratio'] = 0;
                                $account_order_data['ao_twolevel_money'] = 0;
                            }

                            if ($levldata['agent']['city']['nick'] != '') {
                                $account_order_data['ao_city_uid'] = $levldata['agent']['city']['uid'];
                                $account_order_data['ao_city_nick'] = $levldata['agent']['city']['nick'];
                                $account_order_data['ao_city_money'] = ($levldata['agent']['city']['ratio'] * $orderAmount) / 100;
                                $account_order_data['ao_city_ratio'] = $levldata['agent']['city']['ratio'];
                                $balance_money -= ($levldata['agent']['city']['ratio'] * $orderAmount) / 100;
                            } else {
                                $account_order_data['ao_city_uid'] = '';
                                $account_order_data['ao_city_nick'] = '';
                                $account_order_data['ao_city_money'] = 0;
                                $account_order_data['ao_city_ratio'] = 0;
                            }

                            if ($levldata['agent']['province']['nick'] != '') {
                                $account_order_data['ao_province_uid'] = $levldata['agent']['province']['uid'];
                                $account_order_data['ao_province_nick'] = $levldata['agent']['province']['nick'];
                                $account_order_data['ao_province_money'] = ($levldata['agent']['province']['ratio'] * $orderAmount) / 100;
                                $account_order_data['ao_province_ratio'] = $levldata['agent']['province']['ratio'];
                                $balance_money -= ($levldata['agent']['province']['ratio'] * $orderAmount) / 100;
                            } else {
                                $account_order_data['ao_province_uid'] = '';
                                $account_order_data['ao_province_nick'] = '';
                                $account_order_data['ao_province_money'] = '';
                                $account_order_data['ao_province_ratio'] = 0;
                            }

                            if ($levldata['org']['dttx']['uid'] !=''){
                                $account_order_data['ao_datang_uid']=$levldata['org']['dttx']['uid'];
                                $account_order_data['ao_datang_nick']=$levldata['org']['dttx']['nick'];
                                $account_order_data['ao_datang_ratio'] = 8;
                                $account_order_data['ao_datang_money'] = $orderAmount*8/100;
                                $balance_money -= $orderAmount*8/100;
                            }else{
                                $account_order_data['ao_datang_uid']='';
                                $account_order_data['ao_datang_nick']='';
                                $account_order_data['ao_datang_ratio'] = 0;
                                $account_order_data['ao_datang_money'] = 0;
                            }

                            if ($levldata['org']['lyj']['uid']!=''){
                                $account_order_data['ao_tech_uid']=$levldata['org']['lyj']['uid'];
                                $account_order_data['ao_tech_nick']=$levldata['org']['lyj']['nick'];
                                $account_order_data['ao_tech_ratio'] = 4;
                                $account_order_data['ao_tech_money'] = $orderAmount*4/100;
                                $balance_money -= $orderAmount*4/100;
                            }else{
                                $account_order_data['ao_tech_uid']='';
                                $account_order_data['ao_tech_nick']='';
                                $account_order_data['ao_tech_ratio'] = 0;
                                $account_order_data['ao_tech_money'] = 0;
                            }

                            if ($levldata['org']['platfrom']['uid']!=''){
                                $account_order_data['ao_platform_uid']=$levldata['org']['platfrom']['uid'];
                                $account_order_data['ao_platfrom_nick']=$levldata['org']['platfrom']['nick'];
                                $account_order_data['ao_platform_money'] = $balance_money;
                            }

                            $account_order_data['ao_state'] = 0;
                            $account_order_data['ao_balance_money'] = $balance_money;

                            $account_order_data['ao_create_time'] = $payTime;
                            $result3 = Db::name('account_order')->insert($account_order_data);
                            if (!$result3) {
                                Logs::writeMongodb(200040,'db_account_order',$shopOrderID,"分润订单创建失败",$account_order_data,'Ym');
                                throw new Exception('分润订单计算失败!');
                            }else{
                                Logs::writeMongodb(200041,'db_account_order',$shopOrderID,"分润订单创建成功",$account_order_data,'Ym');
                            }
                        }

                        $result = Db::name('orders')->where(['os_id' => $shopOrderID])->update(['os_status' => 3, 'os_pay_order_id' => $orderId, 'os_pay_time' => $payTime,'os_pay_type'=>$paytype,'os_auto_receiver_time'=>time()]);
                        if (!$result) {
                            Logs::writeMongodb(200040,'db_orders',$shopOrderID,"订单状态更新失败",['os_status' => 3, 'os_pay_order_id' => $orderId, 'os_pay_time' => $payTime,'os_pay_type'=>$paytype,'os_auto_receiver_time'=>time()],'Ym');
                            throw new Exception("订单状态更新失败,订单号:$shopOrderID");
                        }else{
                            Logs::writeMongodb(200041,'db_orders',$shopOrderID,"订单状态更新成功",['os_status' => 3, 'os_pay_order_id' => $orderId, 'os_pay_time' => $payTime,'os_pay_type'=>$paytype,'os_auto_receiver_time'=>time()],'Ym');
                        }
                        $userlevel =Db::name('user_platform')->where(['up_id'=>$pay_userid])->update(['up_user_level_id'=>$ugoods_ulevelId]);
                        if (!$userlevel){
                            Logs::writeMongodb(200040,'user_platform',$shopOrderID,'会员等级更新失败:',['up_user_level_id'=>$ugoods_ulevelId,'up_id'=>$pay_userid],'Ym');
                            throw new Exception('会员等级更新失败，请联系客服人员处理！');
                        }else{
                            Logs::writeMongodb(200041,'user_platform',$shopOrderID,'会员等级更新成功：',['up_user_level_id'=>$ugoods_ulevelId,'up_id'=>$pay_userid],'Ym');
                        }
                        Db::commit();
                        Logs::writeMongodb(200040,'db_orders',$shopOrderID,"异步订单处理成功",$account_order_data,'Ym');
                        echo "success";
                    } catch (\Exception $e) {
//                        echo $e->getMessage();
                        echo "fail";
                        Db::rollback();
                    }
                }
                }

            } else {
                echo "fail";
                Logs::writeMongodb(200040,'notifyurl',$post['shopOrderID'],"会员订单数据验证失败:",$post,'Ym');
            }

        }
    }

    //同步通知
    public function returnurl()
    {
        $id = Request::instance()->param('id', '0');
        if (!empty($id) && is_numeric($id)) {
            $this->assign('state', 'success');
            $this->assign('title', '支付成功!');
        } else {
            $this->assign('state', 'failure');
            $this->assign('title', '支付失败!');
        }
        $this->assign('orderid', $id);
        return $this->fetch();
    }

    // 单品订单支付
    public function orderpayment()
    {
        $dtpaySubmit = new DtpaySubmit('Single');
        return $dtpaySubmit->buildRequestForm($_POST);
    }

    // 会员升级支付
    public function viporderpayment()
    {
        $dtpaySubmit = new DtpaySubmit('Vip');
        return $dtpaySubmit->buildRequestForm($_POST);
    }


    public function index()
    {

       return "";

    }

    //队列循环计算表订单
    public function ordertask()
    {
    //    while (true){
            $res = Db::name('order_tasklist')->where(['ot_state' => 0])->order(['ot_level' => 'asc'])->select();

    if (!empty($res)) {
        foreach ($res as $item){
            $orderid = $item['ot_id'];
            if (empty($orderid) && is_numeric($orderid)) {
                $this->ajaxReturn(ajaxCallBack(300, '订单ID不能为空!'));
            }
            //订单数据
            $orders = Db::name('orders')->field('os_id,os_order_price,os_actual_payprice,os_status')->where(['os_id' => $orderid])->find();
            if ($orders['os_status'] == 3) {
                //入账资金数据
                $account_order_payin = Db::name('account_pay_in')->where(['ap_shop_order_id' => $orderid])->find();
                if (empty($account_order_payin)) {
                    echo "没有该订单支付记录,请确认该订单是否已支付"."<br>";
                    Logs::writeMongodb(900000,'',$orderid,'没有该订单支付记录,请确认该订单是否已支付','','Ym');
                    continue;
                }
                //分润订单数据
                $account_order_data = Db::name('account_order')->where(['ao_order_id' => $orderid])->lock(true)->find();
                if (empty($account_order_data)) {
                    Logs::writeMongodb(900000,'',$orderid,'分润订单记录不存在,请确认','','Ym');
                    echo "分润订单记录不存在,请确认"."<br>";
                    continue;
                }

                if ($account_order_data['ao_state']==1){
                    Db::name('order_tasklist')->where(['ot_id'=>$orderid])->update(['ot_state'=>1,'ot_update_time'=>mytime()]);
                    echo $account_order_data['ao_order_id']."该订单已处理,请勿重复操作!"."<br>";
                    continue;
                }


                if (($orders['os_actual_payprice'] != $account_order_data['ao_money']) || ($account_order_payin['ap_pay_money'] != $account_order_data['ao_money'])) {
                    echo "订单金额异常,请确认订单金额与入账金额是否一致!"."<br>";
                    continue;
                }
                $operatorid =9999;
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
                                    Logs::writeMongodb(900000,'db_account_order_tran',$orderid,'一级订单分润表创建失败',$onedata,'Ym');
                                    throw new Exception('一级订单分润表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_order_tran',$orderid,'一级订单分润表创建成功',$onedata,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_cash_tran',$orderid,'一级资金异动表创建失败',$one_tran,'Ym');
                                    throw new Exception('一级资金异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_cash_tran',$orderid,'一级资金异动表创建成功',$one_tran,'Ym');
                                }
                                $oneresult = $account->changeAccountMoney($one_account['data']['a_id'], $account_order_data['ao_onelevel_money'], 'vip', 'add');
                                if ($oneresult['statusCode'] == 300) {
                                    Logs::writeMongodb(900000,'db_account',$orderid,'会员' . $account_order_data['ao_onelevel_uid'] . '账号:' . $one_account['data']['a_id'] . 'message:' . $oneresult['message'],$oneresult,'Ym');
                                    throw new Exception($one_account['data']['a_id'].$oneresult['message']);
                                    //补日志记录
                                }else{
                                    Logs::writeMongodb(900001,'db_account',$orderid,'会员' . $account_order_data['ao_onelevel_uid'] . '账号:' . $one_account['data']['a_id'] . 'message:' . $oneresult['message'],$oneresult,'Ym');
                                }

                            } else {
                                //补日志记录
                                Logs::writeMongodb(900000,'db_account',$orderid,'账号查询:' . $account_order_data['ao_onelevel_uid'] . $one_account['data'],$one_account,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_order_tran',$orderid,'二级订单异动表创建失败:',$twodata,'Ym');
                                    throw new Exception('二级订单异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_order_tran',$orderid,'二级订单异动表创建成功:',$twodata,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_cash_tran',$orderid,'二级资金异动表创建失败',$two_tran,'Ym');
                                    throw new Exception('二级资金异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_cash_tran',$orderid,'二级资金异动表创建成功',$two_tran,'Ym');
                                }
                                $tworesult = $account->changeAccountMoney($two_account['data']['a_id'], $account_order_data['ao_twolevel_money'], 'vip', 'add');
                                if ($tworesult['statusCode'] == 300) {
                                    //补日志记录
                                    Logs::writeMongodb(900000,'db_account',$orderid,'会员' . $account_order_data['ao_twolevel_uid'] . '账号:' . $two_account['data']['a_id'] . 'message:' . $tworesult['message'],$tworesult,'Ym');
                                    throw new Exception($account_order_data['ao_twolevel_nick']."账户".$tworesult['message']);

                                }else{
                                    Logs::writeMongodb(900001,'db_account',$orderid,'会员' . $account_order_data['ao_twolevel_uid'] . '账号:' . $two_account['data']['a_id'] . 'message:' . $tworesult['message'],$tworesult,'Ym');
                                }
                            } else {
                                //补日志记录
                                Logs::writeMongodb(900000,'db_account',$orderid,'账号查询:' . $account_order_data['ao_twolevel_uid'] . $two_account['data'],$two_account,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_order_tran',$orderid,'省代订单分润异动表创建失败',$provdata,'Ym');
                                    throw new Exception('省代订单分润异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_order_tran',$orderid,'省代订单分润异动表创建成功',$provdata,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_cash_tran',$orderid,'省代资金异动表创建失败',$province_tran,'Ym');
                                    throw new Exception('省代资金异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_cash_tran',$orderid,'省代资金异动表创建成功',$province_tran,'Ym');
                                }

                                $province_result = $account->changeAccountMoney($province_data['data']['a_id'], $account_order_data['ao_province_money'], 'agent', 'add');
                                if ($province_result['statusCode'] == 300) {
                                    //补日志记录
                                    //    echo $province_result['message'];
                                    Logs::writeMongodb(900000,'db_account',$orderid,'会员' . $account_order_data['ao_province_uid'] . '账号:' . $province_data['data']['a_id'] . 'message:' . $province_result['message'],$province_result,'Ym');
                                    throw new Exception($account_order_data['ao_twolevel_nick']."账户".$province_result['message']);

                                }else{
                                    Logs::writeMongodb(900001,'db_account',$orderid,'会员' . $account_order_data['ao_province_uid'] . '账号:' . $province_data['data']['a_id'] . 'message:' . $province_result['message'],$province_result,'Ym');
                                }
                            } else {
                                //补日志记录
                                Logs::writeMongodb(900000,'db_account',$orderid,'账号查询:' . $account_order_data['ao_province_uid'] . $province_data['data'],$province_data,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_order_tran',$orderid,'市代订单分润异动表创建失败',$citydata,'Ym');
                                    throw new Exception('市代订单分润异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_order_tran',$orderid,'市代订单分润异动表创建成功',$citydata,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_cash_tran',$orderid,'市代资金订单分润异动表创建失败',$city_tran,'Ym');
                                    throw new Exception('市代资金订单分润异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_cash_tran',$orderid,'市代资金订单分润异动表创建成功',$city_tran,'Ym');
                                }

                                $cityresult = $account->changeAccountMoney($city_data['data']['a_id'], $account_order_data['ao_city_money'], 'agent', 'add');
                                if ($cityresult['statusCode'] == 300) {
                                    //补日志记录
                                    Logs::writeMongodb(900000,'db_account',$orderid,'会员' . $account_order_data['ao_city_uid'] . '账号:' . $city_data['data']['a_id'] . 'message:' . $cityresult['message'],$cityresult,'Ym');
                                    throw new Exception($account_order_data['ao_city_nick']."账户".$cityresult['message']);

                                }else{
                                    Logs::writeMongodb(900001,'db_account',$orderid,'会员' . $account_order_data['ao_city_uid'] . '账号:' . $city_data['data']['a_id'] . 'message:' . $cityresult['message'],$cityresult,'Ym');
                                }
                            } else {
                                //补日志记录
                                Logs::writeMongodb(900000,'db_account',$orderid,'账号查询:' . $account_order_data['ao_city_uid'] . $city_data['data'],$city_data,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_order_tran',$orderid,'大唐服务费订单分润异动表创建失败',$datangdata,'Ym');
                                    throw new Exception('大唐服务费订单分润异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_order_tran',$orderid,'大唐服务费订单分润异动表创建成功',$datangdata,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_cash_tran',$orderid,'大唐服务费订单分润异动表创建失败',$datang_tran,'Ym');
                                    throw new Exception('大唐服务费资金订单分润异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_cash_tran',$orderid,'大唐服务费订单分润异动表创建成功',$datang_tran,'Ym');
                                }

                                $datangresult = $account->changeAccountMoney($datang_data['data']['a_id'], $account_order_data['ao_datang_money'], '', 'add');
                                if ($datangresult['statusCode'] == 300) {
                                    //补日志记录
                                    Logs::writeMongodb(900000,'db_account',$orderid,'会员' . $account_order_data['ao_datang_uid'] . '账号:' . $datang_data['data']['a_id'] . 'message:' . $datangresult['message'],$datangresult,'Ym');
                                    throw new Exception($account_order_data['ao_datang_nick']."账户".$datangresult['message']);
                                }else{
                                    Logs::writeMongodb(900001,'db_account',$orderid,'会员' . $account_order_data['ao_datang_uid'] . '账号:' . $datang_data['data']['a_id'] . 'message:' . $datangresult['message'],$datangresult,'Ym');
                                }
                            } else {
                                //补日志记录
                                Logs::writeMongodb(900000,'db_account',$orderid,'账号查询:' . $account_order_data['ao_datang_uid'] . $datang_data['data'],$datang_data,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_order_tran',$orderid,'技术服务费订单分润异动表创建失败',$teachdata,'Ym');
                                    throw new Exception('技术服务费订单分润异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_order_tran',$orderid,'技术服务费订单分润异动表创建成功',$teachdata,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_cash_tran',$orderid,'技术服务费订单分润异动表创建失败',$tech_tran,'Ym');
                                    throw new Exception('技术服务费资金订单分润异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_cash_tran',$orderid,'技术服务费订单分润异动表创建成功',$tech_tran,'Ym');
                                }

                                $techresult = $account->changeAccountMoney($tech_data['data']['a_id'], $account_order_data['ao_tech_money'], '', 'add');
                                if ($techresult['statusCode'] == 300) {
                                    //补日志记录
                                    Logs::writeMongodb(900000,'db_account',$orderid,'会员' . $account_order_data['ao_tech_uid'] . '账号:' . $tech_data['data']['a_id'] . 'message:' . $techresult['message'],$techresult,'Ym');
                                    throw new Exception($account_order_data['ao_tech_nick']."账户".$techresult['message']);
                                }else{
                                    Logs::writeMongodb(900001,'db_account',$orderid,'会员' . $account_order_data['ao_tech_uid'] . '账号:' . $tech_data['data']['a_id'] . 'message:' . $techresult['message'],$techresult,'Ym');
                                }
                            } else {
                                //补日志记录
                                Logs::writeMongodb(900000,'db_account',$orderid,'账号查询:' . $account_order_data['ao_tech_uid'] . $tech_data['data'],$tech_data,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_order_tran',$orderid,'项目方订单分润异动表创建失败',$platformdata,'Ym');
                                    throw new Exception('项目方订单分润异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_order_tran',$orderid,'项目方订单分润异动表创建成功',$platformdata,'Ym');
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
                                    Logs::writeMongodb(900000,'db_account_cash_tran',$orderid,'项目方资金订单分润异动表创建失败',$platfrom_tran,'Ym');
                                    throw new Exception('项目方资金订单分润异动表创建失败!');
                                }else{
                                    Logs::writeMongodb(900001,'db_account_cash_tran',$orderid,'项目方资金订单分润异动表创建成功',$platfrom_tran,'Ym');
                                }

                                $platfromresult = $account->changeAccountMoney($platfrom_data['data']['a_id'], $account_order_data['ao_platform_money'], '', 'add');
                                if ($platfromresult['statusCode'] == 300) {
                                    //补日志记录
                                    Logs::writeMongodb(900000,'db_account',$orderid,'会员' . $account_order_data['ao_platform_uid'] . '账号:' . $platfrom_data['data']['a_id'] . 'message:' . $platfromresult['message'],$platfromresult,'Ym');
                                    throw new Exception($account_order_data['ao_platfrom_nick']."账户".$platfromresult['message']);
                                }else{
                                    Logs::writeMongodb(900001,'db_account',$orderid,'会员' . $account_order_data['ao_platform_uid'] . '账号:' . $platfrom_data['data']['a_id'] . 'message:' . $platfromresult['message'],$platfromresult,'Ym');
                                }
                            } else {
                                //补日志记录
                                Logs::writeMongodb(900000,'db_account',$orderid,'账号查询:' . $account_order_data['ao_platform_uid'] . $platfrom_data['data'],$platfrom_data,'Ym');
                                throw new Exception($account_order_data['ao_platfrom_nick'].$platfrom_data['data']);
                            }
                        }

                        $order_state =Db::name('account_order')->where(['ao_id' => $account_order_data['ao_id']])->update(['ao_state' => 1,'ao_oper_id'=>$operatorid,'ao_sucess_time'=>time()]);
                        if (!$order_state) {
                            Logs::writeMongodb(900000,'db_account_order',$orderid,'订单分润表更新失败'.$account_order_data['ao_id'],['ao_state' => 1,'ao_oper_id'=>$operatorid,'ao_sucess_time'=>time(),'ao_id' => $account_order_data['ao_id']],'Ym');
                            throw new Exception('订单分润表更新失败!');
                        }else{
                            Logs::writeMongodb(900000,'db_account_order',$orderid,'订单分润表更新成功'.$account_order_data['ao_id'],['ao_state' => 1,'ao_oper_id'=>$operatorid,'ao_sucess_time'=>time(),'ao_id' => $account_order_data['ao_id']],'Ym');
                        }
                        Db::name('order_tasklist')->where(['ot_id'=>$orderid])->update(['ot_state'=>1,'ot_update_time'=>mytime()]);
                        Db::commit();
                        Logs::writeMongodb(900001,'db_account_order',$orderid,"订单收益计算完成",$account_order_data,'Ym');
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                        Db::rollback();
                        continue;
                    }

                } else {
                    Logs::writeMongodb(900012,'db_account_order',$orderid,"订单状态异常是否交易完成",'','Ym');
                }

            }

        }else{
       //     sleep(3);
            echo "计算完毕，延迟3秒";
        }
   //     }

    }


    //订单自动收货
    public function orderautoreceive(){
        $timer =time();
        $res =Db::name('orders')->field('os_id,os_platform_id')->where(['os_status'=>2])->where('os_auto_receiver_time','<',$timer)->limit(0,10)->order('os_auto_receiver_time','asc')->select();

        if (!empty($res)){
           foreach ($res as $item){

               $order =Db::name('orders')->field('os_id,os_status,os_platform_id')->where(['os_id'=>$item['os_id']])->lock(true)->find();
               if (!empty($order) && $order['os_status']==2){
                   $data['orderID']=$order['os_id'];
                   $data['returnType']=1;
                   $data['dealType']=2;
                   $confirmOrderUrl =Config::get('dttxapi.confirmOrderUrl');
                   $interface_res =datang_interface($confirmOrderUrl,$data,700001);
                   Logs::writeMongodb(700001,'',$order['os_id'],'自动收货接口日志',$interface_res,'Ym');
                   if (false===$interface_res){
                       Logs::writeMongodb(600020,'db_orders',$order['os_id'],'订单['.$order['os_id'].']确认收获接口数据获取错误',$interface_res,'Ym');
                       continue;
                   }
                   if ($interface_res['id']==1001 || $interface_res['id']==1009 ){
                       Db::startTrans();
                       try{
                           $res_orders =Db::name('orders')->where(['os_id'=>$order['os_id']])->update(['os_status'=>3]);
                           if (!$res_orders){
                               $sql =Db::name('orders')->where(['os_id'=>$order['os_id']])->fetchSql(true)->update(['os_status'=>3]);
                               Logs::writeMongodb(600020,'db_orders',$order['os_id'],'订单状态更新失败',$sql,'Ym');
                               throw new Exception('订单状态更新失败，请重试!');
                           }
                           $res_pay = Db::name('account_pay_in')->where(['ap_shop_order_id'=>$order['os_id']])->update(['ap_state'=>3]);
                           if (!$res_pay){
                               Logs::writeMongodb(600020,'db_account_pay_in',$order['os_id'],'订单入账表状态更新失败','','Ym');
                               throw  new Exception("订单入账表状态更新失败，请重试!");
                           }

                           $res_task =Db::name('order_tasklist')->insert([
                               'ot_id' =>$order['os_id'],
                               'ot_state'=>0,
                               'ot_platform_id'=>$order['os_platform_id'],
                               'ot_create_time'=>mytime(),
                               'ot_level'=>3
                           ]);
                           if (!$res_task){
                               Logs::writeMongodb(600020,'db_order_tasklist',$order['os_id'],'增加分润任务记录失败','','Ym');
                               throw new Exception('增加分润任务记录失败!');
                           }
                           Db::commit();
                           echo "success";
//                       $this->ajaxReturn(ajaxCallBack(200,'确认收货成功!'));
                           Logs::writeMongodb(600021,'db_orders',$order['os_id'],'自动收货成功','','Ym');
                       }catch (\Exception $e){
                           Db::rollback();
                           continue;
                       }

                   }else{
                       Logs::writeMongodb(600020,'db_orders',$order['os_id'],'订单确认收货失败',$interface_res,'Ym');
                       echo json_encode(ajaxCallBack(301,'订单['.$order['os_id'].']订单确认收货失败'.date('Y-m-d H:i:s')));
                   }
               }

           }

        }else{
            echo json_encode(ajaxCallBack(404,'暂无订单_'.date('Y-m-d H:i:s')));
        //    $timer =mytime();
        //    Log::write('接口测试'.$timer,'debug');
   //     }

    }


}

    //关闭超时订单
    public function closeTimeOutOrder(){
        $timer =time();
        $res =Db::name('orders')->field('os_id,os_status')->where('os_status',0)->where('os_create_time','<',$timer-1800)->order('os_create_time','asc')->limit(0,10)->select();
        if (!empty($res)){
            foreach ($res as $item){
                $order =Db::name('orders')->where(['os_id'=>$item['os_id'],'os_status'=>0])->lock(true)->find();
                if (!empty($order)){
                    $result =Db::name('orders')->where('os_id',$item['os_id'])->update(['os_status'=>-1,'os_update_time'=>time()]);
                    if (!$result){
                    //    echo "fail".PHP_EOL;
                        echo json_encode(ajaxCallBack(301,"订单".$item['os_id']."状态更新失败!"));
                        Logs::write(600030,'',$item['os_id'],'db_orders','Ym');
                    }else{
                        echo "success".PHP_EOL;
                        Logs::write(600031,'',$item['os_id'],'db_orders','Ym');
                    }
                }
            }
        }else{
            sleep(10);
            echo json_encode(ajaxCallBack(404,"暂无订单，延迟10秒执行"));
        }

    }

}