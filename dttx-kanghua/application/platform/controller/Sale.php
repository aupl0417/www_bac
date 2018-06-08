<?php
namespace app\platform\controller;

use app\common\controller\Platform;
use app\common\model\BaseGoods;
use app\common\model\ShopItems;
use think\Exception;
use think\Request;
use think\db;

class Sale extends Platform{

    public function _initialize(){
        parent::_initialize();
        $this->model = model('ShopItems');
        $this->model =new ShopItems();
        $this->goodsModel = new BaseGoods();
    }

    public function index(){

    }

    public function edit()
    {
        // TODO: Implement edit() method.
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }

    public function goodslist(){
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 30, 'intval');

        $number    = input('post.number', '', 'htmlspecialchars,strip_tags,trim');
        $goodsname = input('post.goodsname', '', 'htmlspecialchars,strip_tags,trim');
        $isSale    = input('post.isSale', '', 'htmlspecialchars,strip_tags,trim');
        $where     = array('si_createId' => session('user.userId'), 'si_isDelete' => 0, 'si_projectId' => session('user.platformId'));

        !empty($number)    && $where['bg_number']  = $number;
        !empty($goodsname) && $where['bg_name']    = array('LIKE', '%' . $goodsname .'%');
        $isSale !== ''      && $where['si_isSale']  = $isSale;

        $join = [
            ['db_base_goods b','a.si_goodsId=b.bg_id', 'LEFT'],
            ['db_category c','b.bg_cateId=c.ca_id', 'LEFT'],
            ['db_goods_stock gs','a.si_goodsId=gs.gs_goodsId', 'LEFT']
        ];

        $page['totalCount']  = $this->model->getShopItemsCount($where, $join);
        $limit = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];
        $field = 'si_id as id,bg_name as name,bg_number as number,bg_image as image,bg_scoreReward as scoreReward,bg_price as price,bg_cateId as cateId,bg_isRecommend as isRecommend,
                ca_name as category,si_isSale as isSale,bg_model as model,bg_format as format,gs_goodsStock as stock,bg_isSale';
        $goods = $this->model->getShopItemsAll($field, $where, $join, 'si_createTime desc', $limit);

        $this->assign('goodsList', $goods);
        $this->assign('page',      $page);
        $this->assign('number',    $number);
        $this->assign('isSale',    $isSale !== '' ? intval($isSale) : '');
        $this->assign('goodsname',  $goodsname);
        return $this->fetch();
    }

    /*
     * 选取商品
     * */
    public function choose(){
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 10, 'intval');

        $number    = input('post.number', '', 'htmlspecialchars,strip_tags,trim');
        $goodsname = input('post.goodsname', '', 'htmlspecialchars,strip_tags,trim');
        $beginTime = input('post.beginTime', '', 'htmlspecialchars,strip_tags,trim');
        $endTime   = input('post.endTime', '', 'htmlspecialchars,strip_tags,trim');
        $where     = array('bg_isDelete' => 0, 'bg_isSale' => 1);

        !empty($number)    && $where['bg_number']  = array('LIKE', '%' . $number .'%');
        !empty($goodsname) && $where['bg_name']    = array('LIKE', '%' . $goodsname .'%');
        !empty($beginTime) && $where['bg_createTime'] = array('>=', '%' . $beginTime .'%');
        !empty($endTime)   && $where['bg_createTime'] = array('<=', '%' . $endTime .'%');
        $platformId =session('user.platformId');
        $condition = ['si_createId' => session('user.userId'), 'si_projectId' => $platformId, 'si_isDelete' => 0];
        $selectedIds = model('ShopItems')->getShopItemsAll('si_goodsId', $condition);
        if($selectedIds){
            $selectedIds = array_column($selectedIds, 'si_goodsId');
            $where['bg_id'] = array('not in', implode(',', $selectedIds));

        }
        $where['bg_projectId'] = $platformId;
        $join = [
            ['db_category b','a.bg_cateId=b.ca_id', 'LEFT'],
            ['db_goods_stock gs','a.bg_id=gs.gs_goodsId', 'LEFT']
//            ['db_base c','a.bg_baseId=c.ba_id'],
        ];

        $goodsModel = model('BaseGoods');
        $page['totalCount']  = $goodsModel->getGoodsCount($where, $join);
        $limit = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];
        $field = 'bg_id as id,bg_name as name,bg_number as number,bg_image as image,bg_format as format,bg_cost as cost,bg_price as price,ca_name as category,bg_description as description,bg_model as model';
        $goods = $goodsModel->getAllGoodsInfoList($field, $where, $join, 'bg_createTime desc', $limit);
        if($goods){
            foreach($goods as &$val){
                $val['profit'] = $val['price']  - $val['cost'];
            }
        }

        $this->assign('goodsList', $goods);
        $this->assign('page',      $page);
        $this->assign('number',    $number);
        $this->assign('goodsname', $goodsname);
        $this->assign('beginTime', $beginTime);
        $this->assign('endTime',   $endTime);
        return $this->fetch();
    }

    public function selectItem(){
        $ids = Request::instance()->param('ids', '', 'htmlspecialchars,strip_tags,trim');
        empty($ids) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));

        $ids    = strpos($ids, ',') !== false ? explode(',', $ids) : array($ids);
        $userId = session('user.userId');
        foreach($ids as $key => $val){
            $data[$key] = array(
                'si_goodsId' => $val,
                'si_createId' => $userId,
                'si_model'      => '',
                'si_channel'    => '',
                'si_stock'      => 0,
                'si_createTime' => time(),
                'si_operateId'  => $userId,
                'si_updateTime' => time(),
                'si_projectId'  => session('user.platformId'),
            );
        }

        $res = Db::name('shop_items')->insertAll($data);
        !$res && $this->ajaxReturn(array('statusCode' => 300, 'message' => '选取商品失败'));
        $this->ajaxReturn(array('statusCode' => 200, 'message' => '选取商品成功'));
    }

    /*public function stock(){
        $id = input('id', 0, 'intval');
        !$id && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));

        $where = ['si_id' => $id, 'si_projectId' => session('user.platformId')];
        $field = 'si_id as id,bg_model as model,bg_format as format,gs_goodsStock as stock';
        $join  = [['db_base_goods bg','a.si_goodsId=bg.bg_id', 'LEFT'], ['db_goods_stock b','a.si_goodsId=b.gs_goodsId', 'LEFT']];
        $goods = $this->model->getShopItemsOne($field, $where, $join);
        !$goods && $this->ajaxReturn(array('statusCode' => 300, 'message' => '商品不存在'));;

        $this->assign('goods', $goods);
        return $this->fetch();
    }*/

    public function detail(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $where = ['si_id' => $id];
//        $field = 'bg_id as id,bg_model as model,bg_format as format,gs_goodsStock as stock';
        $field = '';
        $join  = [
            ['db_base_goods bg','a.si_goodsId=bg.bg_id', 'LEFT'],
            ['db_goods_stock b','a.si_goodsId=b.gs_goodsId', 'LEFT'],
            ['db_category c','bg.bg_cateId=c.ca_id', 'LEFT']
        ];
        $goods = $this->model->getShopItemsOne($field, $where, $join);
        !$goods && $this->ajaxReturn(array('statusCode' => 300, 'message' => '商品不存在'));
        $this->assign('goods', $goods);
        return $this->fetch();
    }

    public function offShelf(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $res   = $this->model->offShelf($id);
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => '下架失败'));

        $this->ajaxReturn(array('statusCode' => 200, 'message' => '下架成功'));
    }

    public function onShelf(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $where = ['si_id'  => $id, 'si_isSale' => 0, 'si_isDelete' => 0, 'bg_isSale' => 1, 'bg_isDelete' => 0];
        $join  = [['db_base_goods bg', 'bg.bg_id=a.si_goodsId', 'LEFT']];
        $goods = $this->model->getShopItemsCount($where, $join);
        !$goods && $this->ajaxReturn(array('statusCode' => 300, 'message' => '该商品在总部已下架'));

        $res   = $this->model->onShelf($id);
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => '上架失败'));

        $this->ajaxReturn(ajaxCallBack(200, '上架成功', '', 'platform_sale_goodslist'));
    }

    /*
     * 商品批量上架
     * */
    public function batchOnShelf(){
        $ids = input('get.expids');

        $res = db('base_goods')->where(array('bg_id' => array('IN', $ids)))->update(['bg_isSale' => 1]);
        $res === false && $this->ajaxReturn(ajaxCallBack(300, '商品上架失败'));
        $this->ajaxReturn(ajaxCallBack(200, '商品上架成功', true, 'platform_sale_goodslist'));
    }

    /*
     * 商品批量下架
     * */
    public function batchOffShelf(){
        $ids = input('get.expids');

        $res = db('base_goods')->where(array('bg_id' => array('IN', $ids)))->update(['bg_isSale' => 2]);
        $res === false && $this->ajaxReturn(ajaxCallBack(300, '商品下架失败'));
        $this->ajaxReturn(ajaxCallBack(200, '商品下架成功'));
    }

    /*
     * 分享
     * */
    public function share(){
        $id = input('id', 0, 'intval');
        $code = session('user.code');
        $this->assign('title', '分享');
        $this->assign('id', $id);
        $this->assign('code', $code);
        return $this->fetch();
    }

    public function qcode(){
        $id   = input('id', 0, 'intval');
        $code = input('code', '', 'intval');

        $url = url('wap/goods/detail', ['id' => $id, 'code' => $code], 'html', true);

        return \QRcode::png($url, false, 'H', 6);
    }

}
