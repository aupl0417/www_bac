<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/6 0006
 * Time: 9:13
 */

namespace app\wap\controller;

use app\common\controller\Wap;
use app\common\model\Area;
use app\common\model\Orders;

use app\common\tools\Logs;
use think\Cache;
use think\Config;
use think\Db;
use think\Exception;
use think\Log;
use think\Request;
use think\Session;

class Order extends Wap{

    public function index()
    {
        // TODO: Implement index() method.
    }


    /**
     * 订单中心
     * @return mixed
     */
    public function center(){
        $userid =Session::get('user.userId');
        $typeid=Request::instance()->param('type','all','trim');
        $input['field']="os_id,os_actual_payprice,os_status,os_deliver_price,os_goods_num,os_seller_phone,os_buyer_note,os_bus_id";
        $input['condition']=[
            'os_buyer_id'  =>$userid
        ];
        $ordersModel =new Orders();
        $list = $ordersModel->getOrderlist($input);

        $input['condition']['os_status']= 0;
        $waitpaylist = $ordersModel->getOrderlist($input);

        $input['condition']['os_status']= 1;
        $waitsend = $ordersModel->getOrderlist($input);

        $input['condition']['os_status']= 2;
        $waitrecvied = $ordersModel->getOrderlist($input);

        $count =$ordersModel->getOrderCount($userid);
        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->assign('typeid',$typeid);
        $this->assign('waitpaylist',$waitpaylist);
        $this->assign('waitsend',$waitsend);
        $this->assign('waitrecvied',$waitrecvied);
        $this->assign('title','我的订单');
        return $this->fetch();
    }

    /**
     * 我的经销商订单
     * @return mixed
     */
    public function shopcenter(){
        $userid =Session::get('user.userId');
        $typeid=Request::instance()->param('type','all','trim');
        $input['field']="os_id,os_actual_payprice,os_status,os_deliver_price,os_goods_num,os_seller_phone";
        $input['condition']=[
            'os_seller_id'  =>$userid
        ];
        $ordersModel =new Orders();
        $list = $ordersModel->getOrderlist($input);

        $input['condition']['os_status']= 0;
        $waitpaylist = $ordersModel->getOrderlist($input);

        $input['condition']['os_status']= 1;
        $waitsend = $ordersModel->getOrderlist($input);

        $input['condition']['os_status']= 2;
        $waitrecvied = $ordersModel->getOrderlist($input);

        $count =$ordersModel->getSellerOrderCount($userid);
        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->assign('typeid',$typeid);
        $this->assign('waitpaylist',$waitpaylist);
        $this->assign('waitsend',$waitsend);
        $this->assign('waitrecvied',$waitrecvied);
        $this->assign('title','我的经销商订单');
        return $this->fetch();
    }

    public function docreate(){

        $sid =input('post.sid','0','intval');
        $number =input('post.number','1','intval');

        if (empty($sid) || empty($number)){
            $this->redirect('Store/index');
        }

        Session::set('order',['sid'=>$sid,'number'=>$number]);

        if (!Session::has('user') || Session::get('user.userId')==''){
            Session::set('buy_goods_jump_url', url('order/createOrder'));
            $this->redirect('login/index');
//            $this->ajaxReturn(ajaxCallBack(301, array('msg' => '您未登录', 'url' => url('login/index'))));
        }
    //    Cache::set('order' . Session::get('user.userId'), ['sid'=>$sid,'number'=>$number]);
        $this->redirect('order/createOrder');

    }

    /**
     * 订单详情
     */
    public function orderdetail(){

        $id =Request::instance()->param('id','');
        if (empty($id) || !is_numeric($id)){
            $this->error('订单编号错误，请返回上一页重试!',null);
        }
        $orders =new Orders();
        $data =$orders->getOrderAndGoodsByOrderId($id);
        if (empty($data)){
            $this->error('订单信息不存在!',null);
        }
        $timer = $data['os_auto_receiver_time'] -time();
        if ($timer>3600){
            $day =intval($timer/86400)<0?0:intval($timer/86400);
            $hourtimer =$timer -$day*86400;
            $hour = intval($hourtimer/3600)<0?0:intval($hourtimer/3600);
            $this->assign('outtime',$day."天".$hour."小时");
        }else{
            $minut =intval($timer/60);
            $this->assign('outtime',"0小时".$minut."分");
        }

        $this->assign('data',$data);
        $this->assign('title','订单详情');
        return $this->fetch();

    }

    /**
     * 查看物流
     * @return mixed
     */
    public function lookexpress(){

        $id =Request::instance()->param('id','');
        if (empty($id) || !is_numeric($id)){
            $this->error('订单编号错误，请返回上一页重试!',null);
        }

        $orders =new Orders();
        $data =$orders->getOrderAndGoodsByOrderId($id);
        if (empty($data)){
            $this->error('订单信息不存在!',null);
        }

        $this->assign('data',$data);
        $this->assign('title','查看物流');

       return $this->fetch();
    }


    /**
     * 创建订单页面
     */
    public function createOrder(){

        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->ajaxReturn(ajaxCallBack(301, array('msg' => '您未登录', 'url' => url('login/index'))));
        }

        $aid = input('aid', 0, 'intval');

        $sid =Session::get('order.sid');
        $number =Session::get('order.number');
        $asid   = input('get.asid', '', 'intval');//默认收货地址id或者在地址管理中选择的收货地址id

        if (empty($sid) || empty($number)){
            $this->error('商品信息为空，重新选择商品！',null);
        }

        $res = Db::name('shop_items')->alias('si')->field('si_id,bg_id,bg_name,bg_image,bg_price,bg_model,si_createId,bg_scoreReward')->join('base_goods bg','si.si_goodsId=bg.bg_id')->where(['si_id'=>$sid,'si_isSale'=>1])->find();
        if (empty($res)){
//            $this->assign('res','');
            Logs::writeMongodb(600000,'shop_items',$sid,'商品信息不存在'.$sid,'','','fenxiao_goods');
            $this->error('您选择的商品已下架或已被删除，请重新选择!','store/index');
        }

        $userid =Session::get('user.userId');
        $map['ad_userId']=$userid;
        if (!empty($asid)){
            $map['ad_id'] = $asid;
        }
        $address =Db::name('user_delivery_address')->where($map)->order('ad_isDefault','desc')->find();

        $area =new Area();
        if (!empty($address)){
            if (!empty($address['ad_provinceId'])){
                $address['province']=$area->getAreaNameById($address['ad_provinceId']);
            }

            if (!empty($address['ad_cityId'])){
                $address['city']=$area->getAreaNameById($address['ad_cityId']);
            }

            if (!empty($address['ad_regionId'])){
                $address['region']=$area->getAreaNameById($address['ad_regionId']);
            }

        }

        $this->assign('address',$address);
        $this->assign('platformdata',$this->plafromdata);
        $this->assign('order',$res);
        $this->assign('number',$number);
        $this->assign('title','订单提交');
        return $this->fetch();

    }

    /**
     * 生成订单
     */
    public function generate(){
        if (!Session::has('user') || Session::get('user.userId')==''){
            $this->ajaxReturn(ajaxCallBack(301, array('msg' => '您未登录', 'url' => url('login/index'))));
        }

        $number =input('post.number','1','intval');
        $addressid =input('post.addressid','','intval');
        $shopid =input('post.shopid','0','intval');

        if (empty($addressid)){
            $this->ajaxReturn(ajaxCallBack(300,'请填写收货地址'));
        }
 
        $res = Db::name('shop_items')->alias('si')->field('bg_id,bg_name,bg_image,bg_price,bg_model,si_createId,bg_scoreReward')->join('base_goods bg','si.si_goodsId=bg.bg_id','left')->where(['si_id'=>$shopid,'si_isSale'=>1])->find();

        $address =Db::name('user_delivery_address')->where(['ad_id'=>$addressid])->order('ad_isDefault','desc')->find();

        $area =new Area();
        if (!empty($address)){
            if (!empty($address['ad_provinceId'])){
                $address['province']=$area->getAreaNameById($address['ad_provinceId']);
            }

            if (!empty($address['ad_cityId'])){
                $address['city']=$area->getAreaNameById($address['ad_cityId']);
            }

            if (!empty($address['ad_regionId'])){
                $address['region']=$area->getAreaNameById($address['ad_regionId']);
            }
            $this->assign('address',$address);
        }

        if (!empty($res['si_createId'])){
            $seller = Db::name('user_platform')->alias('up')->field('u_tel,u_nick')->join('user u','up.up_uid=u.u_id','left')->where(['up_id'=>$res['si_createId']])->find();
        }
        $userid =Session::get('user.userId');
        $platformId =Session::get('user.platformId');
        $orderid =getTimeMarkID();
        $order_price =$res['bg_price']*$number;
        $order_score =$res['bg_price']*$number*$res['bg_scoreReward'];
        $timer =time();
        Db::startTrans();
        try{
            $orders =[
                'os_id'=>$orderid,
                'os_order_price'=>$order_price,
                'os_actual_payprice'=>$order_price,
                'os_seller_id'=>$res['si_createId'],
                'os_buyer_id'=>$userid,
                'os_buyer_nick'=>Session::get('user.username'),
                'os_buyer_dttx_uid'=>Session::get('user.dttxId'),
                'os_score'=>$order_score,
                'os_seller_phone'=>$seller['u_tel'],
                'os_create_time'=>$timer,
                'os_platform_id'=>$platformId,
                'os_goods_num'=>$number,
                'os_receiver_name'=>$address['ad_userNick'],
                'os_receiver_phone'=>$address['ad_phone'],
                'os_provinceId'=>$address['ad_provinceId'],
                'os_cityId'=>$address['ad_cityId'],
                'os_regionId'=>$address['ad_regionId'],
                'os_postage'=>$address['ad_postage'],
                'os_address'=>$address['province'].$address['city'].$address['region'].$address['ad_address'],
            ];
            $resresult = Db::name('orders')->insert($orders);
            if(!$resresult){
                Logs::writeMongodb(200020,'orders',$shopid,'提交订单失败ID',$orders,'Ym');
                throw new \Exception('提交订单失败');
            }
           $goods =[
               'og_platform_id'=>$platformId,
               'og_order_id'=>$orderid,
               'og_goods_id'=>$res['bg_id'],
               'og_goods_name'=>$res['bg_name'],
               'og_goods_sku'=>$res['bg_model'],
               'og_shopid'=>$shopid,
               'og_goods_url'=>url('wap/goods/detail',['id'=>$shopid]),
               'og_goods_price'=>$res['bg_price'],
               'og_goods_num'=>$number,
               'og_goods_img'=>$res['bg_image'],
               'og_create_time'=>$timer
           ];

           Db::name('orders_goods')->insert($goods);
           $lastgoodsid =Db::name('orders_goods')->getLastInsID();
           if (!$lastgoodsid){
               Logs::writeMongodb(200020,'orders',$shopid,'插入商品表失败',$orders,'Ym');
               throw new \Exception('插入商品表失败！');
           }
            Logs::writeMongodb(200021,'orders',$orderid,'订单提交成功:',$orders,'Ym');
            Db::commit();
            $url =url('order/payorder',['id'=>$orderid]);
            Session::set('order',null);
            $this->ajaxReturn(ajaxCallBack(200,'订单提交成功!',$url));

//            Cache::set('order' . Session::get('user.userId'), null);
        }catch (\Exception $e){
            Db::rollback();
            $this->ajaxReturn(ajaxCallBack(300,'订单提交失败!'));
        }

    }

    /*
     *订单支付页
     */
    public function payorder(){

        $id=Request::instance()->param('id','');
        if (!is_numeric($id)){
            $this->error('参数错误，请重新提交!',null);
        }

        $res =Db::name('orders')->alias('or')
             ->field('or.*,p.pl_dttx_uid,pl_dttx_nick')
             ->where(['os_id'=>$id,'os_isDelete'=>0,'os_status'=>0])
             ->join('platform p','or.os_platform_id=p.pl_id')->find();
        if (empty($res)){
            $this->error('订单未找到，请重新提交!',null);
        }

        $goods =Db::name('orders_goods')->field('og_goods_name')->where(['og_order_id'=>$id])->select();
        if (!empty($goods)){
            $goodsItem=[];
            foreach ($goods as $item){
                $goodsItem[]=$item['og_goods_name'];
            }
            $goodsName =implode(',',$goodsItem);
            $this->assign('goodsName',$goodsName);
        }
        $this->assign('title','确认支付');
        $this->assign('data',$res);
        return $this->fetch();
    }


    public function confirmOrder(){

        $id =input('post.id','0');
        if (empty($id) || !is_numeric($id)){
            $this->ajaxReturn(ajaxCallBack(300,'参数错误，请重试!'));
        }

        $orders =Db::name('orders')->where(['os_id'=>$id])->lock(true)->find();
        if (empty($orders)){
            $this->ajaxReturn(ajaxCallBack(300,'该订单不存在，请确认后再试!'));
        }

        if ($orders['os_status']==3){
            $this->ajaxReturn(ajaxCallBack(300,'该订单已确认收货，请勿重复处理!'));
        }

        $account_pay_indata =Db::name('account_pay_in')->where(['ap_shop_order_id'=>$id])->lock(true)->find();
        if (empty($account_pay_indata)){
            $this->ajaxReturn(ajaxCallBack(300,'订单状态异常，请检查是否存在支付信息!'));
        }

        $data['orderID']=$id;
        $data['returnType']=1;
        $data['dealType']=2;
        $confirmOrderUrl =Config::get('dttxapi.confirmOrderUrl');
        $res =datang_interface($confirmOrderUrl,$data);
        if (false===$res){
            Logs::writeMongodb(600010,'db_orders',$id,'收货接口超时错误',$res,'Ym');
            $this->ajaxReturn(ajaxCallBack(300,'系统超时，请重试'));
        }
        if ($res['id']==1001 || $res['id']==1009 ){
            Db::startTrans();
            try{
                $res_orders =Db::name('orders')->where(['os_id'=>$id])->update(['os_status'=>3,'os_auto_receiver_time'=>time()]);
                if (!$res_orders){
                    Logs::writeMongodb(600010,'db_orders',$id,'订单状态更新失败','','Ym');
                    throw new Exception('订单状态更新失败，请重试!');
                }
                $res_pay = Db::name('account_pay_in')->where(['ap_shop_order_id'=>$id])->update(['ap_state'=>3]);
                if (!$res_pay){
                    Logs::writeMongodb(600010,'db_account_pay_in',$id,'订单分润表状态更新失败','','Ym');
                    throw  new Exception("订单分润表状态更新失败，请重试!");
                }

                $res_task =Db::name('order_tasklist')->insert([
                    'ot_id' =>$id,
                    'ot_state'=>0,
                    'ot_platform_id'=>$orders['os_platform_id'],
                    'ot_create_time'=>mytime(),
                    'ot_level'=>3
                ]);
                if (!$res_task){
                    Logs::writeMongodb(600010,'db_order_tasklist',$id,'增加分润任务记录失败','','Ym');
                    throw new Exception('增加分润任务记录失败!');
                }
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200,'确认收货成功!'));
            }catch (\Exception $e){
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300,'确认收货失败，请重试！'));
            //    Log::write('订单id'.$id.'确认收货失败'.$e->getMessage(),'error');
            }

        }else{
            $this->ajaxReturn(ajaxCallBack(300,$res['info']));
        }


    }

























    /*
     * 下订单
     * */
//    public function create(){
//        $id     = input('id', 0, 'intval');
//        $id =Request::instance()->param('id','0','intval');
//        $number = input('number', 1, 'intval');
//        $platId = input('platformId', 1, 'intval');//卖家平台id
//        $price  = input('price', 0.00, 'floatval');
//        $model  = input('model', '', 'htmlspecialchars,strip_tags,trim');
//
//        $where = array('bg_isSale' => 1, 'bg_id' => $id, 'bg_isDelete' => 0);
//        $goodsNumber = model('BaseGoods')->getBaseGoodsOne('bg_number as number', $where);
//        $goodsNumber = $goodsNumber['number'];
//
//        $field = 'bg_id as id,bg_image as image,bg_name as name,bg_scoreReward as scoreReward,bg_model as model,bg_format as format';
//        $goods = model('BaseGoods')->getBaseGoodsOne($field, array('bg_isSale' => 1, 'bg_number' => $goodsNumber, 'bg_model'  => $model, 'bg_isDelete' => 0));
//        !$goods && $this->ajaxReturn(ajaxCallBack(300, '商品不存在'));
//
//        $data = array(
//            'number'    => $number,
//            'price'     => $price,
//            'pay'       => $price * $number,
//            'buyerId'   => session('user.userId'),
//            'projectId' => session('user.platformId'),
//            'platId'    => $platId
//        );
//
//        $data = array_merge($data, $goods);
//
//        session(md5('order_' . session('user.userId') . $model .  $goods['id']), $data);
//        $this->ajaxReturn(ajaxCallBack(200, $goods['id']));
 //   }

    /*
     * 订单详情
     * */
//    public function detail(){
//        $id = input('id', '', 'htmlspecialchars,strip_tags,trim');
//        (empty($id) || !is_numeric($id)) && $this->ajaxReturn(ajaxCallBack(300, '非法参数'));
//
//        $userId = session('user.userId');
//
//        $where = array('bg_isSale' => 1, 'bg_id' => $id, 'bg_isDelete' => 0);
//        $model = model('BaseGoods')->getBaseGoodsOne('bg_model as model', $where);
//        $model = $model['model'];
//
//        $order = session('order_' . $userId . $model .  $id);
//
//        !$order && $this->ajaxReturn(ajaxCallBack(300, '订单不存在'));
//        $order['score'] = $order['price'] * 100 * $order['number'] * $order['scoreReward'] / 100;
//
//        $field   = 'ad_id as id,ad_provinceId as provinceId,ad_cityId as cityId,ad_regionId as regionId,ad_address as address';
//        $address = Db::name('user_delivery_address')->where(['ad_isDefault' => 1, 'ad_isDelete' => 0, 'ad_userId' => $userId])->field($field)->find();
//        if($address){
//            $areaModel  = model('common/Area');
//            $province   = $areaModel->getAreaById('a_name', $address['provinceId']);
//            $city       = $areaModel->getAreaById('a_name', $address['cityId']);
//            $region     = $areaModel->getAreaById('a_name', $address['regionId']);
//            $address['area'] = ($province ? $province['a_name'] : '')  . ($city ? $city['a_name'] : '') . ($region ? $region['a_name'] : '');
//        }
//
//        $this->assign('title',   '商品选择');
//        $this->assign('order',   $order);
//        $this->assign('address', $address);
//        return $this->fetch();
//    }

}