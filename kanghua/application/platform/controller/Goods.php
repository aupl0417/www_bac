<?php
namespace app\platform\controller;

use app\common\controller\Platform;
use think\Exception;
use think\Request;
use think\db;

class Goods extends Platform
{
    public function _initialize(){
        parent::_initialize();
        $this->goodsModel = model('BaseGoods');
    }

    public function index()
    {
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 30, 'intval');

        $number    = input('post.number', '', 'htmlspecialchars,strip_tags,trim');
        $goodsname = input('post.goodsname', '', 'htmlspecialchars,strip_tags,trim');
        $isSale    = input('post.isSale', '', 'htmlspecialchars,strip_tags,trim');
        $where     = array('bg_isDelete' => 0, 'bg_projectId' => session('user.platformId'), 'gs_createId' => session('user.userId'));

        !empty($number)    && $where['bg_number']  = $number;
        !empty($goodsname) && $where['bg_name']    = array('LIKE', '%' . $goodsname .'%');
        $isSale !== ''      && $where['bg_isSale']  = $isSale;

        $join = [
            ['db_category b','a.bg_cateId=b.ca_id', 'LEFT'],
            ['db_goods_stock gs','a.bg_id=gs.gs_goodsId', 'LEFT']
        ];

        $page['totalCount']  = $this->goodsModel->getGoodsCount($where, $join);
        $limit = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];
        $field = 'bg_id as id,bg_name as name,bg_number as number,bg_image as image,bg_scoreReward as scoreReward,bg_price as price,bg_cateId as cateId,
                bg_isRecommend as isRecommend,ca_name as category,bg_isSale as isSale,bg_model as model,bg_format as format,gs_goodsStock as stock';
        $goods = $this->goodsModel->getAllGoodsInfoList($field, $where, $join, 'bg_id desc', $limit);

        $this->assign('goodsList', $goods);
        $this->assign('page',      $page);
        $this->assign('baseId',    $number);
        $this->assign('isSale',    $isSale !== '' ? intval($isSale) : '');
        $this->assign('baseName',  $goodsname);
        return $this->fetch();
    }

    public function create(){
        $input     = input('');
        if(Request::instance()->isPost()){
            $name        = input('post.name', '', 'htmlspecialchars,strip_tags,trim');
            $scoreReward = input('post.scoreReward', 0, 'floatval');
            $cateId      = input('post.cateId', 0, 'intval');
            $isSale      = input('post.isSale', 1, 'intval');
            $image       = input('post.image', '', 'htmlspecialchars,strip_tags,trim');
            $content     = $_POST['content'];
            $attribute   = isset($input['customList']) && !empty($input['customList']) ? $input['customList'] : array();

            !$name      && $this->ajaxReturn(ajaxCallBack(300, '商品名称不能为空'));
            !$cateId    && $this->ajaxReturn(ajaxCallBack(300, '请选择分类'));
            !$image     && $this->ajaxReturn(ajaxCallBack(300, '请上传商品图片'));
            !$content   && $this->ajaxReturn(ajaxCallBack(300, '请填写商品详情'));
            !$attribute && $this->ajaxReturn(ajaxCallBack(300, '请填写商品属性'));

            $modelList = array_column($attribute, 'model');
            $modelList = array_filter($modelList);
            if(count($attribute) != count($modelList)){
                $this->ajaxReturn(ajaxCallBack(300, '商品型号不能为空'));
            }
            if(count($attribute) != count(array_unique($modelList))){
                $this->ajaxReturn(ajaxCallBack(300, '商品型号不能重复'));
            }

            $base  = model('Base')->getBaseOne('ba_id', ['ba_code' => 'parent', 'ba_isDelete' => 0]);
            !$base && $this->ajaxReturn(ajaxCallBack(300, '总仓库不存在，请先创建！'));

            Db::startTrans();
            try{
                $time = time();
                $params = array(
                    'bg_number' => $this->createGoodsNumber(),
                    'bg_name'   => $name,
                    'bg_image'  => $image,
                    'bg_scoreReward' => $scoreReward,
                    'bg_projectId'   => session('user.platformId'),
                    'bg_content'     => $content,
                    'bg_cateId'      => $cateId,
                    'bg_isSale'     => $isSale,
                    'bg_createTime' => $time,
                    'bg_updateTime' => $time,
                    'bg_createId'   => session('user.userId')
                );

                $shopItem = array(
                    'si_number' => $params['bg_number'],
                    'si_createId' => session('user.userId'),
                    'si_createTime' => $time,
                    'si_operateId'  => session('user.userId'),
                    'si_updateTime' => $time,
                    'si_isSale'     => $isSale,
                    'si_projectId'  => session('user.platformId'),
                );

                foreach($attribute as $key => $value){
                    $stock[$key]['gs_baseId']    = $base['ba_id'];//总仓库发货
                    $stock[$key]['gs_projectId'] = session('user.platformId');
                    $stock[$key]['gs_createId']  = session('user.userId');
                    $stock[$key]['gs_operateId'] = session('user.userId');
                    $stock[$key]['gs_createTime'] = $time;
                    $stock[$key]['gs_updateTime'] = $time;

                    foreach($value as $k => $val){
                        if($k == 'stock'){
                            $stock[$key]['gs_goodsStock'] = $val + 0;
                        }else{
                            $k = 'bg_' . $k;
                            $params[$k] = htmlspecialchars(strip_tags(trim($val)));
                        }
                    }

                    $res = Db::table('db_base_goods')->insert($params);
                    if(!$res){
                        throw new Exception('插入到商品表失败');
                    }

                    $goodsId = Db::table('db_base_goods')->getLastInsID();
                    $stock[$key]['gs_goodsId'] = $goodsId;
                    $shopItem['si_goodsId']    = $goodsId;
                    $res = Db::name('shop_items')->insert($shopItem);
                    if(!$res){
                        throw new Exception('插入到经销商商品表失败');
                    }
                }

                $res = Db::table('db_goods_stock')->insertAll($stock);
                if(!$res){
                    throw new Exception('插入商品库存表失败');
                }

                // 提交事务
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200, '添加商品成功', true, 'platform_Goods_index'));
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300, '商品入库失败'));
            }
        }else{
            //分类列表
            $where    = array('ca_isDelete' => 0, 'ca_projectId' => session('user.platformId'));
            $field    = 'ca_id as id,ca_name as name';
            $cateList = model('Category')->getCateList($field, $where);

            $this->assign('cateList', $cateList);
            return $this->fetch();
        }
    }

    protected function createGoodsNumber(){
        return date('YmdHis');
    }

    public function detail(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $where = ['bg_id' => $id];
        $field = '';
        $join  = [
            ['db_goods_stock b','a.bg_id=b.gs_goodsId', 'LEFT'],
            ['db_category c','a.bg_cateId=c.ca_id', 'LEFT']
        ];
        $goods = $this->goodsModel->getAllGoodsInfoOne($field, $where, $join);
        !$goods && $this->ajaxReturn(array('statusCode' => 300, 'message' => '商品不存在'));
        $this->assign('goods', $goods);
        return $this->fetch();
    }

    public function edit(){

        if(Request::instance()->isPost()){
            $input       = input('');
            $validate = array(
                'id'   => '商品id不能为空',
                'name' => '商品名称不能为空',
                'cateId' => '请选择分类',
                'image' => '请上传商品图片',
                'model' => '请填写型号',
                'cost' => '请填写成本价格',
                'price' => '请填写建议零售价',
                'goodsStock' => '请填写库存量',
                'instruction' => '请填写说明',
            );

            foreach($validate as $key => $val){
                !trim($input[$key]) && $this->ajaxReturn(ajaxCallBack(300, $val));
            }



            Db::startTrans();
            try{
                $time = time();
                $params = array(
                    'bg_name'   => input('post.name', '', 'htmlspecialchars,strip_tags,trim'),
                    'bg_image'  => input('post.image', '', 'trim'),
                    'bg_scoreReward' => input('post.scoreReward', 0, 'floatval'),
                    'bg_projectId'   => session('user.platformId'),
                    'bg_content'     => $_POST['content'],
                    'bg_instruction' => input('post.instruction', '', 'htmlspecialchars,strip_tags'),
                    'bg_cateId'      => input('post.cateId', 0, 'intval'),
                    'bg_isSale'      => input('post.isSale', 0, 'intval'),
                    'bg_updateTime'  => $time,
                    'bg_model'       => input('post.model', '', 'htmlspecialchars,strip_tags,trim'),
//                    'bg_format'      => input('post.format', 0, 'intval'),
//                    'bg_unit'        => input('post.unit', '', 'htmlspecialchars,strip_tags,trim'),
                    'bg_cost'        => input('post.cost', 0, 'floatval'),
                    'bg_price'       => input('post.price', 0, 'floatval'),
//                    'bg_packFormat'  => input('post.packFormat', '', 'htmlspecialchars,strip_tags,trim'),
                );

                $id = input('post.id', '', 'htmlspecialchars,strip_tags,trim');

                $res = Db::name('base_goods')->where(['bg_id' => $id])->update($params);
                if(!$res){
                    throw new Exception('编辑商品失败');
                }

                $stock = array(
                    'gs_goodsStock' => input('post.goodsStock', 0, 'intval'),
                    'gs_operateId'  => session('user.userId'),
                    'gs_updateTime' => $time
                );

                $res = Db::name('goods_stock')->where(['gs_goodsId' => $id])->update($stock);
                if(!$res){
                    throw new Exception('更新商品库存量失败');
                }

                // 提交事务
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200, '编辑商品成功', true, 'platform_Goods_index'));
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300, '编辑商品失败'.$e->getMessage()));
            }
        }else{
            $id = input('id', 0, 'intval');
            !$id && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));

            $where = ['bg_id' => $id];
            $field = '';
            $join  = [
                ['db_goods_stock b','a.bg_id=b.gs_goodsId', 'LEFT'],
                ['db_category c','a.bg_cateId=c.ca_id', 'LEFT']
            ];
            $goods = $this->goodsModel->getAllGoodsInfoOne($field, $where, $join);
            !$goods && $this->ajaxReturn(array('statusCode' => 300, 'message' => '商品不存在'));

            //分类列表
            $where    = array('ca_isDelete' => 0, 'ca_projectId' => session('user.platformId'));
            $field    = 'ca_id as id,ca_name as name';
            $cateList = model('Category')->getCateList($field, $where);

            $this->assign('cateList', $cateList);
            $this->assign('goods', $goods);
            return $this->fetch();
        }
    }

    public function remove(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $res   = $this->goodsModel->deleteBaseGoods($id);
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => '删除失败'));

        $this->ajaxReturn(array('statusCode' => 200, 'message' => '删除成功'));
    }

    public function recommend(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $res   = $this->goodsModel->recomendGoods($id);
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => '推荐失败'));

        $this->ajaxReturn(array('statusCode' => 200, 'message' => '推荐成功'));
    }

    public function offShelf(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $res   = $this->goodsModel->offShelf($id);
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => '下架失败'));

        $this->ajaxReturn(array('statusCode' => 200, 'message' => '下架成功'));
    }

    public function onShelf(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $res   = $this->goodsModel->onShelf($id);
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => '上架失败'));

        $this->ajaxReturn(array('statusCode' => 200, 'message' => '上架成功'));
    }

    /*
     * 商品批量上架
     * */
    public function batchOnShelf(){
        $ids = input('ids', '', 'htmlspecialchars,strip_tags,trim');

        $res = db('base_goods')->where(array('bg_id' => array('IN', $ids)))->update(['bg_isSale' => 1]);
        $res === false && $this->ajaxReturn(ajaxCallBack(300, '商品上架失败'));
        $this->ajaxReturn(ajaxCallBack(200, '商品上架成功'));
    }

    /*
     * 商品批量下架
     * */
    public function batchOffShelf(){
        $ids = input('ids', '', 'htmlspecialchars,strip_tags,trim');

        $res = db('base_goods')->where(array('bg_id' => array('IN', $ids)))->update(['bg_isSale' => 0]);
        $res === false && $this->ajaxReturn(ajaxCallBack(300, '商品下架失败'));
        $this->ajaxReturn(ajaxCallBack(200, '商品下架成功'));
    }

    public function addattribute(){
        $id = input('id', 0, 'intval');
        !$id && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));

        $params = array(
            'bg_model' => '请输入型号',
            'bg_cost'  => '成本价格不能为空',
            'bg_price' => '建议零售价不能为空',
            'bg_instruction' => '请输入说明'
        );

        $data = array(
            'bg_model' => input('model', '', 'htmlspecialchars,strip_tags,trim'),
            'bg_cost' => input('cost', '', 'floatval'),
            'bg_price' => input('price', '', 'floatval'),
            'bg_instruction' => input('instruction', '', 'htmlspecialchars,strip_tags,trim'),
        );

        foreach($data as $key => $val){
            empty($val) && $this->ajaxReturn(array('statusCode' => 300, 'message' => $params[$key]));
        }

        $stock = Db::name('goods_stock')->where(['gs_id' => $id])->field('gs_goodsId')->find();
        !$stock && $this->ajaxReturn(array('statusCode' => 300, 'message' => '库存不存在'));

        Db::startTrans();
        try{
            $res = Db::name('base_goods')->where(['bg_id' => $stock['gs_goodsId']])->update($data);
            if($res === false){
                throw new Exception('编辑失败');
            }

            $goodsStock = [
                'gs_goodsStock' => input('goodsStock', '', 'intval'),
                'gs_operateId'  => session('user.userId'),
                'gs_updateTime' => time()
            ];

            $res = Db::name('goods_stock')->where(['gs_id' => $id])->update($goodsStock);
            if($res === false){
                throw new Exception('更新库存失败');
            }
            Db::commit();
            $this->ajaxReturn(array('statusCode' => 200, 'message' => '编辑成功'));
        }catch (\Exception $e){
            Db::rollback();
            $this->ajaxReturn(array('statusCode' => 300, 'message' => '编辑失败'));
        }
    }
}
