<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/1 0001
 * Time: 20:34
 */
namespace app\platform\controller;

use app\common\controller\Platform;
use app\common\tools\Logs;
use app\payment\model\UserPlatform;
use think\Config;
use think\Request;
use think\db;
use think\Session;

class Order extends Platform{

    public function _initialize(){
        parent::_initialize();
        $this->model = model('Orders');
    }

    public function index(){
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 30, 'intval');

        $id            = input('post.id', '', 'htmlspecialchars,strip_tags,trim');
        $username      = input('post.username', '', 'htmlspecialchars,strip_tags,trim');
        $goodsname     = input('post.goodsname', '', 'htmlspecialchars,strip_tags,trim');
        $beginTime     = input('post.beginTime', '', 'htmlspecialchars,strip_tags,trim');
        $endTime       = input('post.endTime', '', 'htmlspecialchars,strip_tags,trim');
        $state         = input('post.state', '1', 'htmlspecialchars,strip_tags,trim');
        $platformid =Session::get('user.platformId');
        $where     = array('os_isDelete' => 0,'os_platform_id'=>$platformid);
        !empty($username)   && $where['os_buyer_nick']  = array('LIKE', '%' . $username .'%');
        !empty($goodsname)  && $where['og_goods_name']  = array('LIKE', '%' . $goodsname .'%');
        !empty($id)         && $where['os_id']          = array('=', $id);
        !empty($beginTime)  && $where['os_create_time'] = array('>=', strtotime($beginTime));
        !empty($endTime)    && $where['os_create_time'] = array('<=', strtotime($endTime));
        is_numeric($state)  && $where['os_status']      = array('=', $state);

        $roleid =Session::get('user.roleid');
        $userid =Session::get('user.userId');

        if ($roleid!=1){
            $where['os_seller_id']   = $userid;
        }

        $limit = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];
        $join  = [
            ['db_orders_goods og','os.os_id=og.og_order_id', 'LEFT']
        ];
        $page['totalCount']  = Db::name('orders os')->where($where)->join($join)->count();
        $field = 'os_id as id,os_status as state,og_goods_name as goodsName,og_goods_sku as model,os_buyer_nick as nick,os_goods_num as number,og_goods_price as price,os_actual_payprice as pay,og_goods_img as image,os_create_time as createTime';
        //        $order = $this->model->getOrderAll($field, $where, $join, 'os_create_time desc', $limit);
        $order = Db::name('orders os')->where($where)->field($field)->order('os_create_time desc')->join($join)->limit($limit)->select();
        $stateList = Db::name('dictionary')->where(['dt_typeid' => 4])->field('dt_key,dt_value')->order('dt_sort asc')->select();

        $this->assign('stateList', $stateList);
        $this->assign('username',  $username);
        $this->assign('roleId',    session('user.roleid'));
        $this->assign('goodsname', $goodsname);
        $this->assign('orderList', $order);
        $this->assign('page',      $page);
        $this->assign('id',        $id );
        $this->assign('beginTime', $beginTime);
        $this->assign('endTime',   $endTime);
        $this->assign('state',     $state);
        return $this->fetch();
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     * 编辑订单
     * @return mixed
     */
    public function edit(){
        $id = Request::instance()->param('id', '', 'htmlspecialchars,strip_tags,trim');
        empty($id) && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));

        $where = array('os_id' => $id, 'os_isDelete' => 0, 'os_status' => 0);
        if(Request::instance()->isPost()){
            $status = input('post.status', 0, 'intval');
            empty($status) && $this->ajaxReturn(ajaxCallBack(300, '请选择订单状态'));

            $data = [
                'os_status'     => $status,
                'os_update_time' => time(),
                'os_operate_id'  => session('user.userId')
            ];

            $res = model('Orders')->updateOrder($data, $where);
            !$res && $this->ajaxReturn(array('statusCode' => 300, 'message' => '修改失败'));
            $this->ajaxReturn(ajaxCallBack(200, '修改成功', true, 'platform_Order_index'));
        }else {
            $order = model('Orders')->getOrderByOrderId($id);
            !$order && $this->ajaxReturn(ajaxCallBack(300, '订单不存在'));

            $areaModel  = model('common/Area');
            $order['province']   = $areaModel->getAreaById('a_name', $order['os_provinceId'])['a_name'];
            $order['city']       = $areaModel->getAreaById('a_name', $order['os_cityId'])['a_name'];
            $order['region']     = $areaModel->getAreaById('a_name', $order['os_regionId'])['a_name'];

            $this->assign('order', $order);
            $this->assign('id', $id);
            return $this->fetch();
        }
    }

    /**
     * 订单详情
     * @return mixed
     */
    public function detail(){
        $id = Request::instance()->param('id', '', 'htmlspecialchars,strip_tags,trim');
        empty($id) && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));

        $order = model('Orders')->getOrderByOrderId($id);
        !$order && $this->ajaxReturn(ajaxCallBack(300, '订单不存在'));

        $areaModel  = model('common/Area');
        $order['province']   = $areaModel->getAreaById('a_name', $order['os_provinceId'])['a_name'];
        $order['city']       = $areaModel->getAreaById('a_name', $order['os_cityId'])['a_name'];
        $order['region']     = $areaModel->getAreaById('a_name', $order['os_regionId'])['a_name'];

        $stateList = Db::name('dictionary')->where(['dt_typeid' => 4])->field('dt_key,dt_value')->order('dt_sort asc')->select();

        $this->assign('stateList', $stateList);
        $this->assign('order',     $order);
        $this->assign('id',        $id);
        return $this->fetch();
    }

    /**
     * 关闭订单
     */
    public function close(){
        $id = Request::instance()->param('id', '', 'htmlspecialchars,strip_tags,trim');
        empty($id) && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));

        $res = model('Orders')->updateOrderStatus($id, 0, -1);
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => '关闭订单失败'));
        $this->ajaxReturn(array('statusCode' => 200, 'message' => '关闭订单成功', 'tabid'=>'platform_Order_index'));
    }

    /*
     * 发货
     */
    public function delivery(){
        $id = Request::instance()->param('id', '', 'htmlspecialchars,strip_tags,trim');
        empty($id) && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));

        $where = array('os_id' => $id, 'os_isDelete' => 0, 'os_status' => 1);
        if(Request::instance()->isPost()){
            $os_deliver_name = input('os_deliver_name', '', 'htmlspecialchars,strip_tags,trim');
            $os_deliver_num  = input('os_deliver_num', '', 'htmlspecialchars,strip_tags,trim');

            !$os_deliver_name && $this->ajaxReturn(ajaxCallBack(300, '请输入配送方式'));
            !$os_deliver_num  && $this->ajaxReturn(ajaxCallBack(300, '请输入物流号码'));

            $data = array(
                'os_deliver_name' => $os_deliver_name,
                'os_deliver_num'  => $os_deliver_num,
                'os_update_time'  => time(),
                'os_deliver_time' => time(),
                'os_operate_id'   => session('user.userId'),
                'os_auto_receiver_time'=>time()+864000,
                'os_status'       => 2,
            );
            $res = Db::name('orders')->where($where)->update($data);
            !$res && $this->ajaxReturn(array('statusCode' => 300, 'message' => '发送失败'));
            $this->ajaxReturn(ajaxCallBack(200, '发送成功', true, 'platform_Order_index'));
        }else {
            $this->assign('id', $id);
            return $this->fetch();
        }
    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }

    /**
     * 手动确认订单到账
     */
    public function confirmOrderforOrderId(){

        $id =Request::instance()->param('id','0');
        if (empty($id)){
            $this->ajaxReturn(ajaxCallBack(300,'参数错误!'));
        }

        $res =Db::name('orders')->field('os_status')->where('os_id',$id)->find();
        if (empty($res)){
            $this->ajaxReturn(ajaxCallBack(300,'没有该订单信息!'));
        }
        if ($res['os_status']>0){
            $this->ajaxReturn(ajaxCallBack(300,'该订单已支付成功，请勿重复处理!'));
        }
        $data['orderID'] =$id;
        $paydata =datang_interface(Config::get('dttxapi.getOrderInfoUrl'),$data,700003);
        if (false===$paydata){
            return $this->ajaxReturn(ajaxCallBack(300,'获取订单信息网络超时，请重试!'));
        }
        if (1001==$paydata['id']){
            if (!empty($paydata['info'])){
                $post = $paydata['info'];
                if (!empty($post)){
                    Logs::write(700004,$post,$post['shopOrderID'],'','Ym');
                }else{
                    return $this->ajaxReturn(ajaxCallBack(300,'订单信息获取失败请重试!'));
                }
                $sign =$post['sign'];
                unset($post['sign']);
                ksort($post);
                $signkey = md5( http_build_query($post)."&RDSDDF43E4354EWSSD7FW4FSD1");
                if ($signkey === $sign ) {
                    $shopOrderID = $post['shopOrderID'];
                    $orderId = $post['orderID'];
                    $payTime = strtotime($post['payTime']);
                    $orderAmount = $post['orderAmount'] / 100;
                    $acccount_id = getTimeMarkID();
                    if (!empty($shopOrderID) && !empty($orderId)) {
                        $res = Db::name('orders')->where(['os_id' => $shopOrderID])->lock(true)->find();
                        if (empty($res)) {
                            return $this->ajaxReturn(ajaxCallBack(300,'没有该订单信息!'));
                        }
                        $payindata = Db::name('account_pay_in')->where(['ap_shop_order_id' => $orderId])->find();
                        $acorderdata = Db::name('account_order')->where(['ao_order_id' => $orderId])->find();
                        if (!empty($payindata) || !empty($acorderdata)) {
                            Logs::writeMongodb(200050,'',$shopOrderID,'订单ID:' . $shopOrderID . "已入库，无需重复计算",'','Ym');

                            return $this->ajaxReturn(ajaxCallBack(300,'该订单入账信息已存在，请勿重复操作!'));
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
                                'ap_only_pay' => $post['onlyPay'],
                                'ap_pay_channel' => $post['payChannel'],
                                'ap_bus_id' => $post['busID'],
                                'ap_state' => $post['state']==3?1:0,
                                'ap_create_time' => time()
                            ];
                            $result2 = Db::name('account_pay_in')->insert($pay_in);
                            if (!$result2) {
                                Logs::writeMongodb(200050,'db_account_pay_in',$shopOrderID,"入账数据创建失败",$pay_in,'Ym');
                                return $this->ajaxReturn(ajaxCallBack(300,'入账数据创建失败!'));
                            }else{
                                Logs::writeMongodb(200051,'db_account_pay_in',$shopOrderID,'订单入账记录创建成功',$pay_in,'Ym');
                            }

                            if ($post['onlyPay'] == 2) {
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
                                    Logs::writeMongodb(200050,'db_account_tangbao',$shopOrderID,'订单唐宝入账失败!',$tangbao_pay_in,'Ym');
                                }else{
                                    Logs::writeMongodb(200051,'db_account_tangbao',$shopOrderID,'订单号唐宝入账成功!',$tangbao_pay_in,'Ym');
                                }
                            }

                            $accorder =Db::name('account_order')->field('ao_id')->where('ao_order_id',$id)->find();
                            if (!empty($accorder)){
                                return $this->ajaxReturn(ajaxCallBack(300,'分润订单已生成，请勿重复操作!'));
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
                                        Logs::writeMongodb(200050,'db_account_order',$shopOrderID,"分润订单创建失败",$account_order_data,'Ym');
                                        throw new Exception('分润订单计算失败!');
                                    }else{
                                        Logs::writeMongodb(200051,'db_account_order',$shopOrderID,'订单分润订单创建成功',$account_order_data,'Ym');
                                    }
                                }
                                $result = Db::name('orders')->where(['os_id' => $shopOrderID])->update(['os_status' => 1, 'os_pay_order_id' => $orderId, 'os_pay_time' => $payTime,'os_pay_type'=>$post['onlyPay']]);
                                if (!$result) {
                                    Logs::writeMongodb(200050,'db_orders',$shopOrderID,'订单状态更新失败',$account_order_data,'Ym');
                                    throw new Exception("订单状态更新失败,订单号:$shopOrderID");
                                }else{
                                    Logs::writeMongodb(200051,'db_orders',$shopOrderID,'订单状态更新成功',$account_order_data,'Ym');
                                }
                                Db::commit();
                                Logs::writeMongodb(200051,'db_orders',$shopOrderID,'手动订单入账处理成功',$account_order_data,'Ym');
                                return $this->ajaxReturn(ajaxCallBack(200,'订单入账处理成功!'));
                            } catch (\Exception $e) {
                                Db::rollback();
                                return $this->ajaxReturn(ajaxCallBack(300,'订单入账处理失败!'));
                            }
                        }
                    }

                } else {

                    Logs::writeMongodb(200050,'notifyurl',$post['shopOrderID'],"订单数据验证失败:",$post,'Ym');
                    return $this->ajaxReturn(ajaxCallBack(300,'订单数据验证失败'));
                }
            }
        }else{
            return $this->ajaxReturn(ajaxCallBack(300,'未找到该订单支付信息，请确认是否支付!'));
        }
    }

}