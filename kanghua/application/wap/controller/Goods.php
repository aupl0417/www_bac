<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/5 0005
 * Time: 15:59
 */

namespace app\wap\controller;

use app\common\controller\Common;
use app\common\controller\Share;
use app\common\controller\Wap;
use app\common\model\ShopItems;
use think\Db;
use think\Request;
use think\Session;

class Goods extends Share {

    public function detail(){
        $id =Request::instance()->param('id','','intval');
        if (empty($id)){
            $this->error('该商品不存在或已下架!','store/index');
        }

        $where = ['si_id' => $id, 'si_isSale' => 1, 'si_isDelete' => 0, 'bg_isSale' => 1, 'bg_isDelete' => 0];
        $res   = Db::name('shop_items')->alias('si')->where($where)->join('base_goods bg','si.si_goodsId=bg.bg_id')->find();
        if (empty($res)){
                $this->error('该商品不存在或已下架!','store/index');
        }

        $shopitems = new ShopItems();
        $models    = $shopitems->getSameGoodsModelByNumberAndCreateID($res['bg_number'], $res['si_createId']);

        $code = input('code', '', 'intval');

        $isLogin = session('user') ? 1 : 0;
        if (!empty($code) && Session::get('user.isActive')!=1){//分享码不为空，则保存，以便在用户登录时，能知道是从哪里分享的
            session('url_fcode', $code);
        }

        $this->assign('models',  $models);
        $this->assign('isLogin', $isLogin);
        $this->assign('res',     $res);
        $this->assign('code',   isset($code) ? $code : '');
        $this->assign('title',   $res['bg_name']);
        return $this->fetch();
    }


    public function about(){
        $this->redirect('store/index');
    }

}