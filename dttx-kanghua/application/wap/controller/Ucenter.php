<?php
namespace app\wap\controller;
use app\common\controller\Wap;
use app\common\model\Orders;
use app\common\model\ShopItems;
use app\common\model\Area;
use app\common\model\UserPlatform;
use app\common\tools\Logs;
use app\payment\model\Account;
use think\Cache;
use think\Exception;
use think\Session;
use think\Db;
use think\Request;

/**
 * 个人中心
 * User: lirong
 * Date: 2017/7/9
 * Time: 11:35
 */
class Ucenter extends Wap{




    public function index(){

        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->redirect('login/index');
        }
        $userid =Session::get('user.userId');
        $userplatformModel =new UserPlatform();
        $res =$userplatformModel->findUserPlatformInfoByUpid($userid);
//dump($res);die;
        $account =new Account();
        $accountres =$account->findAccountByUid($userid);
        if ($accountres['code']=='300'){
            $accountres['a_freeMoney']=$accountres['data'];
        }else{
            $code = $accountres['code'];
            $accountres = $accountres['data'];
            $accountres['code'] = $code;
        }

        $orders =new Orders();
        //我的订单
        $count = $orders->getOrderCount($userid);

        //我的经销商订单
        $sellercount = $orders->getSellerOrderCount($userid);

        //是否是经销商或者是经销商是否被冻结
        $shopkeeper  = Db::name('shopkeeper')->where(['s_userId' => $userid, 's_state' => 'pass', 's_isBlocked' => 0])->count();

        //我的经销商品
        $list = array();
        $shopitems = new ShopItems();
        $field  = 'si_id as id,bg_name as name,bg_image as image,bg_scoreReward,bg_price as price';
        $join   = [['db_base_goods bg', 'bg.bg_id=a.si_goodsId']];
        if($shopkeeper){
            $where  = array('si_createId' => $userid, 'si_isSale' => 1, 'si_isDelete' => 0);
            $list   = $shopitems->getShopItemsAll($field, $where, $join, 'si_id desc');
            if($list){
                foreach($list as &$val){
                    $val['score'] = $val['price'] * $val['bg_scoreReward'];
                }
            }
        }

        //随机展示一条未被冻结的经销商的商品
        $where  = array('si_isSale' => 1, 'si_isDelete' => 0,'bg_isSale' => 1, 'bg_isDelete' => 0, 's_isBlocked' => 0, 's_state' => 'pass', 'si_projectId' => session('user.platformId'));
        $order  = 'rand()';
        $join[] = ['db_shopkeeper s', 's.s_userId=a.si_createId', 'LEFT'];
        $goods  = $shopitems->getShopItemsOne($field, $where, $join, $order);
        $goods && $goods['score'] = $goods['price'] * $goods['bg_scoreReward'];

        $this->assign('list',  $list);
        $this->assign('goods', $goods);
        $this->assign('sellercount',$sellercount);
        $this->assign('count',$count);
        $this->assign('member',$res);
        $this->assign('accres',$accountres);
        $this->assign('title','个人中心');
        return $this->fetch();
    }


    /*
     * 我的邀请人列表
     * */
    public function inviter(){

        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->redirect('login/index');
        }

        $userid = Session::get('user.userId');
        $code   = input('code', '', 'htmlspecialchars,strip_tags,trim');
        (!$code || !is_numeric($code)) && $this->redirect('ucenter/index');

        $cacheKey  = md5('inviter_list' . $userid . $code);
        if(!($list = Cache::get($cacheKey))){
            $userplatformModel = new UserPlatform();
            $field= 'u_nick as nick,u_name as name,up_create_time as createTime,up_provinceId as province,up_cityId as city,up_regionId as region,u_tel as tel';
            $list = $userplatformModel->findInviterListByCode($code, $field);
            if($list){
                $areaModel = new Area();
                foreach($list as $key => &$val){
                    $val['province'] = $areaModel->getAreaById('a_name', $val['province'])['a_name'];
                    $val['city']     = $areaModel->getAreaById('a_name', $val['city'])['a_name'];
                    $val['region']   = $areaModel->getAreaById('a_name', $val['region'])['a_name'];
                }
            }

            Cache::set($cacheKey, $list, 3600);
        }

        $this->assign('list', $list);
        $this->assign('title', '我的邀请会员');
        return $this->fetch();
    }

    /**
     * 邀请码
     * @return mixed
     */
    public function inviteCode(){

        $code   = input('code', '', 'htmlspecialchars,strip_tags,trim');
        (!$code || !is_numeric($code)) && $this->redirect('ucenter/index');

        $this->assign('title', '邀请码');
        $this->assign('code', $code);
        return $this->fetch();
    }

    /**
     * 二维码
     */
    public function qcode(){
        $code   = input('code', '', 'htmlspecialchars,strip_tags,trim');
        (!$code || !is_numeric($code)) && $this->redirect('ucenter/index');

        $url = url('Login/index', ['code' => $code], '', true);

        return \QRcode::png($url, false, 'L',6);
    }

    /*
     * 会员升级
     * */
    public function upgrade(){

        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->redirect('login/index');
        }
        $platformId =$this->plafromdata['pl_id'];

        $userlevel = Db::name('user_level')->where(['ul_platform_id'=>$platformId])->select();
        $userId = Session::get('user.userId');
        $user   = Db::name('user_platform')->alias('up')->join('user_level ul','up.up_user_level_id=ul.ul_id','left')->where(['up_id' => $userId])->field('up_id,up_user_level_id,ul_name')->find();
        $this->assign('user',$user);
        $this->assign('userlevel',$userlevel);
        $this->assign('title', '会员升级');
        return $this->fetch();
    }

    /*
     * 会员升级处理
     * */
    public function upgradeDeal(){
        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->ajaxReturn(ajaxCallBack(301, array('msg' => '您未登录', 'url' => url('login/index'))));
        }

        $userId = Session::get('user.userId');

        $userPlateForm = new UserPlatform();
        $res = $userPlateForm->upgrade($userId, 1);
        $res === false && $this->ajaxReturn(ajaxCallBack(300, '升级失败'));
        $this->ajaxReturn(ajaxCallBack(200, '升级成功'));
    }

    public function upgradepay(){
        $uid =input('uid','0','intval');

        $levelid =input('levelid','0','intval');
        $platform_id =Session::get('user.platformId');
        $userlevel = Db::name('user_level')->where(['ul_id'=>$levelid,'ul_platform_id'=>$platform_id])->find();

        if (empty($userlevel)){
            $this->error('升级会员等级不存在，请确认!',null);
        }

        if ($userlevel['ul_status']==0){
            $this->error('该级别已被禁用无法升级请确认!',null);
        }

        $res =Db::name('user_platform')->where(['up_id'=>$uid])->find();
        if (empty($res)){
            $this->error('没有找到该会员相关信息，请刷新后再试!',null);
        }

        if ($res['up_user_level_id']>0){
            $this->error('该会员已升级,请勿重复操作!',null);
        }

        $userid =Session::get('user.userId');

        //查找收款方
        $sellerdata =Db::name('user_platform')->where(['up_dttx_uid'=>$this->plafromdata['pl_dttx_uid'],'up_plateform_id'=>$this->plafromdata['pl_id']])->find();
        if (empty($sellerdata)){
            $this->error('系统异常会员升级失败，请稍后再试！');
            exit();
        }
        $ordersid=getTimeMarkID();
        $userlevlprice =$userlevel['ul_money'];
        Db::startTrans();
        try{
            $orders =[
                'os_id' =>$ordersid,
                'os_order_price'=>$userlevlprice,
                'os_actual_payprice'=>$userlevlprice,
                'os_seller_id'=>$sellerdata['up_id'],
                'os_buyer_id'=>$userid,
                'os_buyer_nick'=>Session::get('user.username'),
                'os_buyer_dttx_uid'=>Session::get('user.dttxId'),
                'os_score'=>$userlevlprice*100,
                'os_seller_phone'=>$this->plafromdata['pl_contact'],
                'os_create_time'=>time(),
                'os_bus_id'=>1,
                'os_platform_id'=>$platform_id,
                'os_buyer_note'=>'大唐分销系统会员升级'
            ];
            $res =Db::name('orders')->insert($orders);
            if (!$res){
                Logs::writeMongodb(200010,'db_orders','创建订单信息失败，请重试',$orders,'Ym','fenxiao_orders',Session::get('user.username'));
                throw new Exception('创建订单信息失败，请重试!');
            }
            $orders_goods=[
                'og_platform_id' =>$platform_id,
                'og_order_id'=>$ordersid,
                'og_goods_id'=>$levelid,
                'og_shopid'=>0,
                'og_goods_name'=>'大唐天下分销系统会员升级',
                'og_goods_sku'=>'',
                'og_goods_url'=>url('wap/ucenter/upgrade'),
                'og_goods_price'=>$userlevlprice,
                'og_goods_num'=>1,
                'og_goods_img'=>'/static/wap/images/upgrade.png',
                'og_create_time'=>time()
            ];
            $res1=Db::name('orders_goods')->insert($orders_goods);
            if (!$res1){
                Logs::writeMongodb(200010,'db_orders_goods','创建订单商品记录失败，请重试',$orders_goods,'Ym','fenxiao_orders',Session::get('user.username'));
                throw new Exception('创建订单商品记录失败，请重试!');
            }
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();

            $this->error($e->getMessage(),null);
            exit();
        }
        $this->redirect('ucenter/payorder',['id'=>$ordersid]);
    }

    public function payorder(){

        $id=Request::instance()->param('id','');
        if (!is_numeric($id)){
            $this->assign('errormessage','参数错误，请重新提交!');
        }

        $res =Db::name('orders')->alias('or')
            ->field('or.*,p.pl_dttx_uid,pl_dttx_nick')
            ->where(['os_id'=>$id,'os_isDelete'=>0,'os_status'=>0])
            ->join('platform p','or.os_platform_id=p.pl_id')->find();
        if (empty($res)){
            $this->error('订单未找到，请确认该订单提交成功!',null);
        }

        if ($res['os_status']>0){
            $this->error('该订单已支付，请勿重复支付','wap/order/center');
        }

        $users =Db::name('user_platform')->where(['up_id'=>$res['os_buyer_id']])->find();

        if ($users['up_user_level_id']>0){
            Db::name('orders')->where(['os_id'=>$id])->update(['os_status'=>-1]);
            $this->error('您已经升级为会员，无须重复升级!','wap/order/center');
        }

        $this->assign('title','确认支付');
        $this->assign('goodsName','会员升级');
        $this->assign('data',$res);
        return $this->fetch();
    }


    /*
     * 余额明细
     * */
    public function balance(){

        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->redirect('login/index');
        }

        $userId = Session::get('user.userId');
        $user   = Db::name('user_platform up')->where(['up_id' => $userId])
                ->join('db_user u', 'u.u_id=up.up_uid', 'LEFT')->join('db_account a', 'a.a_uid=up.up_id')
                ->field('u_auth,a_payPwd,a_freeMoney as freeMoney')->find();
        !$user && $this->redirect('login/index');

        $balance= $user['freeMoney'] ?: 0;

        //是否绑定银行卡
        $bankCount = Db::name('account_bank')->where(['ab_uid' => $userId, 'ab_isDelete' => 0])->count();
        //是否设置支付密码
        $isSetPayPwd = !empty($user['a_payPwd']) ? 1 : 0;
        //是否身份证认证
        $isAuth = $user['u_auth'][2] == 1 ? 1 : 0;
        //是否满足所有提现条件
        $isSatisfy = (!empty($user['a_payPwd']) && $bankCount && $isAuth) ? 1 : 0;

        //余额记录
        $where  = array('ca_uid' => $userId);
        $field  = 'ca_id as id,ca_money as money,ca_balance_type,ca_type,ca_create_time';
        $data   = Db::name('account_cash_tran')->where($where)->field($field)->order('ca_create_time desc')->select();

        $balanceType = Db::name('dictionary')->where(['dt_typeid' => 5])->field('dt_key,dt_value')->select();
        $balanceType = array_column($balanceType, 'dt_value', 'dt_key');

        $dataIn = array();
        $dataOut= array();
        if($data){
            foreach($data as $key => $val){
                $val['ca_balance_type'] = $balanceType[$val['ca_balance_type']];
                if($val['ca_type'] == -1){
                    $dataOut[] = $val;
                }else{
                    $dataIn[]  = $val;
                }
            }
        }

        $this->assign('balanceType', $balanceType);
        $this->assign('isSatisfy',   $isSatisfy);
        $this->assign('balance',     $balance);
        $this->assign('isSetPayPwd', $isSetPayPwd);
        $this->assign('bankCount',   $bankCount);
        $this->assign('dataOut', $dataOut);
        $this->assign('dataIn',  $dataIn);
        $this->assign('isAuth',  $isAuth);
        $this->assign('title', '余额明细');
        return $this->fetch();
    }

    public function balanceDetail(){

        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->redirect('login/index');
        }

        $id     = input('id', '', 'htmlspecialchars,strip_tags,trim');
        $userId = Session::get('user.userId');

        $where  = array('ca_uid' => $userId, 'ca_id' => $id);
        $field  = 'ca_id as id,ca_money as money,ca_balance_type,ca_type,ca_create_time,ca_order_id,ca_memo';
        $data   = Db::name('account_cash_tran')->where($where)->field($field)->find();
        !$data && $this->redirect('ucenter/balance');

        $balanceType = Db::name('dictionary')->where(['dt_typeid' => 5])->field('dt_key,dt_value')->select();
        $balanceType = array_column($balanceType, 'dt_value', 'dt_key');

        $this->assign('balanceType', $balanceType);
        $this->assign('data', $data);
        $this->assign('title', '异动详情');
        return $this->fetch();
    }

    /*
     * 提现
     * */
    public function withdraw(){
        $userId     = Session::get('user.userId');
        $platformId = Session::get('user.platformId');
        if(Request::instance()->isPost()){
            if (!Session::has('user') || Session::get('user.userId')==''){
                $this->ajaxReturn(ajaxCallBack(301, array('msg' => '您未登录', 'url' => url('login/index'))));
            }

            $balance = input('balance', 0, 'floatval');
            $bankId  = input('bankId', 0, 'intval');
            $password= input('password', '', 'htmlspecialchars,strip_tags,trim');

            !$balance && $this->ajaxReturn(ajaxCallBack(300, '提现金额不能为空'));
            !$bankId && $this->ajaxReturn(ajaxCallBack(300, '请选择银行卡'));
            $balance < 0 && $this->ajaxReturn(ajaxCallBack(300, '提现金额不能小于0'));
            $balance > 50000 && $this->ajaxReturn(ajaxCallBack(300, '提现金额不能超过单笔最大金额'));
            $balance < 1 && $this->ajaxReturn(ajaxCallBack(300, '提现金额不能小于1元'));
            !preg_match('/^\d{6}$/', $password) && $this->ajaxReturn(ajaxCallBack(300, '密码为6位数字'));

            $userplatformModel = new UserPlatform();
            $user   = $userplatformModel->findUserPlatformInfoByUpid($userId);
            !$user && $this->redirect('login/index');

            //获取账户信息
            $account = Db::name('account')->where(['a_uid' => $userId, 'a_platform_id' => $user['up_plateform_id'], 'a_states' => array('neq', -1)])->find();
            !$account && $this->ajaxReturn(ajaxCallBack(300, '账户不存在'));
            $account['a_states'] == 0 && $this->ajaxReturn(ajaxCallBack(300, '该账户已冻结，请联系管理员'));
            ($account['a_freeMoney'] <= 50000 && $balance > $account['a_freeMoney']) && $this->ajaxReturn(ajaxCallBack(300, '提现额不能超过您的账户余额'));

            //提现条件 设置了支付密码，绑定了银行卡，已通过身份认证
            $user['u_auth'][2] != 1 && $this->ajaxReturn(ajaxCallBack(300, '您未通过实名认证,请返回大唐天下认证'));
            $bankCount = Db::name('account_bank')->where(['ab_uid' => $userId, 'ab_isDelete' => 0])->count();
            empty($account['a_payPwd']) && $this->ajaxReturn(ajaxCallBack(301, array('msg' => '请设置支付密码', 'url' => url('user/setPayPwd'))));
            !$bankCount && $this->ajaxReturn(ajaxCallBack(301, array('msg' => '请先绑定银行卡', 'url' => url('Bank/create'))));

            $time = date('Y-m-d H:i:s');

            //密码输入错误次数统计及处理
            $where = ['pr_userId' => $userId, 'pr_platformId' => $platformId, 'pr_createTime' => array('BETWEEN', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])];
            $count = Db::name('paypwd_record')->where($where)->count();
            if($count >= 3){
                $this->ajaxReturn(ajaxCallBack(300, '您当天支付密码输入错误次数已到3次'));
            }
            if(getSuperMD5($password . $account['a_payPwdNew']) != $account['a_payPwd']){
                $record = array(
                    'pr_userId'     => $userId,
                    'pr_platformId' => $platformId,
                    'pr_createTime' => $time,
                    'pr_meno'       => '支付密码第' . ($count + 1) .'次输入错误',
                    'pr_ip'         => get_client_ip(0, true)
                );

                Db::name('paypwd_record')->insert($record);
                Logs::writeMongodb(400000,'db_account_cash_out','提现密码错误！',$record,'','fenxiao_account');
                $lastCount = 3 - ($count + 1);
                $this->ajaxReturn(ajaxCallBack(302, '支付密码不正确，你还可以输入' . $lastCount . '次'));
            }

            //查询当天是否有提现记录
            $condition['co_uid'] = $userId;
            $condition['co_arriveDateTime'] = array('BETWEEN', array(date('Y-m-d'), date('Y-m-d') . ' 23:59:59'));
            if(Db::name('account_cash_out')->where($condition)->count()){
                $this->ajaxReturn(ajaxCallBack(300, '一天只能提现一次'));
            }

            //获取所选择的银行账户信息
            $bank = Db::name('account_bank')->where(['ab_id' => $bankId, 'ab_uid' => $userId])->field('ab_type_id,ab_bank_name,ab_card_number,ab_account_name,ab_bank_address')->find();
            if(!$bank){
                $this->ajaxReturn(ajaxCallBack(300, '该银行卡不存在'));
            }

            Db::startTrans();
            try{
                $data = array(
                    'co_caid' => getTimeMarkID(),
                    'co_uid'  => $userId,
                    'co_unick' => $user['u_nick'],
                    'co_arriveDateTime' => $time,
                    'co_money' => $balance,
                    'co_platform_id' => $user['up_plateform_id'],
                    'co_tax'   => 0,
                    'co_ratio' => 0,
                    'co_day'   => 3,//提现天数
                    'co_day_time' => date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d')+3,date('Y'))),
                    'co_toCardType' => $bank['ab_type_id'],
                    'co_account'    => $bank['ab_card_number'],
                    'co_cardmaster' => $bank['ab_account_name'],
                    'co_cardaddr'   => $bank['ab_bank_address'],
                    'co_aexId'      => $bankId,
                    'co_bankName'   => $bank['ab_bank_name'],
                    'co_memo'=>'账号余额提现'
                );

                $res = Db::name('account_cash_out')->insert($data);
                if(!$res){
                    Logs::writeMongodb(800000,'db_account_cash_out','创建资金出账表失败',$data,'Ym','fenxiao_account');
                    throw new Exception('创建资金出账表失败');
                }

                $info = array(
                    'ca_id'          => $data['co_caid'],
                    'ca_uid'         => $userId,
                    'ca_unick'       => $user['u_nick'],
                    'ca_platform_id' => $user['up_plateform_id'],
                    'ca_aid'         => $account['a_id'],
                    'ca_money'       => $balance,
                    'ca_balance'     => $account['a_freeMoney'] - $balance,
                    'ca_balance_type'=> 3,//提现
                    'ca_type'        => -1,
                    'ca_business_id' => 1,
                    'ca_create_time' => $time,
                    'ca_memo'        => '账号余额提现'
                );

                $res = Db::name('account_cash_tran')->insert($info);
                if(!$res){
                    Logs::writeMongodb(800000,'db_account_cash_tran','创建资金进出账异动表失败',$data,'Ym','fenxiao_account');
                    throw new Exception('创建资金进出账异动表失败');
                }

                $crc = Account::getVerifyAndOutCrcCode($account, false);
                if(!$crc){
                    throw new Exception('账户异常，系统自锁请联系客服人员解锁');
                }

                $account['a_freeMoney']   -= $balance;
                $account['a_frozenMoney'] += $balance;

                $account['a_crc'] = Account::getVerifyAndOutCrcCode($account, true);
                $where = ['a_id' => $account['a_id'], 'a_platform_id' => $account['a_platform_id']];
                unset($account['a_id']);
                $res = Db::name('account')->where($where)->update($account);
                if(!$res){
                    Logs::writeMongodb(800000,'db_account','更新账户失败',$account,'Ym');
                    throw new Exception('更新账户失败');
                }

                Db::commit();
                Logs::writeMongodb(800001,'db_account','账户提现成功',$account,'Ym');
                $this->ajaxReturn(ajaxCallBack(200, '提现成功'));
            }catch (\Exception $e){
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300, '提现失败'));
            }
        }else{
            if (!Session::has('user') || Session::get('user.userId')==''){
                $this->redirect('login/index');
            }
            $balance= Db::name('account')->where(['a_uid' => $userId])->field('a_freeMoney as freeMoney')->find();
            $balance= $balance ? $balance['freeMoney'] : 0;

            $field    = 'ab_id as id,ab_bank_name as bankName,ab_card_number as cardNumber,ab_is_default_card';
            $bankList = Db::name('account_bank')->where(array('ab_uid'  => $userId, 'ab_isDelete' => 0))->field($field)->select();
            $defaultBank = '';
            if($bankList){
                foreach($bankList as $key=>&$val){
                    if($val['ab_is_default_card'] == 1){
                        $defaultBank = $val['bankName'];
                        $defaultBank = substr_replace($val['cardNumber'], $defaultBank, 0, strlen($val['cardNumber']) -  4);
                    }
                    $val['cardNumber'] = substr_replace($val['cardNumber'], '**** **** **** **** ', 0, strlen($val['cardNumber']) -  4);
                }
            }

            $currentMonth   = date('Y-m');
            $lastMonth      = date('Y-m', strtotime('-1 month'));
            $lastThreeMonth = date('Y-m', strtotime('-3 month'));

            $this->assign('bankList', $bankList);
            $this->assign('balance',  $balance);
            $this->assign('defaultBank',  $defaultBank);
            $this->assign('currentMonth', $currentMonth);
            $this->assign('lastMonth',      $lastMonth);
            $this->assign('lastThreeMonth', $lastThreeMonth);
            $this->assign('title', '余额提现');
            return $this->fetch();
        }
    }


    /*
     * 提现记录
     * */
    public function withdrawList(){

        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->redirect('login/index');
        }

        $userId = Session::get('user.userId');
        $month  = input('month', '', 'htmlspecialchars,strip_tags,trim');

        $where  = array('co_uid' => $userId);
        if($month){
            $m   = date('m', strtotime($month));
            if(trim(date('m'), '0') - trim($m, '0') >= 3){//超过三个月的提现记录
                $month = date('Y-m-d', strtotime('-3 month'));
                $endMonth = date('Y-m-d H:i:s');
            }else{
                $day = date('t', strtotime($month));
                $endMonth = $month . '-' . $day . ' 23:59:59';
            }
            $where['co_arriveDateTime'] = array('BETWEEN', [$month, $endMonth]);
        }

        $field  = 'co_caid as id,co_money as money,co_state,co_arriveDateTime';
        $data   = Db::name('account_cash_out')->where($where)->field($field)->order('co_arriveDateTime desc')->select();
        $state  = array(-1 => '撤销', '未结算', '结算', '在途资金');

        $this->assign('state', $state);
        $this->assign('data', $data);
        $this->assign('title', '提现记录');
        return $this->fetch();
    }

    /*
     * 提现明细
     * */
    public function withdrawDetail(){

        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->redirect('login/index');
        }

        $id     = input('id', '', 'htmlspecialchars,strip_tags,trim');
        $userId = Session::get('user.userId');

        $where  = array('co_uid' => $userId, 'co_caid' => $id);
        $field  = 'co_caid as id,co_money as money,co_state,co_arriveDateTime,co_day,co_day_time,co_reason,co_memo,co_account,co_bankName,co_ratio,ab_bank_name';
        $data   = Db::name('account_cash_out co')->where($where)->join('db_account_bank ab', 'ab.ab_id=co.co_aexId', 'LEFT')->field($field)->find();
        !$data && $this->redirect('ucenter/withdrawList');

        $data['co_account'] = substr_replace($data['co_account'], '**** **** **** **** ', 0, strlen($data['co_account']) -  4);
        $data['fee']        = $data['money'] * $data['co_ratio'];
        $data['expectTime'] = date("Y-m-d", strtotime("+7days", strtotime($data['co_arriveDateTime'])));
        $data['co_bankName']= $data['co_bankName'] ?: $data['ab_bank_name'];
        $state  = array(-1 => '撤销', '未结算', '结算', '在途资金');

        $this->assign('state', $state);
        $this->assign('data',  $data);
        $this->assign('title', '提现明细');
        return $this->fetch();
    }


}