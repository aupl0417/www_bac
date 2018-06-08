<?php
namespace app\platform\controller;

use app\common\controller\Platform;
use think\Exception;
use think\Request;
use think\db;

class Stock extends Platform
{
    public function _initialize(){
        parent::_initialize();
        $this->goodsModel      = model('GoodsStock');
        $this->goodsStockModel = Db::name('goods_stock');
    }

    public function index(){
        $page['pageCurrent'] = input('post.page', 1, 'intval');
        $page['pageSize']    = input('post.pageSize', 30, 'intval');

        $basename   = input('post.basename', '', 'htmlspecialchars,strip_tags,trim');
        $goodsname  = input('post.goodsname', '', 'htmlspecialchars,strip_tags,trim');
        $stockCount = input('post.stockcount', '', 'intval');
        $op         = input('post.op', 'lt', 'htmlspecialchars,strip_tags,trim');
        $where      = array('gs_isDelete' => 0, 'gs_projectId' => session('user.platformId'));
        $op         = strtolower($op) == 'lt' ? '<' : '>';

        !empty($basename)   && $where['ba_name']   = array('LIKE', '%' . $basename .'%');
        !empty($goodsname)  && $where['bg_name']   = array('LIKE', '%' . $goodsname .'%');
        !empty($stockCount) && $where['gs_goodsStock']  = array($op, $stockCount);

        $join = [
            ['db_base_goods b','a.gs_goodsId=b.bg_id', 'LEFT'],
            ['db_base c','a.gs_baseId=c.ba_id', 'LEFT'],
        ];
        $page['totalCount']  = $this->goodsModel->getGoodsStockCount($where, $join);
        $limit = ($page['pageCurrent'] - 1) * $page['pageCurrent'] . ',' . $page['pageSize'];

        $field = 'gs_id as id,bg_name as name,bg_number as number,ba_name as baseName,gs_goodsStock as stock,bg_model as model';
        $goods = $this->goodsModel->getGoodsStockAll($field, $where, $join, 'bg_id desc', $limit);

        $this->assign('goodsList', $goods);
        $this->assign('page',      $page);
        $this->assign('baseName',  $basename);
        $this->assign('goodsname', $goodsname);
        $this->assign('stockCount',$stockCount);
        $this->assign('op',        $op);
        return $this->fetch();
    }

    /*
     * 商品入库
     * */
    public function create(){
        $input     = input('');
        if(Request::instance()->isPost()){
            $goodsNumber = input('post.goodsNumber', '', 'htmlspecialchars,strip_tags,trim');
            $goodsName   = input('post.goodsName',  '', 'htmlspecialchars,strip_tags,trim');
            $modelId     = input('post.modelId', '', 'htmlspecialchars,strip_tags,trim');
            $baseId      = input('post.baseId', 0, 'intval');
            $goodsCount  = input('post.goodsCount', 0, 'intval');

            !$goodsNumber  && $this->ajaxReturn(ajaxCallBack(300, '商品编号不能为空'));
            !$goodsName    && $this->ajaxReturn(ajaxCallBack(300, '商品名称不能为空'));
            !$goodsCount   && $this->ajaxReturn(ajaxCallBack(300, '请填写商品数量'));
            !$modelId      && $this->ajaxReturn(ajaxCallBack(300, '请填选择商品型号'));

            $baseGoods = model('BaseGoods')->getBaseGoodsOne('bg_id,bg_name', ['bg_number' => $goodsNumber]);
            if($baseGoods['bg_name'] !== $goodsName){
                throw new Exception('输入的商品名称不正确');
            }

            Db::startTrans();
            try{
                $time      = time();
                $userId    = session('user.userId');
                $projectId = session('user.platformId');
                $params = array(
                    'od_goodsId'    => $baseGoods['bg_id'],//$goodsNumber,
                    'od_model'      => $modelId,
                    'od_purposeId'  => '',
                    'od_type'       => 'storage',
                    'od_operateId'  => $userId,
                    'od_number'     => $goodsCount,
                    'od_createTime' => $time,
                    'od_orderId'    => getTimeMarkID(),
                    'od_projectId'  => $projectId
                );

                $id = Db::table('db_goods_operate_detail')->insert($params);
                if(!$id){
                    throw new Exception('插入商品明细表失败');
                }


                //保存或插入记录到库存表(通过仓库id、商品id和projectId来判断是否存在该商品库存记录)
                $condition  = array('gs_baseId' => $baseId, 'gs_goodsId' => $baseGoods['bg_id'], 'gs_projectId' => session('user.platformId'), 'gs_isDelete' => 0);
                $goodsStock = $this->goodsStockModel->where($condition)->field('gs_id,gs_goodsStock')->find();
                if(!$goodsStock){
                    $stock = array(
                        'gs_baseId'     => $baseId,
                        'gs_goodsId'    => $baseGoods['bg_id'],
                        'gs_goodsStock' => $goodsCount,
                        'gs_createId'   => $userId,
                        'gs_createTime' => $time,
                        'gs_updateTime' => $time,
                        'gs_operateId'  => $userId,
                        'gs_projectId'  => $projectId
                    );

                    $res = $this->goodsStockModel->insert($stock);
                    if(!$res){
                        throw new Exception('插入记录到库存表失败');
                    }
                }else {
                    $stock = array(
                        'gs_goodsStock' => $goodsStock['gs_goodsStock'] + $goodsCount,
                        'gs_updateTime' => $time,
                        'gs_operateId'  => $userId,
                    );
                    $res = $this->goodsStockModel->where($condition)->update($stock);
                    if(!$res){
                        throw new Exception('保存记录到库存表失败');
                    }
                }

                // 提交事务
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200, '商品入库成功', true, 'stock_index'));
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->ajaxReturn(ajaxCallBack(300, '商品入库失败'));
            }
        }else{
            $where    = array('ba_isDelete' => 0, 'ba_projectId' => session('user.platformId'));
            $field    = 'ba_id as id,ba_name as name';
            $baseList = model('Base')->getBaseList($field, $where);

            $this->assign('baseList', $baseList);
            return $this->fetch();
        }
    }

    /*
     * 商品调拨
     * */
    public function allocation(){

        $id = Request::instance()->param('id', 0, 'intval');
        empty($id) && $this->ajaxReturn(array('state' => 'error', 'message' => '非法参数'));

        if(Request::instance()->isPost()){
//            dump(input(''));die;
            $goodsNumber = input('post.goodsNumber', '', 'htmlspecialchars,strip_tags,trim');
            $goodsName   = input('post.goodsName',  '', 'htmlspecialchars,strip_tags,trim');
            $modelId     = input('post.modelId', '', 'htmlspecialchars,strip_tags,trim');
            $outBaseId   = input('post.outBaseId', 0, 'intval');
            $inBaseId    = input('post.inBaseId', 0, 'intval');
            $goodsCount  = input('post.goodsCount', 0, 'intval');

            !$goodsNumber  && $this->ajaxReturn(ajaxCallBack(300, '商品编号不能为空'));
            !$goodsName    && $this->ajaxReturn(ajaxCallBack(300, '商品名称不能为空'));
            !$goodsCount   && $this->ajaxReturn(ajaxCallBack(300, '请填写商品数量'));
            !$modelId      && $this->ajaxReturn(ajaxCallBack(300, '请填选择商品型号'));

            Db::startTrans();
            try{
                $time      = time();
                $userId    = session('user.userId');
                $projectId = session('user.platformId');
                $field     = 'bg_id,bg_name,bg_model';
                $where     = array('bg_number' => $goodsNumber, 'bg_name' => $goodsName, 'bg_model' => $modelId, 'bg_isDelete' => 0, 'bg_createId' =>  $userId);
                $baseGoods = model('BaseGoods')->getBaseGoodsOne($field, $where);
                if(!$baseGoods){
                    throw new Exception('暂无合适的记录，请重新操作');
                }

                $orderId = getTimeMarkID();
                $paramOut = array(//调拨
                    'od_goodsId'    => $baseGoods['bg_id'],//$goodsNumber,
                    'od_model'      => $modelId,
                    'od_stockId'    => $id,
                    'od_purposeId'  => $inBaseId,
                    'od_type'       => 'allocation',
                    'od_operateId'  => $userId,
                    'od_number'     => $goodsCount,
                    'od_createTime' => $time,
                    'od_orderId'    => $orderId,
                    'od_projectId'  => $projectId
                );

                $id = Db::table('db_goods_operate_detail')->insert($paramOut);
                if(!$id){
                    throw new Exception('插入商品明细表失败', 1);
                }



                //出库数量减少
                $condition = ['gs_baseId' => $outBaseId, 'gs_goodsId' => $baseGoods['bg_id'], 'gs_projectId' => $projectId, 'gs_isDelete' => 0];
                $stock     = $this->goodsStockModel->where($condition)->field('gs_goodsStock')->find();

                if(!$stock){
                    throw new Exception('出货仓库错误或暂无该库存记录');
                }

                if($stock['gs_goodsStock'] < $goodsCount){
                    throw new Exception('库存不足，请重新操作', 2);
                }

                $res = $this->goodsStockModel->where($condition)->setDec('gs_goodsStock', $goodsCount);
                if(!$res){
                    throw new Exception('更新仓库商品表库存失败', 3);
                }

                //该种商品入库数量增加
                $condition['gs_baseId']  = $inBaseId;
                $count = $this->goodsStockModel->where($condition)->count();
                if($count){
                    $res = $this->goodsStockModel->where($condition)->setInc('gs_goodsStock', $goodsCount);
                }else {
                    $data = array(
                        'gs_baseId' => $inBaseId,
                        'gs_goodsId' => $baseGoods['bg_id'],
                        'gs_shopId'  => '',//以后通过绑定的用户id来获取
                        'gs_goodsStock' => $goodsCount,
                        'gs_projectId'  => $projectId,
                        'gs_createId'   => $userId,
                        'gs_operateId'  => $userId,
                        'gs_createTime' => $time,
                        'gs_updateTime' => $time,
                    );

                    $res = $this->goodsStockModel->insert($data);
                    if(!$res){
                        throw new Exception('生成调拨记录失败', 4);
                    }

                    $paramIn = array(//入库
                        'od_goodsId'    => $baseGoods['bg_id'],//$goodsNumber,
                        'od_model'      => $modelId,
                        'od_stockId'    => $this->goodsStockModel->getLastInsID(),
                        'od_purposeId'  => '',
                        'od_type'       => 'storage',
                        'od_operateId'  => $userId,
                        'od_number'     => $goodsCount,
                        'od_createTime' => $time,
                        'od_orderId'    => $orderId,
                        'od_projectId'  => $projectId
                    );

                    $id = Db::table('db_goods_operate_detail')->insert($paramIn);
                    if(!$id){
                        throw new Exception('插入商品明细表失败', 1);
                    }
                }

                // 提交事务
                Db::commit();
                $this->ajaxReturn(ajaxCallBack(200, '商品调拨成功', true, 'platform_stock_index'));
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->ajaxReturn(array('statusCode' => 300, 'message' => '商品调拨失败' . $e->getMessage() . '--' . $e->getCode()));
            }

        }else{
            $where      = array('gs_isDelete' => 0, 'gs_projectId' => session('user.platformId'), 'gs_id' => $id);
            $field      = 'bg_id as id,bg_number as number,bg_name as name,bg_model as model,ba_id as baseId,ba_name as baseName,gs_goodsStock as stock';
            $join       = [
                ['db_base b','a.gs_baseId=b.ba_id', 'LEFT'],
                ['db_base_goods c','a.gs_goodsId=c.bg_id', 'LEFT'],
            ];
            $goodsStock = model('GoodsStock')->getAllGoodsStockOne($field, $where, $join);
            !$goodsStock && $this->ajaxReturn(ajaxCallBack(300, '库存不存在'));

            $where    = array('ba_isDelete' => 0, 'ba_projectId' => session('user.platformId'));
            $field    = 'ba_id as id,ba_name as name';
            $baseList = model('Base')->getBaseList($field, $where);

            $this->assign('stock', $goodsStock);
            $this->assign('baseList', $baseList);
            $this->assign('id',       $id);
            return $this->fetch();
        }
    }

    public function operateDetail(){

        $id = Request::instance()->param('id', 0, 'intval');
        empty($id) && $this->ajaxReturn(array('state' => 'error', 'message' => '非法参数'));

        $where      = array('gs_isDelete' => 0, 'gs_projectId' => session('user.platformId'), 'gs_id' => $id);
        $field      = 'bg_id as id,bg_number as number,bg_name as name,bg_model as model,ba_name as baseName,gs_goodsStock as stock';
        $join       = [
            ['db_base b','a.gs_baseId=b.ba_id', 'LEFT'],
            ['db_base_goods c','a.gs_goodsId=c.bg_id', 'LEFT'],
        ];
        $goodsStock = model('GoodsStock')->getAllGoodsStockOne($field, $where, $join);
        !$goodsStock && $this->ajaxReturn(ajaxCallBack(300, '库存不存在'));

        $where      = array('od_goodsId' => $goodsStock['id'], 'od_stockId' => $id, 'od_projectId' => session('user.platformId'), 'od_isDelete' => 0);
        $join       = [
            ['db_user b','a.od_operateId=b.u_id', 'LEFT'],
            ['db_base c','a.od_purposeId=c.ba_id', 'LEFT'],
        ];

        $field      = 'od_id as id,od_createTime as createTime,od_type as type,od_number as number,od_purposeId as purposeId,u_name as name,ba_name as baseName,od_orderId as orderId';
        $details    = model('GoodsOperateDetail')->getOperateDetailAll($field, $where, $join, 'od_createTime desc');

        $this->assign('details', $details);
        $this->assign('stock', $goodsStock);
        return $this->fetch();
    }

    public function getgoodsmodel(){
        $goodsNumber = input('get.number', '', 'htmlspecialchars,strip_tags,trim');

        empty($goodsNumber) && $this->ajaxReturn(array('state' => 'error', 'message' => '非法参数'));

        $where = array('bg_number' => $goodsNumber);
        $model = model('BaseGoods')->getAllGoodsInfoList('bg_model', $where);

        !$model && $this->ajaxReturn(array('state' => 'error', 'message' => '该商品暂无型号'));

        $option = '<option value="请选择商品型号"></option>';
        foreach($model as $key => $val){
            $option .= "<option value='" . $val['bg_model'] . "'>" . $val['bg_model'] . "</option>";
        }
        $this->ajaxReturn(array('state' => 'ok', 'message' => $option));
    }


    public function edit(){

    }

    public function remove(){
        $input = input('');
        (!isset($input['id']) || empty($input['id'])) && $this->ajaxReturn(array('statusCode' => 300, 'message' => '非法参数'));
        $id    = $input['id'] + 0;

        $res   = $this->goodsModel->deleteBaseGoods($id);
        $res === false && $this->ajaxReturn(array('statusCode' => 300, 'message' => '删除失败'));

        $this->ajaxReturn(array('statusCode' => 200, 'message' => '删除成功'));
    }

    public function checkgoodsnumber(){
        $goodsNumber = input('post.goodsNumber', '', 'htmlspecialchars,strip_tags,trim');

        empty($goodsNumber) && $this->ajaxReturn(ajaxRemoteMessage('error',"请输入商品编号"));

        $res = model('BaseGoods')->getBaseGoodsOne('bg_id', ['bg_number' => $goodsNumber]);
        !$res && $this->ajaxReturn(ajaxRemoteMessage('error',"商品编号不存在"));

        return $this->ajaxReturn(ajaxRemoteMessage('ok', ''));
    }
}
