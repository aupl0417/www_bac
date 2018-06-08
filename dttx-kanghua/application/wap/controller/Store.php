<?php
namespace app\wap\controller;

use app\common\controller\Common;
use app\common\controller\Share;
use app\work\model\model;
use think\Cache;
use think\Db;
use think\Request;
use think\Session;

class Store extends Share
{
    public function index(){
        $id = Request::instance()->param('id','0','intval');
        $platform =['pl_content'=>'','pl_name'=>'','pl_image'=>'','pl_id','pl_description'=>''];

            if(empty($id)){
                if(session('?user')){
                    $id = session('user.platformId');
                    $platform = Db::name('platform')->field('pl_content,pl_name,pl_id,pl_image,pl_description')->where(['pl_states' => 1, 'pl_isDelete' => 0, 'pl_id' => $id])->find();
                }else{
                    $platform = Db::name('platform')->field('pl_content,pl_name,pl_id,pl_image,pl_description')->where(['pl_states' => 1, 'pl_isDelete' => 0])->order('pl_create_time asc')->limit(1)->find();
                    $id =!empty($platform)?$platform ['pl_id']:0;
            }
            }else{
                $platform = Db::name('platform')->where(['pl_states' => 1, 'pl_isDelete' => 0, 'pl_id' => $id])->field('pl_content,pl_name,pl_id,pl_image,pl_description')->find();
            }


        $goods = array();
        $index_goods_cacheId  =md5('index_goods_cache'.$id);
        if (Cache::has($index_goods_cacheId)){
            $goods =Cache::get($index_goods_cacheId);
        }else{
            $sql ="SELECT si_id,si_createId, bg_name, bg_scoreReward, bg_price, bg_image, bg_model FROM ( SELECT * FROM db_shop_items si RIGHT JOIN db_base_goods bg ON si.si_goodsId = bg.bg_id WHERE `si_isSale` = 1 AND `bg_isSale` = 1 AND `si_projectId` = $id ORDER BY RAND() LIMIT 0, 5 ) goods LEFT JOIN `db_shopkeeper` `sk` ON `goods`.`si_createId` = `sk`.`s_userId` WHERE  `s_isBlocked` = 0  AND `s_state` = 'pass' GROUP BY si_createId ORDER BY `bg_isRecommend` DESC, `si_isRecommend` DESC, `si_updateTime` DESC LIMIT 1";
            $goods = Db::query($sql);
            if (!empty($goods)){
                Cache::set($index_goods_cacheId,$goods,600);
            }
        }
        $isLogin    = session('?user') ? 1 : 0;
        $isShopkeep = 0;
        $code       = input('code', '', 'intval');
        if (Session::has('user')){
            $userid   = Session::get('user.userId');
            $Shopkeep = Db::name('shopkeeper')->where(['s_userId'=>$userid,'s_isDelete'=>0,'s_state'=>'pass'])->count();
            $isShopkeep = $Shopkeep ? 1 : 0;
        }

        if (!empty($code) && Session::get('user.isActive')!=1){
            session('url_fcode', $code);//分享码不为空，则保存，以便在用户登录时，能知道是从哪里分享的
        }

        $this->assign('goodsList', $goods);
        $this->assign('content',   $platform['pl_content']);
        $this->assign('isLogin',   $isLogin);
        $this->assign('code',      $code);
        $this->assign('isShopkeep',$isShopkeep);
        $this->assign('title',     $platform['pl_name']);
        $this->assign('platform',     $platform);
        return $this->fetch();
    }
}
