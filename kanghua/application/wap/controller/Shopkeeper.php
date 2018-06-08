<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/4 0004
 * Time: 15:45
 */
namespace app\wap\controller;

use app\common\controller\Wap;
use app\common\tools\Logs;
use think\Exception;
use think\Request;
use think\Db;
use app\platform\model\Area;
use think\Session;

class Shopkeeper extends Wap{

    public function index()
    {
        // TODO: Implement index() method.
    }

    //经销商申请
    public function create(){
        if(Request::instance()->isPost()){
            if (!Session::has('user') || Session::get('user.userId')==''){
                $this->ajaxReturn(ajaxCallBack(301, array('msg' => '您未登录', 'url' => url('login/index'))));
            }
            $user = session('user');
            $user['state']='none';
            $user['createId']=0;
            $user['operateId']=0;
            $res = model('ShopKeeper')->createShopKeeper($user);
            $res && $res['code'] == 300 && $this->ajaxReturn(ajaxCallBack(300, $res['message']));
            $this->ajaxReturn(ajaxCallBack(200, '提交成功'));
        }else{
            if (!Session::has('user') || Session::get('user.userId')==''){
                $this->redirect('login/index');
            }

            $userid =Session::get('user.userId');
            $shoper = Db::name('shopkeeper')->field('s_state')->where(['s_userId'=>$userid])->find();
            if (!empty($shoper)){
                if ($shoper['s_state']=='none'){
                    $this->error('您是申请已提交，请等待审核!');
                }elseif ($shoper['s_state']=='pass'){
                    $this->error('您已经是经销商了，请勿重复申请!');
                }
            }

            $areaModel = new Area();
            $area =$areaModel->findListByParentId(0);
            $this->assign('area',$area);
            $this->assign('dttxNick', session('user.username'));
            $this->assign('title', '经销商申请');
            return $this->fetch();
        }
    }

    /**
     * 线下录单
     * @return mixed
     */
    public function sale(){

        if(Request::instance()->isPost()){

            if (!Session::has('user') || Session::get('user.userId')==''){
                $this->ajaxReturn(ajaxCallBack(301, array('msg' => '您未登录', 'url' => url('login/index'))));
            }

            $input['goodsNum'] = input('goodsNum', '', 'htmlspecialchars,strip_tags,trim');
            $input['number']   = input('number', 1, 'intval');
            $input['dttxnick'] = input('dttxnick', '', 'htmlspecialchars,strip_tags,trim');
            $input['delivery'] = input('delivery', 0, 'intval');
            $input['receiver'] = input('receiver', '', 'htmlspecialchars,strip_tags,trim');
            $input['phone']    = input('phone', '', 'htmlspecialchars,strip_tags,trim');
            $input['provinceId']   = input('provinceId', 0, 'intval');
            $input['cityId']   = input('cityId', 0, 'intval');
            $input['regionId'] = input('regionId', 0, 'intval');
            $input['postage']  = input('postage', '', 'htmlspecialchars,strip_tags,trim');
            $input['address']  = input('address', '', 'htmlspecialchars,strip_tags,trim');

            $result =$this->validate($input,'SaleValidate');
            if (true!==$result){
                $this->ajaxReturn(ajaxCallBack(300,$result));
            }

            //查询用户数据
            $buyers =Db::name('user_platform')->alias('up')->field('up.*')->join('user u','up.up_uid=u.u_id','left')->where(['u_nick'=>$input['dttxnick']])->whereOr(['u_tel'=>$input['dttxnick']])->find();

            if (empty($buyers)){
                $this->ajaxReturn(ajaxCallBack(300,'没有找到该消费者的账号信息，请确认是否激活本项目!'));
            }


            $goods =Db::name('shop_items')->alias('si')->field('si_projectId,si_goodsId,si_id,bg_name,bg_image,bg_model,bg_price,bg_scoreReward')->join('base_goods bg','si.si_goodsId=bg.bg_id','left')->where(['si_id'=>$input['goodsNum']])->find();
            if (empty($goods)){
                $this->ajaxReturn(ajaxCallBack(300,'没有找到该商品!'));
            }
            Db::startTrans();
            try{
//                //保存收货地址数据
//                $addressData = array(
//                    'ad_userId'     => $buyers['up_id'],
//                    'ad_userNick'   => $buyers['up_unick'],
//                    'ad_phone'      => $input['phone'],
//                    'ad_provinceId' => $input['provinceId'],
//                    'ad_cityId'     => $input['cityId'],
//                    'ad_regionId'   => $input['regionId'],
//                    'ad_postage'    => $input['postage'],
//                    'ad_address'    => $input['address']
//                );
//
//                $deliveryModel = Db::name('user_delivery_address');
//                $res = $deliveryModel->insert($addressData);
//                if(!$res){
//                    throw new Exception('添加收货地址失败');
//                }

                //保存订单数据
                $time = time();
                $orderid =getTimeMarkID();
                $seller_id =Session::get('user.userId');
                $data = array(

                    'os_id'      => $orderid,
                    'os_order_price' => $goods['bg_price'] * $input['number'],
                    'os_actual_payprice' => $goods['bg_price'] * $input['number'],
                    'os_seller_id'       => $seller_id,
                    'os_buyer_id'       => $buyers['up_id'],
                    'os_buyer_nick'       => $buyers['up_unick'],
                    'os_buyer_dttx_uid'       => $buyers['up_dttx_uid'],
                    'os_score'       => $goods['bg_price'] * $input['number'] * $goods['bg_scoreReward'],
                    'os_seller_phone'=> Session::get('user.tel'),
                    'os_create_time'=> $time,
                    'os_platform_id' => session('user.platformId'),
                    'os_buyer_note'=>'线下订单',
                    'os_goods_num'   => $input['number'],
                    'os_receiver_name'   => $input['receiver'],
                    'os_receiver_phone'   => $input['phone'],
                    'os_provinceId'   => $input['provinceId'],
                    'os_cityId'   => $input['cityId'],
                    'os_order_type'=>1,
                    'os_regionId'   => $input['regionId'],
                    'os_postage'   => $input['postage'],
                    'os_address'   => $input['address'],

                );

                $res = Db::name('orders')->insert($data);
                if(!$res){
                    Logs::writeMongodb(200002,'db_orders_goods',$orderid,'线下录单提交失败',$data,'Ym');
                    throw new Exception('提交申请失败');
                }

                $orderGoods = array(
                    'og_platform_id' => session('user.platformId'),
                    'og_order_id'    => $orderid,
                    'og_goods_id'    => $goods['si_goodsId'],
                    'og_shopid'      => $goods['si_id'],
                    'og_goods_name'  => $goods['bg_name'],
                    'og_goods_sku'   => $goods['bg_model'],
                    'og_goods_price' => $goods['bg_price'],
                    'og_goods_num'   => $input['number'],
                    'og_goods_img'   => $goods['bg_image'],
                    'og_create_time' => $time
                );

                $res = Db::name('orders_goods')->insert($orderGoods);
                if(!$res){
                    Logs::writeMongodb(200002,'db_orders_goods',$orderid,'插入订单商品表失败',$orderGoods,'Ym');
                    throw new Exception('创建订单商品表失败');
                }

                // 提交事务
                Db::commit();
                Logs::writeMongodb(200002,'db_orders_goods',$orderid,'销售录入成功',$data,'Ym');
                $this->ajaxReturn(ajaxCallBack(301, ['msg'=>'销售录入成功','url'=>url('order/payorder',['id'=>$orderid])]));

            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300, '销售录入失败，请重试!'));
            }
        }else{
            if (!Session::has('user') || Session::get('user.userId')==''){
                $this->redirect('login/index');
            }

            $userId =Session::get('user.userId');
            $shopkeeper =Db::name('shopkeeper')->where(['s_userId'=>$userId,'s_state'=>'pass'])->find();
            if (empty($shopkeeper)){
               $this->error('您不是经销商，没有权限访问该页面！',url('store/index'));
            }

            $areaModel = new Area();
            $area =$areaModel->findListByParentId(0);

            $goodsList =Db::name('shop_items')->alias('si')->field('si_id,bg_name,bg_model')->join('base_goods bg','si.si_goodsId=bg.bg_id','left')->where(['si_createId'=>$userId,'si_isSale'=>1,'si_isDelete'=>0,'bg_isSale'=>1])->select();


            $this->assign('area', $area);
            $this->assign('goodsList',$goodsList);
            $this->assign('title', '销售录入');
            return $this->fetch();
        }

    }

}